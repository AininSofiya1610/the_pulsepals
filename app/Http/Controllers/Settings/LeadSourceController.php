<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\LeadSource;
use Illuminate\Http\Request;

class LeadSourceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        LeadSource::create(['name' => $request->name]);
        return back()->with('success', 'Lead source added.');
    }

    public function destroy(LeadSource $leadSource)
    {
        $leadSource->delete();
        return back()->with('success', 'Lead source deleted.');
    }
}