<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Traits\CanGetTableNameStatically;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $location
 * @property string|null $size
 * @property GrassSeed|null $grass_seed
 * @property GrassType|null $type
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LawnAerating> $aeratingRecords
 * @property-read int|null $aerating_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LawnFertilizing> $fertilizingRecords
 * @property-read int|null $fertilizing_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LawnImage> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LawnMowing> $mowingRecords
 * @property-read int|null $mowing_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LawnScarifying> $scarifyingRecords
 * @property-read int|null $scarifying_records_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\LawnFactory factory($count = null, $state = [])
 * @method static Builder<static>|Lawn forUser()
 * @method static Builder<static>|Lawn newModelQuery()
 * @method static Builder<static>|Lawn newQuery()
 * @method static Builder<static>|Lawn query()
 * @method static Builder<static>|Lawn whereCreatedAt($value)
 * @method static Builder<static>|Lawn whereGrassSeed($value)
 * @method static Builder<static>|Lawn whereId($value)
 * @method static Builder<static>|Lawn whereLocation($value)
 * @method static Builder<static>|Lawn whereName($value)
 * @method static Builder<static>|Lawn whereSize($value)
 * @method static Builder<static>|Lawn whereType($value)
 * @method static Builder<static>|Lawn whereUpdatedAt($value)
 * @method static Builder<static>|Lawn whereUserId($value)
 * @mixin \Eloquent
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
        'user_id',
        'type',
    ];

    protected $casts = [
        'grass_seed' => GrassSeed::class,
        'type' => GrassType::class,
    ];

    // relations
    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<LawnMowing, Lawn>
     */
    public function mowingRecords(): HasMany
    {
        return $this->hasMany(LawnMowing::class);
    }

    /**
     * @return HasMany<LawnFertilizing, Lawn>
     */
    public function fertilizingRecords(): HasMany
    {
        return $this->hasMany(LawnFertilizing::class);
    }

    /**
     * @return HasMany<LawnScarifying, Lawn>
     */
    public function scarifyingRecords(): HasMany
    {
        return $this->hasMany(LawnScarifying::class);
    }

    /**
     * @return HasMany<LawnAerating, Lawn>
     */
    public function aeratingRecords(): HasMany
    {
        return $this->hasMany(LawnAerating::class);
    }

    /**
     * @return HasMany<LawnImage, Lawn>
     */
    public function images(): HasMany
    {
        return $this->hasMany(LawnImage::class);
    }

    /**
     * Gets the last mowing date formatted as a string
     *
     * @param  string  $format  (optional) Format for the date, default is 'd.m.Y'
     */
    public function getLastMowingDate(string $format = 'd.m.Y'): ?string
    {
        /** @var LawnMowing|null $lastMowing */
        $lastMowing = $this->mowingRecords()->latest('mowed_on')->first();

        return $lastMowing?->mowed_on?->format($format);
    }

    /**
     * Gets the last fertilizing date formatted as a string
     *
     * @param  string  $format  (optional) Format for the date, default is 'd.m.Y'
     */
    public function getLastFertilizingDate(string $format = 'd.m.Y'): ?string
    {
        /** @var LawnFertilizing|null $lastFertilizing */
        $lastFertilizing = $this->fertilizingRecords()->latest('fertilized_on')->first();

        return $lastFertilizing?->fertilized_on?->format($format);
    }

    /**
     * Gets the last scarifying date formatted as a string
     *
     * @param  string  $format  (optional) Format for the date, default is 'd.m.Y'
     */
    public function getLastScarifyingDate(string $format = 'd.m.Y'): ?string
    {
        /** @var LawnScarifying|null $lastScarifying */
        $lastScarifying = $this->scarifyingRecords()->latest('scarified_on')->first();

        return $lastScarifying?->scarified_on?->format($format);
    }

    /**
     * Gets the last aerating date formatted as a string
     *
     * @param  string  $format  (optional) Format for the date, default is 'd.m.Y'
     */
    public function getLastAeratingDate(string $format = 'd.m.Y'): ?string
    {
        /** @var LawnAerating|null $lastAerating */
        $lastAerating = $this->aeratingRecords()->latest('aerated_on')->first();

        return $lastAerating?->aerated_on?->format($format);
    }

    /**
     * Scope a query to only include lawns of the currently authenticated user.
     */
    public function scopeForUser(Builder $query): Builder
    {
        return $query->where('user_id', Auth::id());
    }
}
