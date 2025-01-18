<?php

declare(strict_types=1);
// tests/Unit/Models/LawnCareLogTest.php
use App\Enums\LawnCare\LawnCareType;
use App\Models\LawnCare;
use App\Models\LawnCareLog;
use App\Models\User;

describe('LawnCareLog Model', function () {
    beforeEach(function () {
        // Setup for each test
    });

    describe('attributes', function () {
        it('has fillable fields', function () {
            $log = new LawnCareLog;

            expect($log->getFillable())->toContain(
                'lawn_care_id',
                'user_id',
                'action',
                'data'
            );
        });

        it('casts attributes correctly', function () {
            $log = new LawnCareLog;

            expect($log->getCasts())
                ->toHaveKey('data', 'array');
        });

        it('uses timestamps', function () {
            $log = LawnCareLog::factory()->create();

            expect($log->created_at)
                ->toBeInstanceOf(Illuminate\Support\Carbon::class)
                ->and($log->updated_at)
                ->toBeInstanceOf(Illuminate\Support\Carbon::class);
        });
    });

    describe('relationships', function () {
        it('belongs to lawn care', function () {
            $lawnCare = LawnCare::factory()->create();
            $log = LawnCareLog::factory()
                ->for($lawnCare)
                ->create();

            expect($log->lawnCare)
                ->toBeInstanceOf(LawnCare::class)
                ->and($log->lawn_care_id)
                ->toBe($lawnCare->id);
        });

        it('belongs to user', function () {
            $user = User::factory()->create();
            $log = LawnCareLog::factory()
                ->for($user)
                ->create();

            expect($log->user)
                ->toBeInstanceOf(User::class)
                ->and($log->user_id)
                ->toBe($user->id);
        });
    });

    describe('scopes', function () {
        it('can scope to specific action', function () {
            $createdLog = LawnCareLog::factory()->created()->create();
            $updatedLog = LawnCareLog::factory()->updated()->create();

            $createdLogs = LawnCareLog::forAction('created')->get();

            expect($createdLogs)
                ->toHaveCount(1)
                ->first()->id->toBe($createdLog->id);
        });

        it('can scope to lawn care', function () {
            $lawnCare = LawnCare::factory()->create();
            $log = LawnCareLog::factory()
                ->forLawnCare($lawnCare)
                ->create();
            $otherLog = LawnCareLog::factory()->create();

            $lawnCareLogs = LawnCareLog::forLawnCare($lawnCare)->get();

            expect($lawnCareLogs)
                ->toHaveCount(1)
                ->first()->id->toBe($log->id);
        });

        it('can scope to user', function () {
            $user = User::factory()->create();
            $log = LawnCareLog::factory()
                ->for($user)
                ->create();
            $otherLog = LawnCareLog::factory()->create();

            $userLogs = LawnCareLog::forUser($user)->get();

            expect($userLogs)
                ->toHaveCount(1)
                ->first()->id->toBe($log->id);
        });

        it('can scope to date range', function () {
            $log = LawnCareLog::factory()->create([
                'created_at' => now()->subDays(5),
            ]);

            $otherLog = LawnCareLog::factory()->create([
                'created_at' => now()->subDays(15),
            ]);

            $recentLogs = LawnCareLog::createdBetween(
                now()->subDays(7),
                now()
            )->get();

            expect($recentLogs)
                ->toHaveCount(1)
                ->first()->id->toBe($log->id);
        });
    });

    describe('data access', function () {
        it('can get care type', function () {
            $log = LawnCareLog::factory()->create([
                'data' => [
                    'type' => LawnCareType::MOW->value, // MOW statt Mow
                    'care_data' => [],
                ],
            ]);

            expect($log->getCareType())
                ->toBeInstanceOf(LawnCareType::class)
                ->toBe(LawnCareType::MOW);
        });

        it('can get care data', function () {
            $careData = ['height_mm' => 45.5];
            $log = LawnCareLog::factory()->create([
                'data' => [
                    'type' => LawnCareType::MOW->value, // MOW statt Mow
                    'care_data' => $careData,
                ],
            ]);

            expect($log->getCareData())
                ->toBe($careData);
        });

        it('can get changes', function () {
            $changes = ['notes' => ['old' => 'test', 'new' => 'updated']];
            $log = LawnCareLog::factory()->updated($changes)->create();

            expect($log->getChanges())
                ->toBe($changes);
        });

        it('can get additional data', function () {
            $log = LawnCareLog::factory()->create([
                'data' => [
                    'custom_field' => 'test_value',
                ],
            ]);

            expect($log->getAdditionalData('custom_field'))
                ->toBe('test_value')
                ->and($log->getAdditionalData('non_existent', 'default'))
                ->toBe('default');
        });
    });

    describe('factory states', function () {
        it('can create log with created state', function () {
            $log = LawnCareLog::factory()->created()->create();

            expect($log->action)->toBe('created');
        });

        it('can create log with updated state', function () {
            $changes = ['field' => ['old' => 'test', 'new' => 'updated']];
            $log = LawnCareLog::factory()->updated($changes)->create();

            expect($log->action)->toBe('updated')
                ->and($log->getChanges())->toBe($changes);
        });

        it('can create log with completed state', function () {
            $log = LawnCareLog::factory()->completed()->create();

            expect($log->action)->toBe('completed')
                ->and($log->getAdditionalData('completed_at'))
                ->not->toBeNull();
        });

        it('can create log for specific lawn care', function () {
            $lawnCare = LawnCare::factory()->mowing()->create();
            $log = LawnCareLog::factory()
                ->forLawnCare($lawnCare)
                ->create();

            expect($log->lawn_care_id)->toBe($lawnCare->id)
                ->and($log->getCareType())->toBe(LawnCareType::MOW)
                ->and($log->getCareData())->toBe($lawnCare->care_data);
        });
    });
});
