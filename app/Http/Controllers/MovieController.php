<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MovieService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MovieController extends Controller
{
    protected $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->middleware('auth:api');
        $this->middleware('permission:movie.view', ['only' => ['index', 'show', 'list']]);
        $this->middleware('permission:movie.create', ['only' => ['store']]);
        $this->middleware('permission:movie.edit', ['only' => ['update']]);
        $this->middleware('permission:movie.delete', ['only' => ['destroy']]);
        $this->movieService = $movieService;
    }

     public function index()
    {   
        return view('admin.movies.index');
    }

    public function list(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        
        $movies = $this->movieService->getPaginated($perPage, $search);
        
        return response()->json([
            'data' => $movies->items(),
            'meta' => [
            'current_page' => $movies->currentPage(),
            'last_page' => $movies->lastPage(),
            'per_page' => $movies->perPage(),
            'total' => $movies->total()
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $this->movieService->validateData($request->all());
            
            // Handle file uploads
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('movies/images', 'public');
                $validatedData['image'] = $imagePath;
            }

            if ($request->hasFile('video_file')) {
                $video = $request->file('video_file');
                $videoPath = $video->store('movies/videos', 'public');
                $validatedData['video_file'] = $videoPath;
            }

            // Remove tags and cast_info from validated data as they'll be handled separately
            $tags = $validatedData['tags'] ?? [];
            $castInfo = $request->input('cast_info', []);
            unset($validatedData['tags'], $validatedData['cast_info']);

            // Create the movie
            $movie = $this->movieService->create($validatedData);

            // Handle tags relationship
            if (!empty($tags)) {
                foreach ($tags as $tagId) {
                    \App\Models\MovieTag::create([
                        'movie_id' => $movie->id,
                        'tag_id' => $tagId
                    ]);
                }
            }

            // Handle cast info relationship
            if (!empty($castInfo)) {
                foreach ($castInfo as $index => $cast) {
                    $castData = [
                        'movie_id' => $movie->id,
                        'info' => $cast['name'] ?? '',
                    ];
                    
                    if ($request->hasFile("cast_info.{$index}.image")) {
                        $castImage = $request->file("cast_info.{$index}.image");
                        $castImagePath = $castImage->store('movies/cast', 'public');
                        $castData['image'] = $castImagePath;
                    } elseif (isset($cast['existing_image'])) {
                        $castData['image'] = $cast['existing_image'];
                    }
                    
                    \App\Models\MovieCast::create($castData);
                }
            }
            
            return response()->json([
                'message' => 'Movie created successfully',
                'data' => $movie->load(['tags', 'movieCasts'])
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create movie: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $movie = $this->movieService->findById($id);
            
            if (!$movie) {
                return response()->json([
                    'error' => 'Movie not found'
                ], 404);
            }

            // Load relationships
            $movie->load(['tags', 'movieCasts']);
            
            // Format tags for frontend (array of IDs)
            $movie->tag_ids = $movie->tags->pluck('id')->toArray();
            
            // Format cast info for frontend
            $movie->cast_info = $movie->movieCasts->map(function($cast) {
                return [
                    'name' => $cast->info,
                    'image' => $cast->image,
                    'existing_image' => $cast->image
                ];
            })->toArray();
            
            return response()->json([
                'data' => $movie
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch movie'
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $movie = $this->movieService->findById($id);
            
            if (!$movie) {
                return response()->json([
                    'error' => 'Movie not found'
                ], 404);
            }

            $validatedData = $this->movieService->validateData(array_merge($request->all(), ['id' => $id]));
            
            // Handle file uploads
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('movies/images', 'public');
                $validatedData['image'] = $imagePath;
                
                // Delete old image if exists
                if ($movie->image && \Storage::disk('public')->exists($movie->image)) {
                    \Storage::disk('public')->delete($movie->image);
                }
            }

            if ($request->hasFile('video_file')) {
                $video = $request->file('video_file');
                $videoPath = $video->store('movies/videos', 'public');
                $validatedData['video_file'] = $videoPath;
                
                // Delete old video if exists
                if ($movie->video_file && \Storage::disk('public')->exists($movie->video_file)) {
                    \Storage::disk('public')->delete($movie->video_file);
                }
            }

            // Remove tags and cast_info from validated data as they'll be handled separately
            $tags = $validatedData['tags'] ?? [];
            $castInfo = $request->input('cast_info', []);
            unset($validatedData['tags'], $validatedData['cast_info']);

            // Update the movie
            $this->movieService->update($movie, $validatedData);

            // Handle tags relationship - delete existing and create new
            \App\Models\MovieTag::where('movie_id', $movie->id)->delete();
            if (!empty($tags)) {
                foreach ($tags as $tagId) {
                    \App\Models\MovieTag::create([
                        'movie_id' => $movie->id,
                        'tag_id' => $tagId
                    ]);
                }
            }

            // Handle cast info relationship - delete existing and create new
            // First, delete old cast images
            $oldCasts = \App\Models\MovieCast::where('movie_id', $movie->id)->get();
            foreach ($oldCasts as $oldCast) {
                if ($oldCast->image && \Storage::disk('public')->exists($oldCast->image)) {
                    \Storage::disk('public')->delete($oldCast->image);
                }
            }
            \App\Models\MovieCast::where('movie_id', $movie->id)->delete();
            
            if (!empty($castInfo)) {
                foreach ($castInfo as $index => $cast) {
                    $castData = [
                        'movie_id' => $movie->id,
                        'info' => $cast['name'] ?? '',
                    ];
                    
                    if ($request->hasFile("cast_info.{$index}.image")) {
                        $castImage = $request->file("cast_info.{$index}.image");
                        $castImagePath = $castImage->store('movies/cast', 'public');
                        $castData['image'] = $castImagePath;
                    } elseif (isset($cast['existing_image'])) {
                        $castData['image'] = $cast['existing_image'];
                    }
                    
                    \App\Models\MovieCast::create($castData);
                }
            }
            
            return response()->json([
                'message' => 'Movie updated successfully',
                'data' => $movie->fresh()->load(['tags', 'movieCasts'])
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update movie: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $movie = $this->movieService->findById($id);
            
            if (!$movie) {
                return response()->json([
                    'error' => 'Movie not found'
                ], 404);
            }

            $this->movieService->delete($movie);
            
            return response()->json([
                'message' => 'Movie deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete movie'
            ], 500);
        }
    }
}
