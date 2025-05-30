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
            $table->string('source')->nullable()->after('country');
            $table->date('date')->nullable()->after('source');
        });
    }

    public function down()
    {
        Schema::table('page_views', function (Blueprint $table) {
            $table->dropColumn(['source', 'date']);
        });
    }
};
