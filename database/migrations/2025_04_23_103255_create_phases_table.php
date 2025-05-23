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
        Schema::create('phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained('halls')->onDelete('cascade');
            $table->string('name'); // npr. Uithalen, Kozijnen...
            $table->integer('order')->default(0); // redosled faza u prikazu
            $table->integer('aantal'); // koliko stavki ima faza
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phases');
    }
};
