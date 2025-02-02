<?php

declare(strict_types=1);

use App\Enums\WaitlistStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(App\Models\WaitList::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->text('reason')->nullable();
            $table->enum('status', array_column(WaitlistStatus::cases(), 'value'))->default(WaitlistStatus::Pending->value);
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('declined_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(App\Models\WaitList::getTableName());
    }
};
