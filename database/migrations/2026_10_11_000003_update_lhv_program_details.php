<?php

use App\Models\Program;
use App\Models\ProgramFee;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $lhv = Program::where('name', 'LHV')->orWhere('slug', 'lhv')->first();
        if ($lhv) {
            $lhv->update([
                'duration' => '2 Years',
                'description' => 'Essential training for maternal and child healthcare, community midwifery, and primary healthcare services.',
            ]);

            // Ensure standard indicative fees for LHV
            ProgramFee::firstOrCreate(
                ['program_id' => $lhv->id, 'fee_type' => 'Admission Fee'],
                ['amount' => 20000, 'currency' => 'PKR', 'billing_basis' => 'one time', 'is_active' => true, 'sort_order' => 0]
            );
            ProgramFee::firstOrCreate(
                ['program_id' => $lhv->id, 'fee_type' => 'Tuition Fee'],
                ['amount' => 120000, 'currency' => 'PKR', 'billing_basis' => 'per year', 'is_active' => true, 'sort_order' => 1]
            );
            ProgramFee::firstOrCreate(
                ['program_id' => $lhv->id, 'fee_type' => 'Clinical / Laboratory Fee'],
                ['amount' => 15000, 'currency' => 'PKR', 'billing_basis' => 'per year', 'is_active' => true, 'sort_order' => 2]
            );
        }
    }

    public function down(): void
    {
        // No rollback needed
    }
};

