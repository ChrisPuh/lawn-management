<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\CanGetTableNameStatically;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * 
 *
 * @property int $id
 * @property int $lawn_id
 * @property \Illuminate\Support\Carbon $mowed_on
 * @property string|null $cutting_height
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LawnImage> $images
 * @property-read int|null $images_count
 * @property-read \App\Models\Lawn $lawn
 * @method static \Database\Factories\LawnMowingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereCuttingHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereLawnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereMowedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
final class LawnMowing extends Model
{
    use CanGetTableNameStatically;
    use HasFactory;

    protected $fillable = [
        'lawn_id',
        'mowed_on',
        'cutting_height',
        'notes',
    ];

    protected $table = 'lawn_mowings';

    protected $casts = [
        'mowed_on' => 'date',
        'cutting_height' => 'string',
    ];

    /**
     * Get the lawn that owns the mowing record.
     *
     * @phpstan-return BelongsTo<Lawn, LawnMowing>
     */
    public function lawn(): BelongsTo
    {
        /** @var BelongsTo<Lawn, LawnMowing> */
        return $this->belongsTo(Lawn::class);
    }

    /**
     * Get the images for this mowing record.
     *
     * @return MorphMany<LawnImage, LawnMowing>
     */
    public function images(): MorphMany
    {
        /** @var MorphMany<LawnImage, LawnMowing> */
        return $this->morphMany(LawnImage::class, 'imageable');
    }
}
