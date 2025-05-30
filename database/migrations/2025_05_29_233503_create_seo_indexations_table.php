<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seo_indexations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('website_id')->index();

            // Core fields
            $table->string('url')->index();
            $table->string('status')->nullable();
            $table->string('verdict')->nullable();
            $table->string('robots_state')->nullable();
            $table->string('indexing_state')->nullable();
            $table->string('page_fetch_state')->nullable();
            $table->string('crawled_as')->nullable();
            $table->timestamp('last_crawled_at')->nullable();

            // Canonicals
            $table->string('google_canonical')->nullable();
            $table->string('user_canonical')->nullable();

            // Extra metadata
            $table->json('referring_urls')->nullable(); // was text
            $table->string('mobile_usability_verdict')->nullable();
            $table->string('inspection_link')->nullable();

            $table->text('description')->nullable();

            $table->timestamps();

            $table->foreign('website_id')
                ->references('id')
                ->on('websites')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seo_indexations');
    }
};
