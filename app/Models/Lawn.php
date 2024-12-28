<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Traits\CanGetTableNameStatically;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string|null $location
 * @property string|null $size
 * @property GrassSeed|null $grass_seed
 * @property GrassType|null $type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Collection<int, LawnMowing> $mowingRecords
 *
 * @method static \Illuminate\Database\Eloquent\Builder|static query()
 * @method static static make(array $attributes = [])
 * @method static static create(array $attributes = [])
 * @method static static forceCreate(array $attributes)
 * @method HasMany hasMany(string $related, string|null $foreignKey = null, string|null $localKey = null)
 */
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

    /**
     * @return HasMany<LawnMowing, Lawn>
     */
    public function mowingRecords(): HasMany
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

        return $lastMowing?->mowed_on?->format($format);
    }
}
