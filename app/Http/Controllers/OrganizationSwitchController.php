<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrganizationSwitchController extends Controller
{
    /**
     * Switch the user's current organization.
     */
    public function __invoke(Request $request, Organization $organization): RedirectResponse
    {
        $request->user()->switchOrganization($organization);

        return back();
    }
}
