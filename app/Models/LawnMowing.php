<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\CanGetTableNameStatically;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class LawnMowing extends Model
{
    use CanGetTableNameStatically;
    use HasFactory;

    protected $fillable = [
        'lawn_id',
        'last_mowed',
        'cutting_height',
    ];

    protected $table = 'lawn_mowing_records';

    protected $casts = [
        'mowed_on' => 'date',          // Datum wird in ein Carbon-Objekt umgewandelt
        'cutting_height' => 'string',  // Sicherstellen, dass cutting_height als String behandelt wird
    ];

    public function lawn()
    {
        return $this->belongsTo(
            related: Lawn::class,
            foreignKey: 'lawn_id',
            ownerKey: 'id'
        );
    }
}
