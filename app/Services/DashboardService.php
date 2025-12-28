<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\User;
use App\Models\Genre;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Advertisement;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getStats(): array
    {
        return [
            'total_movies' => Movie::count(),
            'total_users' => User::count(),
            'total_genres' => Genre::count(),
            'total_categories' => Category::count(),
            'total_tags' => Tag::count(),
            'total_advertisements' => Advertisement::count(),
            'active_movies' => Movie::where('status', 'active')->count(),
            'inactive_movies' => Movie::where('status', 'inactive')->count(),
            'recent_movies_count' => Movie::where('created_at', '>=', now()->subDays(7))->count(),
            'movies_by_status' => [
                'active' => Movie::where('status', 'active')->count(),
                'inactive' => Movie::where('status', 'inactive')->count(),
            ],
            'movies_by_category' => $this->getMoviesByCategory(),
            'movies_by_genre' => $this->getMoviesByGenre(),
            'recent_movies' => $this->getRecentMovies(5),
            'top_categories' => $this->getTopCategories(5),
            'top_genres' => $this->getTopGenres(5),
            'monthly_movies' => $this->getMonthlyMovies(),
        ];
    }

    public function getRecentMovies(int $limit = 5): array
    {
        return Movie::with(['genre:id,name', 'category:id,name', 'tags:id,name'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($movie) {
                return [
                    'id' => $movie->id,
                    'name' => $movie->name,
                    'release' => $movie->release,
                    'status' => $movie->status,
                    'genre' => $movie->genre ? $movie->genre->name : null,
                    'category' => $movie->category ? $movie->category->name : null,
                    'tags' => $movie->tags->pluck('name')->toArray(),
                    'image' => $movie->image,
                    'created_at' => $movie->created_at->format('Y-m-d H:i:s'),
                ];
            })
            ->toArray();
    }

    public function getMoviesByCategory(): array
    {
        return Movie::select('categories.name', DB::raw('count(movies.id) as count'))
            ->join('categories', 'movies.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.name')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    public function getMoviesByGenre(): array
    {
        return Movie::select('genres.name', DB::raw('count(movies.id) as count'))
            ->join('genres', 'movies.genre_id', '=', 'genres.id')
            ->groupBy('genres.id', 'genres.name')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    public function getTopCategories(int $limit = 5): array
    {
        return Movie::select('categories.name', DB::raw('count(movies.id) as count'))
            ->join('categories', 'movies.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    public function getTopGenres(int $limit = 5): array
    {
        return Movie::select('genres.name', DB::raw('count(movies.id) as count'))
            ->join('genres', 'movies.genre_id', '=', 'genres.id')
            ->groupBy('genres.id', 'genres.name')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    public function getMonthlyMovies(): array
    {
        return Movie::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }
}

