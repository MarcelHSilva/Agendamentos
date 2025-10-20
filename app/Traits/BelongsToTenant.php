<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

trait BelongsToTenant
{
    /**
     * Boot the trait
     */
    protected static function bootBelongsToTenant(): void
    {
        // Auto-assign tenant_id when creating
        static::creating(function (Model $model) {
            if (!$model->tenant_id && $tenantId = static::getCurrentTenantId()) {
                $model->tenant_id = $tenantId;
            }
        });

        // Add global scope to filter by tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            if ($tenantId = static::getCurrentTenantId()) {
                $builder->where('tenant_id', $tenantId);
            }
        });
    }

    /**
     * Get current tenant ID
     */
    protected static function getCurrentTenantId(): ?int
    {
        // First try to get from config
        if ($tenantId = Config::get('tenant.current_id')) {
            return $tenantId;
        }
        
        // Try to get from app container if bound
        try {
            $tenant = app('tenant');
            return $tenant?->id;
        } catch (\Exception $e) {
            // If tenant is not bound in container, return null
            return null;
        }
    }

    /**
     * Scope query without tenant isolation
     */
    public function scopeWithoutTenantIsolation(Builder $query): Builder
    {
        return $query->withoutGlobalScope('tenant');
    }

    /**
     * Scope query for specific tenant
     */
    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->withoutGlobalScope('tenant')->where('tenant_id', $tenantId);
    }

    /**
     * Relationship to tenant
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}