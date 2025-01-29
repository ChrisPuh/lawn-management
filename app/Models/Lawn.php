<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Traits\CanGetTableNameStatically;
use App\Traits\LawnCare\HasLawnCare;
use Auth;
use Database\Factories\LawnFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $location
 * @property string|null $size
 * @property GrassSeed|null $grass_seed
 * @property GrassType|null $type
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, LawnImage> $images
 * @property-read int|null $images_count
 * @property-read User $user
 *
 * @method static LawnFactory factory($count = null, $state = [])
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
 *
 * @property-read Collection<int, LawnCare> $lawnCares
 * @property-read int|null $lawn_cares_count
 *
 * @mixin Eloquent
 */
final class Lawn extends Model
{
    use CanGetTableNameStatically;

    /** @use HasFactory<LawnFactory> */
    use HasFactory;

    use HasLawnCare;

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
     * @return HasMany<LawnImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(LawnImage::class);
    }

    /**
     * @return HasMany<LawnCare, $this>
     */
    public function lawnCares(): HasMany
    {
        return $this->hasMany(LawnCare::class);
    }

    /**
     * Scope a query to only include lawns of the currently authenticated user.
     */
    public function scopeForUser(Builder $query): Builder
    {
        return $query->where('user_id', Auth::id());
    }
}
