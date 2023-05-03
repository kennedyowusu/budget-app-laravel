<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user()->load('groups');
        return GroupResource::collection($user->groups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GroupRequest $request)
    {

        try {
            DB::beginTransaction();

            $validatedData = $request->validated();
            $validatedData['user_id'] = auth()->id();

            $group = Group::create($validatedData);

            DB::commit();

            return GroupResource::make($group);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());
            Log::error($request->validated());

            return response()->json(['message' => 'Something went wrong'], 500);

        } finally {
            DB::commit();
        }
    }

    public function show(Group $group)
    {
        try {
            if ($group->user_id !== auth()->id()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            return GroupResource::make($group);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    public function update(GroupRequest $request, Group $group)
    {
        try {
            DB::beginTransaction();

            if ($group->user_id !== auth()->id()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $group->update($request->validated());

            DB::commit();

            return GroupResource::make($group);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Something went wrong'], 500);

        } finally {
            DB::commit();
        }
    }

    public function destroy(Group $group)
    {
        try {
            DB::beginTransaction();

            if ($group->user_id !== auth()->id()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $group->delete();

            DB::commit();

            return response()->json(['message' => 'Group deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Something went wrong'], 500);

        } finally {
            DB::commit();
        }
    }
}
