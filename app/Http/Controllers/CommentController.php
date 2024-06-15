<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/posts/{postId}/comments",
     *     tags={"Comment"},
     *     summary="Get comments for a post",
     *     description="Retrieve all comments for the specified post",
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="user", type="object", @OA\Property(property="name", type="string")),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     )
     * )
     */
    public function index($postId)
    {
        $post = Post::find($postId);

        if ($post) {
            $comments = $post->comments()->with('user')->get();
            return response()->json($comments);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/posts/{postId}/comments",
     *      tags={"Comment"},
     *     summary="Store a new comment",
     * 
     *     description="Create a new comment for the specified post",
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="content", type="string", example="This is a new comment.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="content", type="string", example="This is a new comment."),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="post_id", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     )
     * )
     */
    public function store(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);

        $comment = $post->comments()->create([
            'content' => $request->input('content'),
            'user_id' => auth()->id(),
        ]);

        return response()->json($comment, 201);
    }

    /**
     * @OA\Delete(
     *     path="/posts/{postId}/comments/{commentId}",
     *     tags={"Comment"},
     *     summary="Delete a comment",
     *     description="Delete the specified comment for the given post",
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="commentId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Comment deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Comment not found")
     *         )
     *     )
     * )
     */
    public function destroy($postId, $commentId)
    {
        $comment = Comment::where('post_id', $postId)->where('id', $commentId)->first();

        if ($comment) {
            if ($comment->user_id == Auth::user()->id) {
                $comment->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Comment deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to delete this comment'
                ], 403);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found'
            ], 404);
        }
    }
}
