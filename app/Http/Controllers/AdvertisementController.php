<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advertisement;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\AdvertisementService;

class AdvertisementController extends Controller
{
    protected $advertisementService;

    public function __construct(AdvertisementService $advertisementService)
    {
        $this->middleware('auth:api');
        $this->middleware('permission:advertisement.view', ['only' => ['index', 'show', 'list']]);
        $this->middleware('permission:advertisement.create', ['only' => ['store']]);
        $this->middleware('permission:advertisement.edit', ['only' => ['update']]);
        $this->middleware('permission:advertisement.delete', ['only' => ['destroy']]);
        $this->advertisementService = $advertisementService;
    }

    public function index()
    {
        return view('admin.advertisements.index');
    }

    public function list(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        
        $advertisements = $this->advertisementService->getPaginated($perPage, $search);
        
        return response()->json([
            'data' => $advertisements->items(),
            'meta' => [
                'current_page' => $advertisements->currentPage(),
                'last_page' => $advertisements->lastPage(),
                'per_page' => $advertisements->perPage(),
                'total' => $advertisements->total()
            ]
        ]);
    }

    public function show(Advertisement $advertisement): JsonResponse
    {
        return response()->json([
            'data' => $advertisement->load(['creator:id,name', 'updater:id,name'])
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $this->advertisementService->validateData($request->all());
            $advertisement = $this->advertisementService->create($validatedData);

            return response()->json([
                'message' => 'Advertisement created successfully',
                'data' => $advertisement
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating advertisement',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function update(Request $request, Advertisement $advertisement): JsonResponse
    {
        try {
            $validatedData = $this->advertisementService->validateData($request->all());
            $this->advertisementService->update($advertisement, $validatedData);

            return response()->json([
                'message' => 'Advertisement updated successfully',
                'data' => $advertisement->fresh(['creator:id,name', 'updater:id,name'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating advertisement',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Advertisement $advertisement): JsonResponse
    {
        try {
            $this->advertisementService->delete($advertisement);

            return response()->json([
                'message' => 'Advertisement deleted successfully',
                'data' => $advertisement->load(['creator:id,name', 'updater:id,name'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting advertisement',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get advertisement types
     */
    public function getTypes(): JsonResponse
    {
        try {
            $types = $this->advertisementService->getAdvertisementTypes();

            return response()->json([
                'success' => true,
                'data' => $types
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch advertisement types',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}