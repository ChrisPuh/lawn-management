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

final class LawnCare extends Model
{
    use CanGetTableNameStatically;

    /**
     * @uses HasFactory<\Database\Factories\LawnCareFactory>
     */
    use HasFactory;

    protected $table = 'lawn_cares';

    /**
     * @var string[]
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
     * @var string[]
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
