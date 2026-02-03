<?php

namespace App\Http\Controllers;

use App\Models\Comment as CurrentModel;
use App\Http\Requests\StoreCommentRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateCommentRequest as UpdateCurrentModelRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->apiResponse(function () {
            // Betöltjük a user nevét és a termék nevét is
            return CurrentModel::with([
                'user:id,name',
                'product:id,name'
            ])->latest()->get(); // A legfrissebb kommentek lesznek legfelül
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCurrentModelRequest $request)
    {
        return $this->apiResponse(function () use ($request) {
            $this->authorize('create', CurrentModel::class);
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;
            return CurrentModel::create($data);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            // Itt is betöltjük a részleteket
            return CurrentModel::with(['user:id,name', 'product:id,name'])->findOrFail($id);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCurrentModelRequest $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            $comment = CurrentModel::findOrFail($id);
            $this->authorize('update', $comment);

            $comment->update($request->validated());
            return $comment;
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            $comment = CurrentModel::findOrFail($id);
            $this->authorize('delete', $comment);

            $comment->delete();
            return ['id' => $id, 'message' => 'Deleted successfully'];
        });
    }
}
