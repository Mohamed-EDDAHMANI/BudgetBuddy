<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;


class TagController extends Controller
{
    /**
     * Create a new tag.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Gate::authorize('create-tag');
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
        ]);

        // Get authenticated user
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Extract user name from the token abilities (if set)
        $token = $user->currentAccessToken();
        // $tokenName = $token ? ($token->abilities[0] ?? $user->name) : $user->name;
        $tokenName = $token->abilities[0];

        // Create the tag with the user’s name
        $tag = Tag::create([
            'name' => $request->name,
            'created_by' => $tokenName, // Store the user’s name in the tag
        ]);

        return response()->json([
            'message' => 'Tag created successfully',
            'tag' => $tag,
            'created_by' => $tokenName,
        ], 201);
    }

    /**
     * List all tags.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $tags = Tag::all();
        return response()->json($tags);
    }

    /**
     * Show a specific tag.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        return response()->json($tag);
    }

    /**
     * Update a specific tag.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $id,
        ]);

        // Find the tag
        $tag = Tag::find($id);
        

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        // Update the tag
        $tag->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Tag updated successfully',
            'tag' => $tag,
        ]);
    }

    /**
     * Delete a specific tag.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully']);
    }
}