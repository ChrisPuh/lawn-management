<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LawnImageType;
use App\Traits\CanGetTableNameStatically;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @template TModel of LawnAerating|LawnMowing|LawnScarifying|LawnFertilizing
 *
 * @property int $id
 * @property int $lawn_id
 * @property string|null $image_path
 * @property string $imageable_type
 * @property int $imageable_id
 * @property LawnImageType $type
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property \Illuminate\Support\Carbon|null $delete_after
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read TModel $imageable
 * @property-read Lawn $lawn
 *
 * @method static \Database\Factories\LawnImageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereArchivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereDeleteAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereImageableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereImageableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereLawnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class LawnImage extends Model
{
    use CanGetTableNameStatically;
    use HasFactory;

    protected $table = 'lawn_images';

    protected $fillable = [
        'lawn_id',
        'image_path',
        'imageable_id',
        'imageable_type',
        'type',
        'description',
        'archived_at',
        'delete_after',
    ];

    protected $casts = [
        'type' => LawnImageType::class,
        'archived_at' => 'datetime',
        'delete_after' => 'datetime',
    ];

    /**
     * Get the lawn that owns the image.
     *
     * @return BelongsTo<Lawn, LawnImage>
     */
    public function lawn(): BelongsTo
    {
        /** @var BelongsTo<Lawn, LawnImage> */
        return $this->belongsTo(Lawn::class);
    }

    /**
     * Get the parent imageable model.
     *
     * @return MorphTo<TModel, LawnImage>
     */
    public function imageable(): MorphTo
    {
        /** @var MorphTo<TModel, LawnImage> */
        return $this->morphTo();
    }
}
