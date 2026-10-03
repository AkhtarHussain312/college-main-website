<?php

namespace Tests\Feature;

use App\Models\AdmissionMilestone;
use App\Models\Program;
use App\Models\ProgramFee;
use App\Models\SiteContent;
use App\Services\ChatbotKnowledgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatbotApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_endpoint_validates_required_message(): void
    {
        $response = $this->postJson('/api/chat', []);
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['message']);
    }

    public function test_chat_endpoint_validates_message_max_length(): void
    {
        $response = $this->postJson('/api/chat', [
            'message' => str_repeat('a', 1001),
        ]);
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['message']);
    }

    public function test_chat_endpoint_returns_graceful_reply_when_api_key_is_missing(): void
    {
        Config::set('services.gemini.api_key', null);

        SiteContent::create([
            'group' => 'contact page',
            'key' => 'admissions_phone',
            'label' => 'Admissions phone',
            'value' => '+92 301 9999999',
            'type' => 'text',
            'sort_order' => 1,
        ]);

        $response = $this->postJson('/api/chat', [
            'message' => 'What programs do you offer?',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['reply']);

        $this->assertStringContainsString('+92 301 9999999', $response->json('reply'));
    }

    public function test_chat_endpoint_calls_gemini_api_and_returns_reply(): void
    {
        Config::set('services.gemini.api_key', 'mock-gemini-key');

        $program = Program::create([
            'name' => 'BS Nursing (Generic)',
            'slug' => 'bs-nursing-generic',
            'duration' => '4 Years',
            'description' => 'Comprehensive 4-year undergraduate nursing degree.',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        ProgramFee::create([
            'program_id' => $program->id,
            'fee_type' => 'Tuition Fee',
            'amount' => 150000,
            'currency' => 'PKR',
            'billing_basis' => 'per year',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'We offer the BS Nursing (Generic) program which is 4 Years with an annual tuition of PKR 150,000.'],
                            ],
                            'role' => 'model',
                        ],
                        'finishReason' => 'STOP',
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson('/api/chat', [
            'message' => 'What is the fee for BS Nursing?',
            'history' => [
                ['role' => 'user', 'text' => 'Hi'],
                ['role' => 'model', 'text' => 'Hello! How can I help you?'],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('reply', 'We offer the BS Nursing (Generic) program which is 4 Years with an annual tuition of PKR 150,000.');

        Http::assertSent(function ($request) {
            $data = $request->data();
            return (str_contains($request->url(), 'gemini-3.8-flash') || str_contains($request->url(), 'gemini-2.5-flash'))
                && str_contains($request->url(), 'key=mock-gemini-key')
                && isset($data['system_instruction'])
                && isset($data['generationConfig']['temperature'])
                && $data['generationConfig']['temperature'] == 0.3
                && count($data['contents']) >= 2;
        });
    }

    public function test_chat_endpoint_handles_gemini_api_failure_gracefully(): void
    {
        Config::set('services.gemini.api_key', 'mock-gemini-key');

        SiteContent::create([
            'group' => 'contact page',
            'key' => 'admissions_phone',
            'label' => 'Admissions phone',
            'value' => '+92 301 1122334',
            'type' => 'text',
            'sort_order' => 1,
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'error' => [
                    'message' => 'Service Unavailable',
                    'code' => 503,
                ],
            ], 503),
        ]);

        $response = $this->postJson('/api/chat', [
            'message' => 'Tell me about scholarships',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['reply']);

        $this->assertStringContainsString('+92 301 1122334', $response->json('reply'));
    }

    public function test_chatbot_knowledge_service_builds_complete_context(): void
    {
        SiteContent::create([
            'group' => 'college details',
            'key' => 'college_name',
            'label' => 'College name',
            'value' => 'Dir College of Nursing & Allied Health Science',
            'type' => 'text',
            'sort_order' => 1,
        ]);

        $program = Program::create([
            'name' => 'Diploma in Nursing',
            'slug' => 'diploma-in-nursing',
            'duration' => '3 Years',
            'description' => 'Clinical diploma program.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        ProgramFee::create([
            'program_id' => $program->id,
            'fee_type' => 'Admission Fee',
            'amount' => 30000,
            'currency' => 'PKR',
            'billing_basis' => 'one time',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        AdmissionMilestone::create([
            'title' => 'Submit Application Form',
            'sort_order' => 0,
        ]);

        $service = new ChatbotKnowledgeService();
        $context = $service->getDatabaseContext();

        $this->assertStringContainsString('Dir College of Nursing & Allied Health Science', $context);
        $this->assertStringContainsString('Diploma in Nursing', $context);
        $this->assertStringContainsString('30,000', $context);
        $this->assertStringContainsString('Submit Application Form', $context);
    }
}
