<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts",
     *     tags={"Post"},
     *     summary="List all posts",
     *     @OA\Response(response="200", description="Display a listing of the resource.")
     * )
     */
    public function index()
    {
        $posts = Post::all();
        return response(['success' => true, 'data' => $posts], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     tags={"Post"},
     *     summary="Create a new Post",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Store a newly created resource in storage.")
     * )
     */
    public function store(Request $request)
    {
        $post = new Post();
        $post->user_id = $request->user()->id;
        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->user()->associate(auth()->user());
        $post->save();

        return ["success" => true, "Message" => "Post created successfully"];
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     tags={"Post"},
     *     summary="Show Post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Display the specified resource.")
     * )
     */
    public function show($id)
    {
        $post = Post::findOrFail($id);
        return response(['success' => true, 'data' => $post], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     tags={"Post"},
     *     summary="Update Post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Update the specified resource in storage.")
     * )
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->save();

        return ["success" => true, "Message" => "Post updated successfully"];
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     tags={"Post"},
     *     summary="Delete Post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Remove the specified resource from storage.")
     * )
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return ["success" => true, "Message" => "Post deleted successfully"];
    }

}
