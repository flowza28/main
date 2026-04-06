<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapLocation extends Model
{
    use HasFactory;

    public const TYPE_CLIENT = 'client';
    public const TYPE_ODP = 'odp';
    public const TYPE_SERVER = 'server';

    public const TYPES = [
        self::TYPE_CLIENT => 'Rumah Client',
        self::TYPE_ODP => 'ODP',
        self::TYPE_SERVER => 'Server',
    ];

    protected $fillable = [
        'type',
        'name',
        'address',
        'latitude',
        'longitude',
        'notes',
        'active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'active' => 'boolean',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }
}
