<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{


    /**
     * @OA\Post(
     *     path="/api/comments/{postid}",
     *     tags={"Comment"},
     *     summary="Create a new comment for a post",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="content", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="post_id", type="integer"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function store(Request $request, $postId)
    {
        $validatedData = $request->validate([
            'content' => 'required|string',
        ]);

        $post = Post::findOrFail($postId);
        $comment = $post->comments()->create([
            'content' => $validatedData['content'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json($comment, 201);
    }
    /**
     * @OA\Patch(
     *     path="/api/comments/{id}",
     *     tags={"Comment"},
     *     summary="Update an existing comment",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="content", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="post_id", type="integer"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        // Check if the user is the owner of the comment
        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['error' => 'You are not authorized to update this comment.'], 403);
        }

        $validatedData = $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update([
            'content' => $validatedData['content'],
        ]);

        return response()->json($comment, 200);
    }
    /**
     * @OA\Delete(
     *     path="/api/comments/{id}",
     *     tags={"Comment"},
     *     summary="Delete an existing comment",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Successful response"
     *     )
     * )
     */
    public function destroy(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        // Check if the user is the owner of the comment
        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['error' => 'You are not authorized to delete this comment.'], 403);
        }

        $comment->delete();

        return ["success" => true, "Message" => "Post deleted successfully"];
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{postId}/comments",
     *     tags={"Comment"},
     *     summary="Get all comments on a specific post",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="post_id", type="integer"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function getAllCommentsOnPost(Request $request, $postId)
    {
        $comments = Comment::where('post_id', $postId)->get();

        return response()->json($comments, 200);
    }
}
