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
 * @property-read Lawn|null $lawn
 *
 * @method static \Database\Factories\LawnAeratingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating query()
 *
 * @property int $id
 * @property int $lawn_id
 * @property \Illuminate\Support\Carbon $aerated_on
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating whereAeratedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating whereLawnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnAerating whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class LawnAerating extends Model
{
    use CanGetTableNameStatically;
    use HasFactory;

    protected $table = 'lawn_aeratings';

    protected $fillable = [
        'lawn_id',
        'aerated_on',
        'notes',
    ];

    protected $casts = [
        'aerated_on' => 'date',
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