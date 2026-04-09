<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientEngagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $engagements = \App\Models\ClientEngagement::all();
        return view('client_engagements.index', compact('engagements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('client_engagements.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'activity_type' => 'required',
            'date' => 'required|date',
            'notes' => 'nullable',
        ]);

        \App\Models\ClientEngagement::create($request->all());

        return redirect()->route('client-engagements.index')
                        ->with('success','Engagement recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
