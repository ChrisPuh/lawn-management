<?php

declare(strict_types=1);

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Models\Lawn;
use App\Models\LawnMowing;
use App\Models\User;
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

            $table->enum('grass_seed', collect(GrassSeed::cases())->map->value()->all())->nullable();
            $table->enum('type', collect(GrassType::cases())->map->value()->all())->nullable();

            // relations
            $table->foreignId('user_id')->references('id')->on(User::getTableName())->constrained()->cascadeOnDelete();

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
