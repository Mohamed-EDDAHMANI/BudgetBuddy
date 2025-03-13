<?php 


namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Create a new expense.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'tags' => 'nullable|array', // Optional tags to attach
            'tags.*' => 'exists:tags,id', // Ensure tags exist
        ]);

        // Create the expense
        $expense = Auth::user()->expenses()->create([
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        // Attach tags if provided
        if ($request->has('tags')) {
            $expense->tags()->attach($request->tags);
        }

        return response()->json([
            'message' => 'Expense created successfully',
            'expense' => $expense->load('tags'), // Load tags for the response
        ], 201);
    }

    /**
     * List all expenses for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $expenses = Auth::user()->expenses()->with('tags')->get(); // Load expenses with tags
        return response()->json($expenses);
    }

    /**
     * Show a specific expense.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $expense = Auth::user()->expenses()->with('tags')->find($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        return response()->json($expense);
    }

    /**
     * Update a specific expense.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'description' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric|min:0',
            'date' => 'sometimes|date',
            'tags' => 'nullable|array', // Optional tags to update
            'tags.*' => 'exists:tags,id', // Ensure tags exist
        ]);

        // Find the expense
        $expense = Auth::user()->expenses()->find($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        // Update the expense
        $expense->update($request->only(['description', 'amount', 'date']));

        // Sync tags if provided
        if ($request->has('tags')) {
            $expense->tags()->sync($request->tags);
        }

        return response()->json([
            'message' => 'Expense updated successfully',
            'expense' => $expense->load('tags'), // Load tags for the response
        ]);
    }

    /**
     * Delete a specific expense.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $expense = Auth::user()->expenses()->find($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully']);
    }

    /**
     * Attach tags to a specific expense.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function attachTags(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id', // Ensure tags exist
        ]);

        // Find the expense
        $expense = Auth::user()->expenses()->find($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        // Attach tags
        $expense->tags()->attach($request->tags);

        return response()->json([
            'message' => 'Tags attached successfully',
            'expense' => $expense->load('tags'), // Load tags for the response
        ]);
    }
}

?>