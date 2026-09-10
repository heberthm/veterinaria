<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    /**
     * Relación con el tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope para filtrar por tenant
     */
    public function scopeForTenant($query, $tenantId = null)
    {
        $tenantId = $tenantId ?? auth()->user()?->tenant_id;
        
        if ($tenantId) {
            return $query->where('tenant_id', $tenantId);
        }
        
        return $query;
    }
}