<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->decimal('rating', 3, 1)->nullable()->after('release');
            $table->string('duration')->nullable()->after('rating');
            $table->enum('video_source', ['upload', 'link'])->default('upload')->after('duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn(['rating', 'duration', 'video_source']);
        });
    }
};
