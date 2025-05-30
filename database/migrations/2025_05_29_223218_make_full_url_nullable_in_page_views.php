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
            $table->string('full_url')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('page_views', function (Blueprint $table) {
            $table->string('full_url')->nullable(false)->change();
        });
    }
};
