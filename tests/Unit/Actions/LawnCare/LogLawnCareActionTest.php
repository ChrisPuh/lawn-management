<?php

declare(strict_types=1);
// tests/Unit/Actions/LawnCare/LogLawnCareActionTest.php

use App\Actions\LawnCare\LogLawnCareAction;
use App\Enums\LawnCare\LawnCareType;
use App\Models\LawnCare;
use App\Models\LawnCareLog;
use App\Models\User;

describe('LogLawnCare Action', function (): void {
    beforeEach(function (): void {
        // Setup for each test
    });

    describe('logging', function (): void {
        it('creates a basic log entry', function (): void {
            $lawnCare = LawnCare::factory()->mowing()->create();
            $user = User::factory()->create();

            $action = app(LogLawnCareAction::class);

            $log = $action->execute(
                lawn_care: $lawnCare,
                action: 'created',
                user_id: $user->id,
            );

            expect($log)
                ->toBeInstanceOf(LawnCareLog::class)
                ->and($log->lawn_care_id)->toBe($lawnCare->id)
                ->and($log->user_id)->toBe($user->id)
                ->and($log->action)->toBe('created')
                ->and($log->data)
                ->toHaveKey('type', LawnCareType::MOW->value)
                ->toHaveKey('care_data');
        });

        it('includes additional data in log', function (): void {
            $lawnCare = LawnCare::factory()->mowing()->create();
            $user = User::factory()->create();

            $additionalData = [
                'completed_at' => now()->toDateTimeString(),
                'notes' => 'Test note',
            ];

            $action = app(LogLawnCareAction::class);

            $log = $action->execute(
                lawn_care: $lawnCare,
                action: 'completed',
                user_id: $user->id,
                additional_data: $additionalData
            );

            expect($log->data)
                ->toHaveKey('completed_at')
                ->toHaveKey('notes')
                ->and($log->data['notes'])->toBe('Test note');
        });
    });

    describe('relationships', function (): void {
        it('belongs to lawn care', function (): void {
            $lawnCare = LawnCare::factory()->create();
            $user = User::factory()->create();

            $action = app(LogLawnCareAction::class);
            $log = $action->execute(
                lawn_care: $lawnCare,
                action: 'created',
                user_id: $user->id,
            );

            expect($log->lawnCare)
                ->toBeInstanceOf(LawnCare::class)
                ->and($log->lawnCare->id)->toBe($lawnCare->id);
        });

        it('belongs to user', function (): void {
            $lawnCare = LawnCare::factory()->create();
            $user = User::factory()->create();

            $action = app(LogLawnCareAction::class);
            $log = $action->execute(
                lawn_care: $lawnCare,
                action: 'created',
                user_id: $user->id,
            );

            expect($log->user)
                ->toBeInstanceOf(User::class)
                ->and($log->user->id)->toBe($user->id);
        });
    });

    describe('querying', function (): void {
        it('can retrieve logs by lawn care', function (): void {
            $lawnCare = LawnCare::factory()->create();
            $user = User::factory()->create();

            $action = app(LogLawnCareAction::class);

            // Create multiple logs
            $action->execute(
                lawn_care: $lawnCare,
                action: 'created',
                user_id: $user->id,
            );

            $action->execute(
                lawn_care: $lawnCare,
                action: 'updated',
                user_id: $user->id,
                additional_data: ['changes' => ['notes' => 'Updated']]
            );

            expect(LawnCareLog::where('lawn_care_id', $lawnCare->id)->count())
                ->toBe(2);
        });

        it('can retrieve logs by action type', function (): void {
            $lawnCare = LawnCare::factory()->create();
            $user = User::factory()->create();

            $action = app(LogLawnCareAction::class);

            $action->execute(
                lawn_care: $lawnCare,
                action: 'created',
                user_id: $user->id,
            );

            $action->execute(
                lawn_care: $lawnCare,
                action: 'updated',
                user_id: $user->id,
            );

            expect(LawnCareLog::where('action', 'created')->count())->toBe(1)
                ->and(LawnCareLog::where('action', 'updated')->count())->toBe(1);
        });
    });

    describe('validations', function (): void {
        it('requires valid lawn care instance', function (): void {
            $invalidLawnCare = new LawnCare;
            $user = User::factory()->create();

            $action = app(LogLawnCareAction::class);

            expect(fn () => $action->execute(
                lawn_care: $invalidLawnCare,
                action: 'created',
                user_id: $user->id,
            ))->toThrow(Exception::class);
        });

        it('handles invalid action types', function (): void {
            $lawnCare = LawnCare::factory()->create();
            $user = User::factory()->create();

            $action = app(LogLawnCareAction::class);
            $log = $action->execute(
                lawn_care: $lawnCare,
                action: 'invalid_action',
                user_id: $user->id,
            );

            expect($log->action)->toBe('invalid_action')
                ->and($log->exists)->toBeTrue();
        });
    });
});
