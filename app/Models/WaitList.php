<?php

namespace App\Models;

use App\Enums\WaitlistStatus;
use App\Traits\CanGetTableNameStatically;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitList extends Model
{
    use HasFactory;
    use CanGetTableNameStatically;

    protected $table = 'waitlist';

    protected $fillable = [
        'name',
        'email',
        'reason',
        'status',
        'invited_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'status' => WaitlistStatus::class,

    ];
}
