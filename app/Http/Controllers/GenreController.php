<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Services\GenreService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GenreController extends Controller
{
    protected $genreService;

    public function __construct(GenreService $genreService)
    {
        $this->genreService = $genreService;
    }

    public function index()
    {
        return view('admin.genres.index');
    }

    public function list(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        
        $genres = $this->genreService->getPaginated($perPage, $search);
        
        return response()->json([
            'data' => $genres->items(),
            'meta' => [
                'current_page' => $genres->currentPage(),
                'last_page' => $genres->lastPage(),
                'per_page' => $genres->perPage(),
                'total' => $genres->total()
            ]
        ]);
    }

    public function show(Genre $genre): JsonResponse
    {
        return response()->json([
            'data' => $genre->load(['createdBy:id,name', 'updatedBy:id,name'])
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $this->genreService->validateData($request->all());
            $genre = $this->genreService->create($validatedData);

            return response()->json([
                'message' => 'Genre created successfully',
                'data' => $genre
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating genre',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function update(Request $request, Genre $genre): JsonResponse
    {
        try {
            $validatedData = $this->genreService->validateData($request->all());
            $this->genreService->update($genre, $validatedData);

            return response()->json([
                'message' => 'Genre updated successfully',
                'data' => $genre->fresh(['createdBy:id,name', 'updatedBy:id,name'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating genre',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Genre $genre): JsonResponse
    {
        try {
            $this->genreService->delete($genre);

            return response()->json([
                'message' => 'Genre deleted successfully',
                'data' => $genre->load(['createdBy:id,name', 'updatedBy:id,name'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting genre',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
