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
        Schema::create('seo_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->onDelete('cascade');
            $table->string('keyword');
            $table->string('page_url');
            $table->string('country')->default('ALL');
            $table->string('device')->default('ALL');
            $table->date('date');
            $table->integer('clicks')->default(0);
            $table->integer('impressions')->default(0);
            $table->float('ctr')->default(0);
            $table->float('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_keywords');
    }
};
