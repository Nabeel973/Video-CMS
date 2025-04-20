<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index()
    {
        return view('admin.tags.index');
    }

    public function list(Request $request): JsonResponse
    {
        $tags = $this->tagService->getPaginated($request->input('per_page', 10));
        
        return response()->json([
            'data' => $tags->items(),
            'meta' => [
                'current_page' => $tags->currentPage(),
                'last_page' => $tags->lastPage(),
                'per_page' => $tags->perPage(),
                'total' => $tags->total()
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $this->tagService->validateData($request->all());
            $tag = $this->tagService->create($validatedData);

            return response()->json([
                'message' => 'Tag created successfully',
                'data' => $tag
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating tag',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function update(Request $request, Tag $tag): JsonResponse
    {
        try {
            $validatedData = $this->tagService->validateData($request->all());
            $this->tagService->update($tag, $validatedData);

            return response()->json([
                'message' => 'Tag updated successfully',
                'data' => $tag->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating tag',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Tag $tag): JsonResponse
    {
        try {
            $this->tagService->delete($tag);

            return response()->json([
                'message' => 'Tag deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting tag',
                'error' => $e->getMessage()
            ], 422);
        }
    }
} 