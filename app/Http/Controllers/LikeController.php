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
     *     tags={"Like"},
     *     summary="Create or delete a like",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="post_id",
     *                     type="integer"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Store a newly created resource in storage.")
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
            Like::create([
                'user_id' => $user->id,
                'post_id' => $data['post_id'],
            ]);
            $message = 'You liked a post';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ], 200);
    }

    /**
     * @OA\Schema(
     *     schema="likeRequest",
     *     @OA\Property(property="post_id", type="integer", required=true)
     * )
     */
}