<?php

declare(strict_types=1);

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Enums\LawnImageType;
use App\Models\Lawn;
use App\Models\LawnAerating;
use App\Models\LawnFertilizing;
use App\Models\LawnImage;
use App\Models\LawnMowing;
use App\Models\LawnScarifying;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keep existing Lawn table
        Schema::create(Lawn::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('size')->nullable();
            $table->enum('grass_seed', collect(GrassSeed::cases())->map->value()->all())->nullable();
            $table->enum('type', collect(GrassType::cases())->map->value()->all())->nullable();
            $table->foreignId('user_id')->references('id')->on(User::getTableName())->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Mowing history
        Schema::create(LawnMowing::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawn_id')->constrained()->cascadeOnDelete();
            $table->date('mowed_on');
            $table->string('cutting_height')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Fertilizing history
        Schema::create(LawnFertilizing::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawn_id')->constrained()->cascadeOnDelete();
            $table->date('fertilized_on');
            $table->string('fertilizer_name')->nullable();
            $table->decimal('quantity', 8, 2)->nullable();
            $table->string('quantity_unit')->default('kg');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Scarifying history
        Schema::create(LawnScarifying::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawn_id')->constrained()->cascadeOnDelete();
            $table->date('scarified_on');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Aerating history
        Schema::create(LawnAerating::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawn_id')->constrained()->cascadeOnDelete();
            $table->date('aerated_on');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Images for before/after comparisons
        Schema::create(LawnImage::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawn_id')->constrained()->cascadeOnDelete();
            $table->string('image_path')->nullable();
            $table->morphs('imageable'); // Polymorphic relation to link images to ifferent activities
            $table->enum('type', collect(LawnImageType::cases())->map->value->all());
            $table->text('description')->nullable();

            $table->timestamp('archived_at')->nullable();
            $table->timestamp('delete_after')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(LawnImage::getTableName());
        Schema::dropIfExists(LawnAerating::getTableName());
        Schema::dropIfExists(LawnScarifying::getTableName());
        Schema::dropIfExists(LawnFertilizing::getTableName());
        Schema::dropIfExists(LawnMowing::getTableName());
        Schema::dropIfExists(Lawn::getTableName());
    }
};
