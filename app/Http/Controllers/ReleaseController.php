<?php

namespace App\Http\Controllers;

use App\Models\Release;
use App\Services\ReleaseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReleaseController extends Controller
{
    protected $releaseService;

    public function __construct(ReleaseService $releaseService)
    {
        $this->releaseService = $releaseService;
    }

    public function index()
    {
        return view('admin.releases.index');
    }

    public function list(Request $request): JsonResponse
    {
        $releases = $this->releaseService->getPaginated($request->input('per_page', 10));
        
        return response()->json([
            'data' => $releases->items(),
            'meta' => [
                'current_page' => $releases->currentPage(),
                'last_page' => $releases->lastPage(),
                'per_page' => $releases->perPage(),
                'total' => $releases->total()
            ]
        ]);
    }

    public function show(Release $release): JsonResponse
    {
        return response()->json([
            'data' => $release->load(['createdBy:id,name', 'updatedBy:id,name'])
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $this->releaseService->validateData($request->all());
            $release = $this->releaseService->create($validatedData);

            return response()->json([
                'message' => 'Release created successfully',
                'data' => $release
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating release',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function update(Request $request, Release $release): JsonResponse
    {
        try {
            $validatedData = $this->releaseService->validateData($request->all());
            $this->releaseService->update($release, $validatedData);

            return response()->json([
                'message' => 'Release updated successfully',
                'data' => $release->fresh(['createdBy:id,name', 'updatedBy:id,name'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating release',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Release $release): JsonResponse
    {
        try {
            $this->releaseService->delete($release);

            return response()->json([
                'message' => 'Release deleted successfully',
                'data' => $release->load(['createdBy:id,name', 'updatedBy:id,name'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting release',
                'error' => $e->getMessage()
            ], 422);
        }
    }
} 