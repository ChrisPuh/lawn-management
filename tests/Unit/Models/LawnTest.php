<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Models\Lawn;
use Tests\TestCase;

final class LawnTest extends TestCase
{
    public function test_fillable_attributes()
    {
        $lawn = new Lawn;

        $this->assertEquals([
            'name',
            'location',
            'size',
            'grass_seed',
            'type',
        ], $lawn->getFillable());
    }

    public function test_to_array()
    {
        $lawn = Lawn::factory()->create();

        $this->assertEquals([
            'id',
            'name',
            'location',
            'size',
            'grass_seed',
            'type',
            'created_at',
            'updated_at',
        ], array_keys($lawn->fresh()->toArray()));
    }

    public function test_table_name()
    {
        $lawn = new Lawn;

        $this->assertEquals('lawns', $lawn->getTable());
    }

    public function test_casts()
    {
        $lawn = new Lawn;

        $this->assertEquals([
            'grass_seed' => GrassSeed::class,
            'type' => GrassType::class,
            'id' => 'int',
        ], $lawn->getCasts());
    }
}
