<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('page_views_session', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('session_id');
            $table->uuid('visitor_id');

            $table->text('page_url');
            $table->text('page_title')->nullable();
            $table->timestamp('timestamp')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->integer('load_time_ms')->nullable();
            $table->integer('screen_width')->nullable();
            $table->integer('screen_height')->nullable();

            $table->string('html_hash', 255)->nullable()->index();

            $table->json('clicks')->nullable();
            $table->json('screenshot_paths')->nullable();
            $table->json('html_versions')->nullable();

            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            $table->foreign('visitor_id')->references('id')->on('visitors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
