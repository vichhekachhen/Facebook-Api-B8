<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $likes = Like::all(); 
        return response(['success' => true, 'data' => $likes], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $like = Like::create($request->all()); 
        return response(['success' => true, 'message' => 'Like created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $like = Like::find($id);
        if ($like) {
            return response(['success' => true, 'data' => $like], 200);
        } else {
            return response(['success' => false, 'message' => 'Like not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $like = Like::find($id);
        if ($like) {
            $like->update($request->all());
            return response(['success' => true, 'message' => 'Like updated successfully'], 200);
        } else {
            return response(['success' => false, 'message' => 'Like not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $like = Like::find($id);
        if ($like) {
            $like->delete();
            return response(['success' => true, 'message' => 'Like deleted successfully'], 200);
        } else {
            return response(['success' => false, 'message' => 'Like not found'], 404);
        }
    }
}