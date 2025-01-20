<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\LawnCare\CareData;
use App\DataObjects\LawnCare\FertilizingData;
use App\DataObjects\LawnCare\MowingData;
use App\DataObjects\LawnCare\WateringData;
use App\Enums\LawnCare\LawnCareType;
use App\Traits\CanGetTableNameStatically;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $lawn_id
 * @property int $created_by_id
 * @property LawnCareType $type
 * @property array<array-key, mixed>|null $care_data
 * @property string|null $notes
 * @property Carbon|null $performed_at
 * @property Carbon|null $scheduled_for
 * @property Carbon|null $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $createdBy
 * @property-read Lawn $lawn
 *
 * @method static Builder<static>|LawnCare completed()
 * @method static \Database\Factories\LawnCareFactory factory($count = null, $state = [])
 * @method static Builder<static>|LawnCare forLawn(\App\Models\Lawn|int $lawn)
 * @method static Builder<static>|LawnCare newModelQuery()
 * @method static Builder<static>|LawnCare newQuery()
 * @method static Builder<static>|LawnCare query()
 * @method static Builder<static>|LawnCare scheduled()
 * @method static Builder<static>|LawnCare whereCareData($value)
 * @method static Builder<static>|LawnCare whereCompletedAt($value)
 * @method static Builder<static>|LawnCare whereCreatedAt($value)
 * @method static Builder<static>|LawnCare whereCreatedById($value)
 * @method static Builder<static>|LawnCare whereId($value)
 * @method static Builder<static>|LawnCare whereLawnId($value)
 * @method static Builder<static>|LawnCare whereNotes($value)
 * @method static Builder<static>|LawnCare wherePerformedAt($value)
 * @method static Builder<static>|LawnCare whereScheduledFor($value)
 * @method static Builder<static>|LawnCare whereType($value)
 * @method static Builder<static>|LawnCare whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class LawnCare extends Model
{
    use CanGetTableNameStatically;

    /**
     * @uses HasFactory<\Database\Factories\LawnCareFactory>
     */
    use HasFactory;

    protected $table = 'lawn_cares';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'lawn_id',
        'type',
        'care_data',
        'notes',
        'performed_at',
        'scheduled_for',
        'completed_at',
        'created_by_id',
    ];

    /**
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'type' => LawnCareType::class,
        'care_data' => 'array',
        'performed_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Lawn, $this>
     */
    public function lawn(): BelongsTo
    {
        return $this->belongsTo(Lawn::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function getCareData(): ?CareData
    {
        if (! $this->care_data) {
            return null;
        }

        return match ($this->type) {
            LawnCareType::MOW => MowingData::from($this->care_data),
            LawnCareType::FERTILIZE => FertilizingData::from($this->care_data),
            LawnCareType::WATER => WateringData::from($this->care_data),
            default => null,
        };
    }

    public function setCareData(CareData $data): void
    {
        $this->care_data = $data->toArray();
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->whereNotNull('scheduled_for')
            ->whereNull('completed_at');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopeForLawn(Builder $query, int|Lawn $lawn): Builder
    {
        $lawnId = $lawn instanceof Lawn ? $lawn->id : $lawn;

        return $query->where('lawn_id', $lawnId);
    }

    public function complete(): void
    {
        $this->completed_at = now();
        if (! $this->performed_at) {
            $this->performed_at = now();
        }
        $this->save();
    }

    public function isScheduled(): bool
    {
        return ! is_null($this->scheduled_for) && is_null($this->completed_at);
    }

    public function isCompleted(): bool
    {
        return ! is_null($this->completed_at) && ! is_null($this->performed_at);
    }

    public function isDue(): bool
    {
        return $this->isScheduled() && $this->scheduled_for->isPast();
    }

    public function isPending(): bool
    {
        return $this->isScheduled() && $this->scheduled_for->isFuture();
    }

    public function getStatus(): string
    {
        return match (true) {
            $this->isCompleted() => 'completed',
            $this->isDue() => 'due',
            $this->isPending() => 'pending',
            default => 'unscheduled',
        };
    }
}
