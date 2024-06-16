<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LikeController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/likes",
     *     summary="Like or Unlike a Post",
     *     tags={"Like&Unlike"},
     *     description="This endpoint allows a user to like or unlike a post.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="post_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="like_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get the validated data
        $data = $validator->validated();

        // Get the authenticated user
        $user = Auth::user();

        // Check if the like already exists
        $like = Like::where('user_id', $user->id)
            ->where('post_id', $data['post_id'])
            ->first();

        if ($like) {
            // If the like exists, delete it (unlike)
            $like->delete();
            $message = 'You unliked a post';
        } else {
            // If the like does not exist, create a new like
            $newLike = Like::create([
                'user_id' => $user->id,
                'post_id' => $data['post_id'],
            ]);
            $message = 'You liked a post';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'like_id' => $like ? $like->id : $newLike->id, // Return the like ID
        ], 200);
    }

    /**
     * @OA\Schema(
     *     schema="likeRequest",
     *     @OA\Property(property="post_id", type="integer", required=true)
     * )
     */
}
