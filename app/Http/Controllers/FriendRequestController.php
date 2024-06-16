<?php

namespace App\Http\Controllers;

use App\Models\FriendRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class FriendRequestController extends Controller
{
    /**
     * @OA\Post(
     *     path="/friend-requests",
     *     tags={"FriendRequests"},
     *     summary="Send a friend request",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="recipient_id", type="integer", example=123)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Friend request sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Friend request sent"),
     *             @OA\Property(
     *                 property="friend_request",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="sender_id", type="integer"),
     *                 @OA\Property(property="recipient_id", type="integer"),
     *                 @OA\Property(property="status", type="string", enum={"pending", "accepted", "declined"}),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function send(Request $request)
    {
        $this->validate($request, [
            'recipient_id' => 'required|exists:users,id',
        ]);

        $friendRequest = FriendRequest::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $request->recipient_id,
        ]);

        return response()->json([
            'message' => 'Friend request sent',
            'friend_request' => $friendRequest,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/friend-requests/{id}/accept",
     *     tags={"FriendRequests"},
     *     summary="Accept a friend request",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Friend request accepted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Friend request accepted")
     *         )
     *     )
     * )
     */
    public function accept($id)
    {
        $friendRequest = FriendRequest::findOrFail($id);
        $friendRequest->accept();

        return response()->json([
            'message' => 'Friend request accepted',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/friend-requests/{id}/decline",
     *     tags={"FriendRequests"},
     *     summary="Decline a friend request",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Friend request declined",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Friend request declined")
     *         )
     *     )
     * )
     */
    public function decline($id)
    {
        $friendRequest = FriendRequest::findOrFail($id);
        $friendRequest->decline();

        return response()->json([
            'message' => 'Friend request declined',
        ]);
    }
}
