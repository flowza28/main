<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'password',
        'package_id',
        'pool_group_id',
        'expired_at',
        'active',
        'status',
        'email',
        'phone',
        'address',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'expired_at' => 'date',
        'active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function poolGroup()
    {
        return $this->belongsTo(PoolGroup::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    public function getStatusAttribute(): string
    {
        if ($this->isExpired()) {
            return 'expired';
        }

        return $this->attributes['status'] ?? 'active';
    }
}
