<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user()->load('expenses');
        return ExpenseResource::collection($user->expenses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExpenseRequest $request)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validated();
            $validatedData['user_id'] = auth()->id();

            $group = Group::findOrFail($validatedData['group_id']);

            if($group->user_id !== auth()->id()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $expense = Expense::create($validatedData);

            DB::commit();

            return ExpenseResource::make($expense);
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th->getMessage());
            Log::error($request->validated());

            return response()->json(['message' => 'Something went wrong'], 500);
        } finally {
            DB::commit();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        try {
            if($expense->user_id !== auth()->id()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            return ExpenseResource::make($expense);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExpenseRequest $request, Expense $expense)
    {
        try {
            DB::beginTransaction();

            if($expense->user_id !== auth()->id()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $validatedData = $request->validated();
            $expense->update($validatedData);

            DB::commit();

            return ExpenseResource::make($expense);

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            Log::error($request->validated());

            return response()->json(['message' => 'Something went wrong'], 500);
        } finally {
            DB::commit();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        try {
            DB::beginTransaction();

            if($expense->user->id !== auth()->id()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $expense->delete();

            DB::commit();

            return response()->json(['message' => 'Expenses deleted'], 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            DB::rollBack();
            return response()->json(['message' => 'Something went wrong'], 500);
        } finally {
            DB::commit();
        }
    }
}
