<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WaitListStatus;
use App\Traits\CanGetTableNameStatically;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class WaitList extends Model
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
        'status' => WaitListStatus::class,
        'invited_at' => 'datetime',
        'registered_at' => 'datetime',
        'declined_at' => 'datetime'

    ];
}
