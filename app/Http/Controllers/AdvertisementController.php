<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Services\AdvertisementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AdvertisementController extends Controller
{
    protected $advertisementService;

    public function __construct(AdvertisementService $advertisementService)
    {
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


    // public function store(Request $request): JsonResponse
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'name' => 'required|string|max:255|unique:advertisements,name',
    //             'type' => 'required|in:text,image',
    //             'description' => 'nullable|string|required_if:type,text',
    //             'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048|required_if:type,image',
    //             'status' => 'required|in:active,inactive',
    //         ]);

    //         $advertisement = $this->advertisementService->createAdvertisement($validatedData, $request->file('image'));

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Advertisement created successfully',
    //             'data' => $advertisement
    //         ], 201);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $e->errors()
    //         ], 422);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to create advertisement',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function store(Request $request): JsonResponse
{
    try {
        // Validate file upload separately
   
        $advertisement = $this->advertisementService->createAdvertisement(
            $request->all(), 
            $request->file('image')
        );

        return response()->json([
            'success' => true,
            'message' => 'Advertisement created successfully',
            'data' => $advertisement
        ], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        // Log the full error for debugging
        \Log::error('Advertisement creation error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to create advertisement',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function update(Request $request, $id): JsonResponse
{
    try {
        // First validate the file upload rules at controller level
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Debug: Log the incoming request data
        \Log::info('Advertisement update request', [
            'id' => $id,
            'request_data' => $request->all(),
            'has_file' => $request->hasFile('image')
        ]);

        $advertisement = $this->advertisementService->updateAdvertisement(
            $id, 
            $request->all(), 
            $request->file('image')
        );

        if (!$advertisement) {
            return response()->json([
                'success' => false,
                'message' => 'Advertisement not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Advertisement updated successfully',
            'data' => $advertisement
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        // Log the full error for debugging
        \Log::error('Advertisement update error', [
            'id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to update advertisement',
            'error' => $e->getMessage()
        ], 500);
    }
}


    public function show($id): JsonResponse
    {
        try {
            $advertisement = $this->advertisementService->getAdvertisement($id);
            
            if (!$advertisement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Advertisement not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $advertisement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch advertisement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // public function update(Request $request, $id): JsonResponse
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'name' => ['required', 'string', 'max:255', Rule::unique('advertisements')->ignore($id)],
    //             'type' => 'required|in:text,image',
    //             'description' => 'nullable|string|required_if:type,text',
    //             'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //             'status' => 'required|in:active,inactive',
    //         ]);

    //         $advertisement = $this->advertisementService->updateAdvertisement($id, $validatedData, $request->file('image'));

    //         if (!$advertisement) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Advertisement not found'
    //             ], 404);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Advertisement updated successfully',
    //             'data' => $advertisement
    //         ]);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $e->errors()
    //         ], 422);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to update advertisement',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->advertisementService->deleteAdvertisement($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Advertisement not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Advertisement deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete advertisement',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}