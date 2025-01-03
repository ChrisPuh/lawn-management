<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\CanGetTableNameStatically;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $lawn_id
 * @property \Carbon\Carbon $mowed_on
 * @property string|null $cutting_height
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Lawn $lawn
 *
 * @method static \Illuminate\Database\Eloquent\Builder|static query()
 * @method static \Database\Factories\LawnMowingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereCuttingHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereLawnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereMowedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereUpdatedAt($value)
 *
 * @property string|null $notes
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawnMowing whereNotes($value)
 *
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
    ];

    protected $table = 'lawn_mowings';

    protected $casts = [
        'mowed_on' => 'date',          // Datum wird in ein Carbon-Objekt umgewandelt
        'cutting_height' => 'string',  // Sicherstellen, dass cutting_height als String behandelt wird
    ];

    /**
     * Get the lawn that owns the mowing record.
     *
     * @return BelongsTo<Lawn, $this>
     */
    public function lawn(): BelongsTo
    {
        return $this->belongsTo(
            related: Lawn::class,
        );
    }
}
