<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebsiteVisitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $visitors = \App\Models\WebsiteVisitor::all();
        return view('website_visitors.index', compact('visitors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('website_visitors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'page_visited' => 'required',
            'visitor_ip' => 'required',
            'date' => 'required|date',
        ]);

        \App\Models\WebsiteVisitor::create($request->all());

        return redirect()->route('website-visitors.index')
                        ->with('success','Visitor recorded successfully.');
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
