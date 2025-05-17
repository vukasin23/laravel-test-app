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
            // 1) Obriši postojeći foreign key na phase_id
            $table->dropForeign(['phase_id']);

            // 2) Obriši stari unique index na (phase_id, number)
            $table->dropUnique('phase_items_phase_id_number_unique');

            // 3) Dodaj novi unique index na (phase_id, number, date)
            $table->unique(['phase_id','number','date'], 'phase_items_phase_number_date_unique');

            // 4) Vrati foreign key na phase_id
            $table
                ->foreign('phase_id')
                ->references('id')
                ->on('phases')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('phase_items', function (Blueprint $table) {
            // Obriši novi index i FK
            $table->dropForeign(['phase_id']);
            $table->dropUnique('phase_items_phase_number_date_unique');

            // Vrati stari unique index
            $table->unique(['phase_id','number'], 'phase_items_phase_id_number_unique');

            // Vrati FK
            $table
                ->foreign('phase_id')
                ->references('id')
                ->on('phases')
                ->onDelete('cascade');
        });
    }
};
