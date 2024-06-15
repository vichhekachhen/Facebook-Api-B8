<?php

namespace App\Http\Controllers;

use App\Models\FriendRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class FriendRequestController extends Controller
{
    /**
     * @OA\Post(
     *     path="/friend-requests",
     *     summary="Send a friend request",
     *     tags={"FriendRequest"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="recipient_id",
     *                 type="integer"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Friend request sent",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="friend_request",
     *             )
     *         )
     *     )
     * )
     */
    public function send(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id|different:' . Auth::id(),
        ]);

        $friendRequest = FriendRequest::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $request->input('recipient_id'),
        ]);

        return response()->json([
            'message' => 'Friend request sent',
            'friend_request' => $friendRequest,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/friend-requests/{id}/accept",
     *     tags={"FriendRequest"},
     *     summary="Accept a friend request",
     *     security={{"bearerAuth":{}}},
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
     *         description="Friend request accepted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     */
    public function accept(FriendRequest $friendRequest)
    {
        if (Auth::id() == $friendRequest->recipient_id) {
            $friendRequest->update(['status' => 'accepted']);
            return response()->json([
                'message' => 'Friend request accepted',
            ], 200);
        }

        return response()->json([
            'message' => 'You are not authorized to accept this request',
        ], 403);
    }

    /**
     * @OA\Post(
     *     path="/friend-requests/{id}/decline",
     *     tags={"FriendRequest"},
     *     summary="Decline a friend request",
     *     security={{"bearerAuth":{}}},
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
     *         description="Friend request declined",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     */
    public function decline(FriendRequest $friendRequest)
    {
        if (Auth::id() == $friendRequest->recipient_id) {
            $friendRequest->update(['status' => 'declined']);
            return response()->json([
                'message' => 'Friend request declined',
            ], 200);
        }

        return response()->json([
            'message' => 'You are not authorized to decline this request',
        ], 403);
    }

    /**
     * @OA\Get(
     *     path="/friends",
     *     tags={"FriendRequest"},
     *     summary="Lisr all friends",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="friends",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="email", type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getFriends()
    {
        $friendRequests = FriendRequest::where(function ($query) {
            $query->where('sender_id', Auth::id())
                ->orWhere('recipient_id', Auth::id());
        })
            ->where('status', 'accepted')
            ->get();

        $friends = $friendRequests->map(function ($friendRequest) {
            return $friendRequest->sender_id == Auth::id()
                ? $friendRequest->recipient
                : $friendRequest->sender;
        });

        return response()->json([
            'friends' => $friends,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/friends/{id}",
     *     tags={"FriendRequest"},
     *     summary="Remove a friend",
     *     security={{"bearerAuth":{}}},
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
     *         description="Friend removed",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     */
    public function removeFriend(FriendRequest $friendRequest)
    {
        if (Auth::id() == $friendRequest->sender_id || Auth::id() == $friendRequest->recipient_id) {
            $friendRequest->delete();
            return response()->json([
                'message' => 'Friend removed',
            ], 200);
        }

        return response()->json([
            'message' => 'You are not authorized to remove this friend',
        ], 403);
    }
}
