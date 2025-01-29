<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LawnCare\LawnCareType;
use App\Traits\CanGetTableNameStatically;
use DateTime;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $lawn_care_id
 * @property int $user_id
 * @property string $action
 * @property array<array-key, mixed>|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read LawnCare $lawnCare
 * @property-read User $user
 *
 * @method static Builder<static>|LawnCareLog createdBetween(\DateTime $start, \DateTime $end)
 * @method static \Database\Factories\LawnCareLogFactory factory($count = null, $state = [])
 * @method static Builder<static>|LawnCareLog forAction(string $action)
 * @method static Builder<static>|LawnCareLog forLawnCare(\App\Models\LawnCare|int $lawnCare)
 * @method static Builder<static>|LawnCareLog forUser(\App\Models\User|int $user)
 * @method static Builder<static>|LawnCareLog newModelQuery()
 * @method static Builder<static>|LawnCareLog newQuery()
 * @method static Builder<static>|LawnCareLog query()
 * @method static Builder<static>|LawnCareLog whereAction($value)
 * @method static Builder<static>|LawnCareLog whereCreatedAt($value)
 * @method static Builder<static>|LawnCareLog whereData($value)
 * @method static Builder<static>|LawnCareLog whereId($value)
 * @method static Builder<static>|LawnCareLog whereLawnCareId($value)
 * @method static Builder<static>|LawnCareLog whereUpdatedAt($value)
 * @method static Builder<static>|LawnCareLog whereUserId($value)
 *
 * @mixin Eloquent
 */
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
