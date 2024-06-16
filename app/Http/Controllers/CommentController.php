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
     *     path="/posts/{postId}/comments",
     *     tags={"Comment"},
     *     summary="Create a new comment on a post",
     *     description="Creates a new comment on the specified post",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="postId",
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
     *                     property="content",
     *                     type="string",
     *                     description="The content of the new comment"
     *                 ),
     *                 required={"content"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 description="The ID of the new comment"
     *             ),
     *             @OA\Property(
     *                 property="content",
     *                 type="string",
     *                 description="The content of the new comment"
     *             ),
     *             @OA\Property(
     *                 property="user_id",
     *                 type="integer",
     *                 description="The ID of the user who created the comment"
     *             ),
     *             @OA\Property(
     *                 property="post_id",
     *                 type="integer",
     *                 description="The ID of the post the comment was created on"
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="string",
     *                 format="date-time",
     *                 description="The date and time the comment was created"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="string",
     *                 format="date-time",
     *                 description="The date and time the comment was last updated"
     *             )
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
     * @OA\Put(
     *     path="/comments/{id}",
     *     tags={"Comment"},
     *     summary="Update a comment",
     *     description="Updates the specified comment",
     *     security={{"sanctum":{}}},
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
     *                     property="content",
     *                     type="string",
     *                     description="The new content of the comment"
     *                 ),
     *                 required={"content"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 description="The ID of the updated comment"
     *             ),
     *             @OA\Property(
     *                 property="content",
     *                 type="string",
     *                 description="The updated content of the comment"
     *             ),
     *             @OA\Property(
     *                 property="user_id",
     *                 type="integer",
     *                 description="The ID of the user who created the comment"
     *             ),
     *             @OA\Property(
     *                 property="post_id",
     *                 type="integer",
     *                 description="The ID of the post the comment was created on"
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="string",
     *                 format="date-time",
     *                 description="The date and time the comment was created"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="string",
     *                 format="date-time",
     *                 description="The date and time the comment was last updated"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 description="The error message indicating the user is not authorized to update the comment"
     *             )
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
     *     path="/comments/{id}",
     *     tags={"Comment"},
     *     summary="Delete a comment",
     *     description="Deletes the specified comment",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
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
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 description="A boolean indicating whether the deletion was successful"
     *             ),
     *             @OA\Property(
     *                 property="Message",
     *                 type="string",
     *                 description="A message indicating the comment was deleted successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 description="The error message indicating the user is not authorized to delete the comment"
     *             )
     *         )
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

}
