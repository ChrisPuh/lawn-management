<?php

declare(strict_types=1);

namespace Unit\Models;

use App\Models\LawnMowing;
use Tests\TestCase;

final class LawnMowingTest extends TestCase
{
    public function test_fillable_attributes()
    {
        $lawnmMowing = new LawnMowing;

        $this->assertEquals([
            'lawn_id',
            'mowed_on',
            'cutting_height',
        ], $lawnmMowing->getFillable());
    }

    public function test_table_name()
    {
        $lawnmMowing = new LawnMowing;

        $this->assertEquals('lawn_mowings', $lawnmMowing->getTable());
    }

    public function test_lawn_relationship()
    {
        $lawnmMowing = new LawnMowing;

        $this->assertEquals('lawn_id', $lawnmMowing->lawn()->getForeignKeyName());
        $this->assertEquals('id', $lawnmMowing->lawn()->getOwnerKeyName());
    }
}
