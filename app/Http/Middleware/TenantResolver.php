<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantResolver
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $subdomain = $this->extractSubdomain($host);

        if ($subdomain && $subdomain !== 'www') {
            $tenant = $this->resolveTenant($subdomain);

            if (!$tenant) {
                abort(404, 'Tenant not found');
            }

            // Set tenant context
            app()->instance('tenant', $tenant);

            // Set database connection with tenant context
            $this->setTenantContext($tenant);

            // Set timezone
            if ($tenant->timezone) {
                Config::set('app.timezone', $tenant->timezone);
                date_default_timezone_set($tenant->timezone);
            }
        }

        return $next($request);
    }

    /**
     * Extract subdomain from host
     */
    private function extractSubdomain(string $host): ?string
    {
        $appDomain = config('app.domain', 'localhost');

        // Remove port if present
        $host = explode(':', $host)[0];

        if (str_ends_with($host, '.' . $appDomain)) {
            $subdomain = str_replace('.' . $appDomain, '', $host);
            return $subdomain ?: null;
        }

        return null;
    }

    /**
     * Resolve tenant by subdomain
     */
    private function resolveTenant(string $subdomain): ?Tenant
    {
        return Cache::remember(
            "tenant.{$subdomain}",
            3600, // 1 hour
            fn() => Tenant::where('subdomain', $subdomain)
                          ->where('status', 'active')
                          ->first()
        );
    }

    /**
     * Set tenant context for database queries
     */
    private function setTenantContext(Tenant $tenant): void
    {
        // Add global scope for tenant isolation
        DB::listen(function ($query) use ($tenant) {
            // This will be handled by model scopes
        });

        // Store tenant ID in config for easy access
        Config::set('tenant.current_id', $tenant->id);
    }
}
