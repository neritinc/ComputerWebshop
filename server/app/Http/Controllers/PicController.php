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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePicRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Pic $pic)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePicRequest $request, Pic $pic)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pic $pic)
    {
        //
    }
}
