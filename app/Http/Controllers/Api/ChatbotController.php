<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use App\Services\ChatbotKnowledgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle incoming chat queries from students using Google Gemini (gemini-2.5-flash).
     */
    public function chat(Request $request, ChatbotKnowledgeService $knowledgeService): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
            'history.*.role' => 'nullable|string',
            'history.*.text' => 'nullable|string',
            'history.*.content' => 'nullable|string',
        ]);

        $userMessage = trim($validated['message']);
        $apiKey = config('services.gemini.api_key');

        // Retrieve live database context
        $databaseContext = $knowledgeService->getDatabaseContext();

        // Contact numbers for fallbacks
        $content = SiteContent::get()->pluck('value', 'key');
        $admissionsPhone = $content->get('admissions_phone', '+92 301 1234567');
        $whatsapp = $content->get('college_whatsapp', '+92 300 1234567');

        // Check if API key is configured
        if (empty($apiKey)) {
            Log::info('Chatbot query received but GEMINI_API_KEY is not set in configuration.');
            return response()->json([
                'reply' => "Thank you for reaching out to Dir College of Nursing (DCN)! For detailed guidance on our programs, fees, and admissions, please contact our admissions office at {$admissionsPhone} or WhatsApp {$whatsapp}.",
            ]);
        }

        // Construct System Instruction incorporating live database records
        $systemInstruction = "You are the official AI Assistant for Dir College of Nursing (DCN). Use ONLY the following database records to accurately answer student inquiries about fees, programs, and admissions:\n\n"
            . $databaseContext . "\n\n"
            . "Guidelines:\n"
            . "1. Provide direct, helpful, and polite answers using only facts from the database above.\n"
            . "2. When asked about programs or fees, break down the numbers clearly using bullet points and mention currency (PKR).\n"
            . "3. If the answer is not contained in the database records, politely direct the student to contact admissions directly at {$admissionsPhone} or via WhatsApp at {$whatsapp}.\n"
            . "4. Keep replies concise, readable, and structured with clean formatting.";

        // Build conversation contents (incorporate recent history if present)
        $contents = [];
        $history = $validated['history'] ?? [];
        if (is_array($history)) {
            // Keep recent turns to prevent context overload
            $recent = array_slice($history, -8);
            foreach ($recent as $turn) {
                if (!is_array($turn)) {
                    continue;
                }
                $role = ($turn['role'] ?? '') === 'model' ? 'model' : 'user';
                $text = $turn['text'] ?? $turn['content'] ?? '';
                if (!empty($text)) {
                    $contents[] = [
                        'role' => $role,
                        'parts' => [
                            ['text' => (string) $text],
                        ],
                    ];
                }
            }
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage],
            ],
        ];

        $configuredModel = config('services.gemini.model', 'gemini-3.8-flash');
        $modelsToTry = array_values(array_unique(array_filter([
            $configuredModel,
            'gemini-3.8-flash',
            'gemini-2.5-flash',
            'gemini-2.0-flash',
            'gemini-1.5-flash',
        ])));

        $lastError = null;

        for ($i = 0; $i < count($modelsToTry); $i++) {
            $model = $modelsToTry[$i];
            try {
                $response = Http::timeout(30)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                        'system_instruction' => [
                            'parts' => [
                                ['text' => $systemInstruction],
                            ],
                        ],
                        'contents' => $contents,
                        'generationConfig' => [
                            'temperature' => 0.3,
                            'maxOutputTokens' => 800,
                        ],
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                    if (!empty($reply)) {
                        return response()->json([
                            'reply' => trim($reply),
                        ]);
                    }
                }

                $body = $response->body();
                // Check if Google's error response recommends a specific model
                if (preg_match('/models\/([a-zA-Z0-9\.\-_]+)/', $body, $matches)) {
                    $suggestedModel = $matches[1];
                    if (!in_array($suggestedModel, $modelsToTry)) {
                        $modelsToTry[] = $suggestedModel;
                    }
                }

                $lastError = [
                    'model' => $model,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ];

                // If not 404, don't keep cycling models
                if ($response->status() !== 404) {
                    break;
                }
            } catch (\Throwable $e) {
                $lastError = [
                    'model' => $model,
                    'exception' => $e->getMessage(),
                ];
            }
        }

        Log::error('Gemini API communication failed across candidate models', [
            'lastError' => $lastError,
        ]);

        return response()->json([
            'reply' => "Our admissions team can best assist with this inquiry. Please contact us directly at {$admissionsPhone} or WhatsApp at {$whatsapp}.",
        ]);
    }
}
