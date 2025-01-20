<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\CanGetTableNameStatically;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property int $lawn_id
 * @property \Illuminate\Support\Carbon $scarified_on
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LawnImage> $images
 * @property-read int|null $images_count
 * @property-read Lawn $lawn
 *
 * @method static \Database\Factories\LawnScarifyingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying whereLawnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying whereScarifiedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnScarifying whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class LawnScarifying extends Model
{
    use CanGetTableNameStatically;
    use HasFactory;

    protected $table = 'lawn_scarifyings';

    protected $fillable = [
        'lawn_id',
        'scarified_on',
        'notes',
    ];

    protected $casts = [
        'scarified_on' => 'date',
    ];

    public function lawn(): BelongsTo
    {
        return $this->belongsTo(Lawn::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(LawnImage::class, 'imageable');
    }
}
