<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GrassSeed;
use App\Enums\GrassType;
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
        'grass_seed' => GrassSeed::class,
        'type' => GrassType::class,
    ];

    public function mowingRecords()
    {
        return $this->hasMany(LawnMowing::class);
    }

    /**
     * Gibt das letzte Mähdatum zurück, formatiert als String
     *
     * @param  string  $format  (optional) Format für das Datum, Standard ist 'd.m.Y'
     */
    public function getLastMowingDate(string $format = 'd.m.Y'): ?string
    {
        $lastMowing = $this->mowingRecords()->latest('mowed_on')->first();

        return $lastMowing?->mowed_on->format($format);
    }
}
