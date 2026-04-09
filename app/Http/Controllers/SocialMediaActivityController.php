<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SocialMediaActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activities = \App\Models\SocialMediaActivity::all();
        return view('social_media_activities.index', compact('activities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('social_media_activities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'platform' => 'required',
            'activity_type' => 'required',
            'likes' => 'required|integer',
            'comments' => 'required|integer',
            'shares' => 'required|integer',
            'date' => 'required|date',
        ]);

        \App\Models\SocialMediaActivity::create($request->all());

        return redirect()->route('social-media-activities.index')
                        ->with('success','Activity recorded successfully.');
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
