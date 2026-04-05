<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'download_speed',
        'upload_speed',
        'price',
        'rate_limit',
        'description',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
