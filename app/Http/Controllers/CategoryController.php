<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        return view('admin.categories.index');
    }

    public function list(Request $request): JsonResponse
    {
        $categories = $this->categoryService->getPaginated($request->input('per_page', 10));
        
        return response()->json([
            'data' => $categories->items(),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total()
            ]
        ]);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'data' => $category->load(['createdBy:id,name', 'updatedBy:id,name'])
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $this->categoryService->validateData($request->all());
            $category = $this->categoryService->create($validatedData);

            return response()->json([
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating category',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        try {
            $validatedData = $this->categoryService->validateData($request->all());
            $this->categoryService->update($category, $validatedData);

            return response()->json([
                'message' => 'Category updated successfully',
                'data' => $category->fresh(['createdBy:id,name', 'updatedBy:id,name'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating category',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Category $category): JsonResponse
    {
        try {
            $this->categoryService->delete($category);

            return response()->json([
                'message' => 'Category deleted successfully',
                'data' => $category->load(['createdBy:id,name', 'updatedBy:id,name'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting category',
                'error' => $e->getMessage()
            ], 422);
        }
    }
} 