<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WaitingListStatus;
use App\Traits\CanGetTableNameStatically;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $reason
 * @property WaitingListStatus $status
 * @property Carbon|null $invited_at
 * @property Carbon|null $registered_at
 * @property Carbon|null $declined_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static \Database\Factories\WaitListFactory factory($count = null, $state = [])
 */
final class WaitingList extends Model
{
    use CanGetTableNameStatically;
    use HasFactory;

    protected $table = 'waitlist';

    protected $fillable = [
        'name',
        'email',
        'reason',
        'status',
        'invited_at',
        'registered_at',
        'declined_at'

    ];

    protected $casts = [
        'status' => WaitingListStatus::class,
        'invited_at' => 'datetime',
        'registered_at' => 'datetime',
        'declined_at' => 'datetime'

    ];
}
