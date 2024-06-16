<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts/list",
     *     tags={"Post"},
     *     summary="Get all posts",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="user_id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="content", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $posts = Post::all();
        return response(['success' => true, 'data' => $posts], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/posts",
     *     tags={"Post"},
     *     summary="Get the authenticated user's posts",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="user_id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="content", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function ownPost(Request $request)
    {
        $user = $request->user();
        $posts = $user->posts()->get();

        return response()->json(['success' => true, 'data' => $posts]);
    }

    /**
     * @OA\Post(
     *     path="/api/posts/create",
     *     tags={"Post"},
     *     summary="Create a new post",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="New Post"),
     *             @OA\Property(property="content", type="string", example="This is the content of the new post.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="Message", type="string", example="Post created successfully")
     *         )
     *     )
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
     *     path="/api/show/{id}",
     *     tags={"Post"},
     *     summary="Get a specific post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $post = Post::findOrFail($id);
        return response(['success' => true, 'data' => $post], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/posts/update/{id}",
     *     tags={"Post"},
     *     summary="Update a post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Post"),
     *             @OA\Property(property="content", type="string", example="This is the updated content of the post.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="Message", type="string", example="Post updated successfully")
     *         )
     *     )
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
     *     path="/api/posts/delete/{id}",
     *     tags={"Post"},
     *     summary="Delete a post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="Message", type="string", example="Post deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return ["success" => true, "Message" => "Post deleted successfully"];
    }

    //share post
    public function share(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
        ]);

        $user = $request->user();
        $sharedPost = Post::find($request->input('post_id'));

        $post = new Post();
        $post->user_id = $user->id;
        $post->title = $sharedPost->title;
        $post->content = $sharedPost->content;
        $post->sharedPost()->associate($sharedPost);
        $post->save();

        return response()->json([
            'success' => true,
            'message' => 'Post shared successfully',
            'post' => $post,
        ], 201);
    }
}
