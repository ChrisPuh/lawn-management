<?php

declare(strict_types=1);

use App\Models\Lawn;
use App\Models\LawnMowing;
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
        Schema::create(Lawn::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable(); //
            $table->string('size')->nullable();

            //seedtype
            $table->string('grass_seed')->nullable(); //

            $table->string('type')->nullable(); //e.g. sport, garden, park
            $table->timestamps();
        });


        Schema::create(LawnMowing::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawn_id')->constrained()->cascadeOnDelete();
            $table->date('mowed_on'); // Datum des Mähens
            $table->string('cutting_height')->nullable(); // Schnitthöhe (optional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(LawnMowing::getTableName());
        Schema::dropIfExists(Lawn::getTableName());
    }
};
