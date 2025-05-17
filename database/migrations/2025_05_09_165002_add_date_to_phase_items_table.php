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
        Schema::table('phase_items', function (Blueprint $table) {
            $table->date('date')->nullable()->after('number')->index();

        });
    }

    public function down()
    {
        Schema::table('phase_items', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
};
