<?php
// tests/Unit/DataObjects/LawnCare/CreateMowingDataTest.php

declare(strict_types=1);

use App\DataObjects\LawnCare\CreateMowingData;
use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\MowingPattern;

describe('CreateMowingData', function (): void {
    it('can be instantiated with minimal data', function (): void {
        $data = new CreateMowingData(
            lawn_id: 1,
            user_id: 1,
            height_mm: 45.5,
            collected: true,
        );

        expect($data)
            ->lawn_id->toBe(1)
            ->user_id->toBe(1)
            ->height_mm->toBe(45.5)
            ->pattern->toBeNull()
            ->collected->toBeTrue()
            ->blade_condition->toBeNull()
            ->duration_minutes->toBeNull()
            ->notes->toBeNull()
            ->performed_at->toBeNull()
            ->scheduled_for->toBeNull();
    });

    it('can be instantiated with all data', function (): void {
        $performedAt = new DateTime();
        $scheduledFor = new DateTime('+1 day');

        $data = new CreateMowingData(
            lawn_id: 1,
            user_id: 1,
            height_mm: 45.5,
            pattern: MowingPattern::DIAGONAL,
            collected: true,
            blade_condition: BladeCondition::SHARP,
            duration_minutes: 30,
            notes: 'Test notes',
            performed_at: $performedAt,
            scheduled_for: $scheduledFor,
        );

        expect($data)
            ->lawn_id->toBe(1)
            ->user_id->toBe(1)
            ->height_mm->toBe(45.5)
            ->pattern->toBe(MowingPattern::DIAGONAL)
            ->collected->toBeTrue()
            ->blade_condition->toBe(BladeCondition::SHARP)
            ->duration_minutes->toBe(30)
            ->notes->toBe('Test notes')
            ->performed_at->toBe($performedAt)
            ->scheduled_for->toBe($scheduledFor);
    });

    it('inherits base lawn care data properties', function (): void {
        $data = new CreateMowingData(
            lawn_id: 1,
            user_id: 1,
            height_mm: 45.5,
            notes: 'Test notes'
        );

        expect($data)
            ->lawn_id->toBe(1)
            ->user_id->toBe(1)
            ->notes->toBe('Test notes');
    });

    it('creates from array', function (): void {
        $validatedData = [
            'lawn_id' => 1,
            'height_mm' => '45.5',
            'pattern' => MowingPattern::DIAGONAL->value,
            'collected' => true,
            'blade_condition' => BladeCondition::SHARP->value,
            'duration_minutes' => '30',
            'notes' => 'Test notes',
            'performed_at' => '2024-01-19 10:00:00',
            'scheduled_for' => '2024-01-20 10:00:00',
        ];

        $data = CreateMowingData::fromArray($validatedData, 1);

        expect($data)
            ->toBeInstanceOf(CreateMowingData::class)
            ->lawn_id->toBe(1)
            ->user_id->toBe(1)
            ->height_mm->toBe(45.5)
            ->pattern->toBe(MowingPattern::DIAGONAL)
            ->collected->toBeTrue()
            ->blade_condition->toBe(BladeCondition::SHARP)
            ->duration_minutes->toBe(30)
            ->notes->toBe('Test notes')
            ->performed_at->toBeInstanceOf(DateTime::class)
            ->scheduled_for->toBeInstanceOf(DateTime::class);
    });

    it('handles optional fields from array', function (): void {
        $validatedData = [
            'lawn_id' => 1,
            'height_mm' => '45.5',
            'collected' => true,
        ];

        $data = CreateMowingData::fromArray($validatedData, 1);
        expect($data)
            ->pattern->toBeNull()
            ->blade_condition->toBeNull()
            ->duration_minutes->toBeNull()
            ->notes->toBeNull()
            ->performed_at->toBeNull()
            ->scheduled_for->toBeNull();
    });
});
