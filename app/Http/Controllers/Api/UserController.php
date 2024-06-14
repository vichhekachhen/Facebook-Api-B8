<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::list();
        $user = UserResource::collection($user);
        return response(['sucess' => true, 'data' =>$user], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        dd(1);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        User::store($request,$id);
        return ["success" => true, "Message" =>"User updated successfully"];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(['message' => 'User Not Found'], 404);
        }

        $user->delete();
        return response(["success" => true, "Message" =>"The user deleted successfully"]);
    }
}
