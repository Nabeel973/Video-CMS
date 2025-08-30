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
        $this->middleware('permission:user.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:user.create', ['only' => ['store']]);
        $this->middleware('permission:user.edit', ['only' => ['update']]);
        $this->middleware('permission:user.delete', ['only' => ['destroy']]);
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
}
