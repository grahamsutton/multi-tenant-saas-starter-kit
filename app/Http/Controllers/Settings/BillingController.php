<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class BillingController extends Controller
{
    /**
     * Redirect to the Stripe Customer Billing Portal.
     */
    public function edit(Request $request): Response
    {
        return Inertia::location(
            $request->user()->currentOrganization->billingPortalUrl(route('profile.edit'))
        );
    }
}
