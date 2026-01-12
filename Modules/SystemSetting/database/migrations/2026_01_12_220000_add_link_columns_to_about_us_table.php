<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('about_us', function (Blueprint $table) {
            if (!Schema::hasColumn('about_us', 'section_1_link')) {
                $table->string('section_1_link')->nullable()->after('section_1_sub_section_2_icon');
            }
            if (!Schema::hasColumn('about_us', 'section_2_video_link')) {
                $table->string('section_2_video_link')->nullable()->after('section_2_sub_section_2_icon');
            }
        });
    }

    public function down(): void
    {
        Schema::table('about_us', function (Blueprint $table) {
            if (Schema::hasColumn('about_us', 'section_1_link')) {
                $table->dropColumn('section_1_link');
            }
            if (Schema::hasColumn('about_us', 'section_2_video_link')) {
                $table->dropColumn('section_2_video_link');
            }
        });
    }
};
