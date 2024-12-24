<?php

declare(strict_types=1);

use App\Models\Lawn;
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Lawn::getTableName());
    }
};
