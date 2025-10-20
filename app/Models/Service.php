<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'duration',
        'price',
        'color',
        'is_active',
        'online_booking_enabled',
        'buffer_time_before',
        'buffer_time_after',
        'advance_booking_min',
        'advance_booking_max',
        'requirements',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'duration' => 'integer',
        'is_active' => 'boolean',
        'online_booking_enabled' => 'boolean',
        'buffer_time_before' => 'integer',
        'buffer_time_after' => 'integer',
        'advance_booking_min' => 'integer',
        'advance_booking_max' => 'integer',
        'requirements' => 'array',
    ];

    /**
     * Get the tenant that owns the service.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the appointments for the service.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Scope a query to only include active services.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include services with online booking enabled.
     */
    public function scopeOnlineBookingEnabled($query)
    {
        return $query->where('online_booking_enabled', true);
    }
}
