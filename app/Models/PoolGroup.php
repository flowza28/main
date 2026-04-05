<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoolGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_ip',
        'end_ip',
        'description',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
