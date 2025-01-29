<?php

declare(strict_types=1);

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Enums\LawnCare\LawnCareType;
use App\Enums\LawnImageType;
use App\Models\Lawn;
use App\Models\LawnCare;
use App\Models\LawnCareLog;
use App\Models\LawnImage;
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

        Schema::create(LawnCare::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawn_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', collect(LawnCareType::cases())->map->value->all());
            $table->json('care_data')->nullable();
            $table->text('notes')->nullable();
            $table->datetime('performed_at')->nullable();
            $table->datetime('scheduled_for')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['lawn_id', 'type']);
            $table->index(['scheduled_for', 'completed_at']);
        });

        Schema::create(LawnCareLog::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawn_care_id');
            $table->foreignId('user_id')->constrained();
            $table->string('action');
            $table->json('data')->nullable();
            $table->timestamps();

            $table->index(['lawn_care_id', 'action']);
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
        Schema::dropIfExists(LawnCareLog::getTableName());
        Schema::dropIfExists(LawnCare::getTableName());
        Schema::dropIfExists(Lawn::getTableName());
    }
};
