<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LawnCare\LawnCareType;
use App\Traits\CanGetTableNameStatically;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LawnCareLog extends Model
{
    use CanGetTableNameStatically;
    use HasFactory;

    public $timestamps = true;

    protected $table = 'lawn_care_logs';

    protected $fillable = [
        'lawn_care_id',
        'user_id',
        'action',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * @return BelongsTo<LawnCare, $this>
     */
    public function lawnCare(): BelongsTo
    {
        return $this->belongsTo(LawnCare::class);
    }

    /**
     * @return BelongsTo<User,$this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    public function scopeForLawnCare(Builder $query, LawnCare|int $lawnCare): Builder
    {
        $lawnCareId = $lawnCare instanceof LawnCare ? $lawnCare->id : $lawnCare;

        return $query->where('lawn_care_id', $lawnCareId);
    }

    public function scopeForUser(Builder $query, User|int $user): Builder
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $query->where('user_id', $userId);
    }

    public function scopeCreatedBetween(Builder $query, DateTime $start, DateTime $end): Builder
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    public function getCareType(): ?LawnCareType
    {
        return isset($this->data['type'])
            ? LawnCareType::tryFrom($this->data['type'])
            : null;
    }

    public function getCareData(): ?array
    {
        return $this->data['care_data'] ?? null;
    }

    public function getChanges(): ?array
    {
        return $this->data['changes'] ?? null;
    }

    public function getAdditionalData(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }
}
