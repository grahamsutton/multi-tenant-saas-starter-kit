<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationIsSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $organization = $request->user()?->currentOrganization;

        if ($organization && ($organization->subscribed('default') || $organization->onGenericTrial())) {
            return $next($request);
        }

        return redirect()->route('billing.plans');
    }
}
