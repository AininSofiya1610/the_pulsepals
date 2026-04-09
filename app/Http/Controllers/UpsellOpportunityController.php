<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UpsellOpportunityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $upsells = \App\Models\UpsellOpportunity::all();
        return view('upsell_opportunities.index', compact('upsells'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('upsell_opportunities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'item_bought' => 'required',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        \App\Models\UpsellOpportunity::create($request->all());

        return redirect()->route('upsell-opportunities.index')
                        ->with('success','Upsell recorded successfully.');
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
