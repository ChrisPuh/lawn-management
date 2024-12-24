<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\CanGetTableNameStatically;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Lawn extends Model
{
    use CanGetTableNameStatically;

    /** @use HasFactory<\Database\Factories\LawnFactory> */
    use HasFactory;

    protected $table = 'lawns';

    protected $fillable = [
        'name',
        'location',
        'size',
        'grass_seed',
        'type',
    ];

    protected $casts = [
        'grass_seed' => \App\Enums\GrassSeed::class,
        'type' => \App\Enums\GrassType::class,
    ];
}
