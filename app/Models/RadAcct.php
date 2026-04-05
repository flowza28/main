<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RadAcct extends Model
{
    use HasFactory;

    protected $table = 'radacct';
    protected $guarded = [];

    protected $casts = [
        'acctinputoctets' => 'decimal:0',
        'acctoutputoctets' => 'decimal:0',
        'acctstarttime' => 'datetime',
        'acctstoptime' => 'datetime',
    ];
}
