<?php

namespace App\Http\Controllers\API;

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
        $users = User::where('id', auth()->id())->get();
        return UserResource::collection($users);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Ensure that the requested user belongs to the authenticated user
        if ($user->id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return UserResource::make($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Ensure that the requested user belongs to the authenticated user
        if ($user->id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Update the user attributes based on the request data
        $user->name = $request->input('name');
        // Update other user attributes as needed
        // ...

        $user->save();

        return UserResource::make($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Ensure that the requested user belongs to the authenticated user
        if ($user->id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Delete the user
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function profile()
    {
        return UserResource::make(auth()->user());
    }
}
