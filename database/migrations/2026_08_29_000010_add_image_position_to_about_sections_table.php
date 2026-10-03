<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('about_sections', 'image_position')) {
            Schema::table('about_sections', function (Blueprint $table) {
                $table->string('image_position', 12)->default('left')->after('image_path');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('about_sections', 'image_position')) {
            Schema::table('about_sections', function (Blueprint $table) {
                $table->dropColumn('image_position');
            });
        }
    }
};
