<?php

namespace App\Http\Controllers;

use App\Models\Pic;
use App\Http\Requests\StorePicRequest;
use App\Http\Requests\UpdatePicRequest;

class PicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pics = Pic::all();
        return response()->json($pics);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePicRequest $request)
    {
        $validated = $request->validated();
        $pic = Pic::create($validated);
        return response()->json($pic, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pic $pic)
    {
        return response()->json($pic);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePicRequest $request, Pic $pic)
    {
        $validated = $request->validated();
        $pic->update($validated);
        return response()->json($pic);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pic $pic)
    {
        $pic->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
