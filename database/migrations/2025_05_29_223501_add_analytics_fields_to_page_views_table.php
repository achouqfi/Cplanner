<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up()
    {
        Schema::table('page_views', function (Blueprint $table) {
            $table->unsignedBigInteger('pageviews')->default(0);
            $table->unsignedBigInteger('sessions')->default(0);
            $table->unsignedBigInteger('users')->default(0);
            $table->float('bounce_rate')->default(0);
            $table->unsignedBigInteger('engagement_time')->default(0); // userEngagementDuration
            $table->unsignedBigInteger('events')->default(0); // eventCount
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_views', function (Blueprint $table) {
            //
        });
    }
};
