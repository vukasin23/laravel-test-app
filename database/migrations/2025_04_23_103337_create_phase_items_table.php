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
        Schema::create('phase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phase_id')->constrained('phases')->onDelete('cascade');
            $table->integer('number'); // redni broj od 1 do aantal
            $table->boolean('is_done')->default(false);

            $table->unique(['phase_id', 'number']); // sprečava duplikate
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phase_items');
    }
};
