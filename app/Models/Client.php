<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
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
        'email',
        'phone',
        'whatsapp',
        'birth_date',
        'gender',
        'address',
        'notes',
        'is_active',
        'last_appointment_at',
        'total_appointments',
        'total_spent',
        'preferences',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birth_date' => 'date',
        'last_appointment_at' => 'datetime',
        'total_spent' => 'decimal:2',
        'total_appointments' => 'integer',
        'is_active' => 'boolean',
        'preferences' => 'array',
    ];

    /**
     * Get the tenant that owns the client.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope a query to only include active clients.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to search clients by name, email or phone.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('whatsapp', 'like', "%{$search}%");
        });
    }

    /**
     * Get client's full name with phone.
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->name;
        if ($this->phone) {
            $name .= " ({$this->phone})";
        }
        return $name;
    }

    /**
     * Get client's age.
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->birth_date) {
            return null;
        }
        
        return $this->birth_date->diffInYears(now());
    }

    /**
     * Check if client has WhatsApp.
     */
    public function hasWhatsApp(): bool
    {
        return !empty($this->whatsapp);
    }

    /**
     * Get formatted phone number.
     */
    public function getFormattedPhoneAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }
        
        // Simple Brazilian phone formatting
        $phone = preg_replace('/\D/', '', $this->phone);
        
        if (strlen($phone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
        } elseif (strlen($phone) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $phone);
        }
        
        return $this->phone;
    }
}