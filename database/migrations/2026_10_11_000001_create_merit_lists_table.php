<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merit_lists', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('category')->default('merit_list')->index(); // merit_list, prospectus, roll_number_slip, form, academic_calendar
            $t->string('session_year')->nullable()->index(); // e.g. 2026-2027
            $t->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $t->string('file_path');
            $t->string('file_size')->nullable();
            $t->text('description')->nullable();
            $t->unsignedSmallInteger('sort_order')->default(0)->index();
            $t->boolean('is_published')->default(true)->index();
            $t->date('published_at')->nullable()->index();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merit_lists');
    }
};

