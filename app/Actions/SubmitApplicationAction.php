<?php

namespace App\Actions;

use App\Http\Requests\StoreApplicationRequest;
use App\Models\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubmitApplicationAction
{
    /**
     * Store application documents and persist application data within a transaction.
     */
    public function execute(StoreApplicationRequest $request): Application
    {
        return DB::transaction(function () use ($request) {
            $data = $request->safe()->except(['transcript', 'identity_document', 'confirmation']);
            $data['reference'] = self::generateReference();
            $data['transcript_path'] = $request->file('transcript')->store('applications/transcripts', 'local');
            $data['identity_document_path'] = $request->file('identity_document')->store('applications/identity', 'local');
            $data['submitted_at'] = now();

            return Application::create($data);
        });
    }

    /**
     * Generate an authentic, human-readable reference number (e.g. DCN-2026-10482).
     */
    public static function generateReference(): string
    {
        $year = date('Y');
        do {
            $num = mt_rand(10001, 99999);
            $ref = "DCN-{$year}-{$num}";
        } while (Application::where('reference', $ref)->exists());

        return $ref;
    }
}
