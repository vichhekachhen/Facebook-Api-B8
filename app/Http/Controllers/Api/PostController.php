<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();
        return response(['success' => true, 'data' => $posts], 200);

    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $user = Auth::user();
    //     // dd($user);
    //     Post::store($request);
    //     return ["success" => true, "Message" => "Create Post successfully"];

    // }
    public function store(Request $request)
    {
        // Validate the incoming request data
        $user = auth()->user();
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        // Create the post using the authenticated user's ID
        $post = Post::create([
            'user_id' => $user->id,
            'title' => $validatedData['title'],
            'body' => $request->body,
            'image' => $request->image,
        ]);

        return response()->json([
            "success" => true,
            "message" => "Post created successfully",
            "data" => $post
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Post::store($request, $id);
        return ["success" => true, "Message" => "update post successfully"];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);
        $post->delete();
        return ["success" => true, "Message" => "post deleted successfully"];
    }
}
