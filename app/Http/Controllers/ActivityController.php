<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Lead;
use App\Models\Customer;

class ActivityController extends Controller
{
    /**
     * Store a newly created activity (for leads or customers).
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:call,email,whatsapp,note',
            'description' => 'required',
            'lead_id' => 'nullable|exists:leads,id',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        Activity::create([
            'user_id' => auth()->id(),
            'lead_id' => $request->lead_id,
            'customer_id' => $request->customer_id,
            'type' => $request->type,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Activity logged successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();

        return redirect()->back()->with('success','Activity deleted successfully.');
    }
}
