<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\OrganizationUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller
{
    /**
     * Show the organization settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Organization');
    }

    /**
     * Update the organization.
     */
    public function update(OrganizationUpdateRequest $request): RedirectResponse
    {
        $organization = $request->user()->currentOrganization;

        $organization->update([
            'name' => $request->validated('name'),
            'slug' => Str::slug($request->validated('name').'-'.Str::random(6)),
        ]);

        return to_route('organization.edit');
    }
}
