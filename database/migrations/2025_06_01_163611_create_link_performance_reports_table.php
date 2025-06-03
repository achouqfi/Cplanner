<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinkPerformanceReportsTable extends Migration
{
    public function up()
    {
        Schema::create('link_performance_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('website_id')->nullable()->constrained()->onDelete('set null');

            $table->string('url');
            $table->string('domain');
            $table->string('ip_address');
            $table->string('location')->nullable();

            $table->float('load_time_seconds');
            $table->integer('content_length');
            $table->float('readability_score');
            $table->float('performance_score');
            $table->float('structure_score');
            $table->char('gtmetrix_grade', 1);

            $table->json('web_vitals');
            $table->json('html_elements');
            $table->json('technologies');
            $table->json('monetization');
            $table->json('json_ld_schemas');
            $table->json('recommendations')->nullable();
            $table->longText('langchain_summary')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('link_performance_reports');
    }
}
