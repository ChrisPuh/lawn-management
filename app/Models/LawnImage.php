<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LawnImageType;
use App\Traits\CanGetTableNameStatically;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property-read Model|Eloquent $imageable
 * @property-read Lawn|null $lawn
 *
 * @method static \Database\Factories\LawnImageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage query()
 *
 * @mixin \Eloquent
 *
 * @property int $id
 * @property int $lawn_id
 * @property string $image_path
 * @property string $imageable_type
 * @property int $imageable_id
 * @property string $type
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereImageableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereImageableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereLawnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnImage whereUpdatedAt($value)
 *
 * @mixin Eloquent
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
    ];

    protected $casts = [
        'type' => LawnImageType::class,
    ];


    public function lawn(): BelongsTo
    {
        return $this->belongsTo(Lawn::class);
    }

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
