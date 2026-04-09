<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketOption;
use App\Models\Unit;
use App\Services\DropdownService;
use App\Models\LeadSource;

class TicketSettingsController extends Controller
{
    public function index()
    {
        $priorities  = TicketOption::where('type', 'priority')->orderBy('order')->get();
        $categories  = TicketOption::where('type', 'category')->orderBy('order')->get();
        $ticketTypes = TicketOption::where('type', 'ticket_type')->orderBy('order')->get();
        $units       = Unit::all();
        $leadSources = LeadSource::orderBy('order')->get();

        return view('tickets.settings', compact(
            'priorities', 'categories', 'ticketTypes', 'units', 'leadSources'
        ));
    }

    // ==========================================
    // TICKET OPTION METHODS
    // ==========================================

    public function storeOption(Request $request, DropdownService $dropdowns)
    {
        $request->validate([
            'type'  => 'required|in:priority,category,ticket_type',
            'value' => 'required|string|max:255',
        ]);

        $maxOrder = TicketOption::where('type', $request->type)->max('order');

        TicketOption::create([
            'type'      => $request->type,
            'value'     => $request->value,
            'order'     => $maxOrder + 1,
            'is_active' => true,
        ]);

        $dropdowns->clearCache($request->type);

        return redirect()->back()->with('success', 'Option added successfully.');
    }

    public function updateOption(Request $request, $id, DropdownService $dropdowns)
    {
        $option = TicketOption::findOrFail($id);

        $request->validate([
            'value' => 'required|string|max:255',
        ]);

        $option->update(['value' => $request->value]);
        $dropdowns->clearCache($option->type);

        return redirect()->back()->with('success', 'Option updated successfully.');
    }

    public function destroyOption($id, DropdownService $dropdowns)
    {
        $option = TicketOption::findOrFail($id);
        $type   = $option->type;
        $option->delete();
        $dropdowns->clearCache($type);

        return redirect()->back()->with('success', 'Option removed.');
    }

    // ==========================================
    // UNIT METHODS
    // ==========================================

    public function storeUnit(Request $request, DropdownService $dropdowns)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Unit::create([
            'name'      => $request->name,
            'is_active' => true,
        ]);

        $dropdowns->clearCache('units');

        return redirect()->back()->with('success', 'Unit added successfully.');
    }

    public function updateUnit(Request $request, $id, DropdownService $dropdowns)
    {
        $unit = Unit::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $unit->update(['name' => $request->name]);
        $dropdowns->clearCache('units');

        return redirect()->back()->with('success', 'Unit updated successfully.');
    }

    public function destroyUnit($id, DropdownService $dropdowns)
    {
        Unit::findOrFail($id)->delete();
        $dropdowns->clearCache('units');

        return redirect()->back()->with('success', 'Unit removed.');
    }

    // ==========================================
    // LEAD SOURCE METHODS
    // ==========================================

    /**
     * Store a new Lead Source.
     */
    public function storeLeadSource(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:lead_sources,name',
        ]);

        $maxOrder = LeadSource::max('order') ?? 0;

        LeadSource::create([
            'name'      => $request->name,
            'order'     => $maxOrder + 1,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Lead source added successfully.');
    }

    /**
     * Update an existing Lead Source.
     */
    public function updateLeadSource(Request $request, $id)
    {
        $source = LeadSource::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:lead_sources,name,' . $id,
        ]);

        $source->update(['name' => $request->name]);

        return redirect()->back()->with('success', 'Lead source updated successfully.');
    }

    /**
     * Delete a Lead Source.
     */
    public function destroyLeadSource($id)
    {
        LeadSource::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Lead source removed.');
    }
}
