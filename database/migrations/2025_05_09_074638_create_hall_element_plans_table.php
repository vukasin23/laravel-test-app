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
        Schema::create('hall_element_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained('halls')->cascadeOnDelete();
            $table->date('date')->index();
            $table->integer('planned_count')->default(0);
            $table->foreignId('entered_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            // jedinstven zapis po hali i datumu
            $table->unique(['hall_id','date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hall_element_plans');
    }
};
