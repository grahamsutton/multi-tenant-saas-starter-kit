<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlanController extends Controller
{
    /**
     * Display available plans for subscription.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        if ($request->user()->currentOrganization->subscribed('default')) {
            return to_route('dashboard');
        }

        return Inertia::render('billing/Plans', [
            'plans' => Plan::where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
