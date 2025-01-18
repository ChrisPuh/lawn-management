<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\CanGetTableNameStatically;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LawnImage> $images
 * @property-read int|null $images_count
 * @property-read Lawn $lawn
 *
 * @method static \Database\Factories\LawnFertilizingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnFertilizing query()
 *
 * @property int $id
 * @property int $lawn_id
 * @property \Illuminate\Support\Carbon|null $fertilized_on
 * @property string|null $fertilizer_name
 * @property string|null $quantity
 * @property string|null $quantity_unit
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
final class LawnFertilizing extends Model
{
    use CanGetTableNameStatically;
    use HasFactory;

    protected $table = 'lawn_fertilizings';

    protected $fillable = [
        'lawn_id',
        'fertilized_on',
        'fertilizer_name',
        'quantity',
        'quantity_unit',
        'notes',
    ];

    protected $casts = [
        'fertilized_on' => 'date',
        'quantity' => 'decimal:2',
    ];

    /**
     * Get the lawn that owns the fertilizing record.
     *
     * @return BelongsTo<Lawn, LawnFertilizing>
     */
    public function lawn(): BelongsTo
    {
        /** @var BelongsTo<Lawn, LawnFertilizing> */
        return $this->belongsTo(Lawn::class);
    }

    /**
     * Get the images for this fertilizing record.
     *
     * @return MorphMany<LawnImage, LawnFertilizing>
     */
    public function images(): MorphMany
    {
        /** @var MorphMany<LawnImage, LawnFertilizing> */
        return $this->morphMany(LawnImage::class, 'imageable');
    }
}
