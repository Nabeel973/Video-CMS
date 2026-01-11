<?php

namespace App\Services;

use App\Models\Movie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MovieService
{
    public function getAll(): Collection
    {
        return Movie::with([
            'createdBy:id,name',
            'updatedBy:id,name',
            'tags:id,name',
            'movieCasts:id,movie_id,info,image'
        ])->get();
    }

    public function getPaginated(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        $query = Movie::with([
            'createdBy:id,name',
            'updatedBy:id,name',
            'tags:id,name',
            'movieCasts:id,movie_id,info,image'
        ]);

        // Add search functionality
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                //   ->orWhere('status', 'LIKE', "%{$search}%")
                  ->orWhereHas('createdBy', function ($subQuery) use ($search) {
                      $subQuery->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('updatedBy', function ($subQuery) use ($search) {
                      $subQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): Movie
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        
        return Movie::create($data);
    }

    public function update(Movie $Movie, array $data): bool
    {
        $data['updated_by'] = Auth::id();
        
        return $Movie->update($data);
    }

    public function delete(Movie $Movie): bool
    {
        return $Movie->delete();
    }

    public function findById(int $id): ?Movie
    {
        return Movie::with([
            'createdBy:id,name',
            'updatedBy:id,name',
            'tags:id,name',
            'movieCasts:id,movie_id,info,image'
        ])->find($id);
    }

    public function validateData(array $data, $request = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'tags' => 'nullable|array',
            'genre_id' => 'required|exists:genres,id',
            'release' => 'required|string|max:255',
            'rating' => 'required|numeric|min:0|max:5|regex:/^\d+(\.\d{1})?$/',
            'duration' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    // Check if duration is empty
                    if (empty($value) || trim($value) === '') {
                        $fail('The duration field is required.');
                        return;
                    }
                    
                    // Check format
                    if (!preg_match('/^\d+h\s*\d+m$/', $value)) {
                        $fail('The duration must be in the format Xh Ym (e.g., 2h 30m).');
                        return;
                    }
                    
                    // Check if duration is not "0h 0m"
                    if (preg_match('/^(\d+)h\s*(\d+)m$/', $value, $matches)) {
                        $hours = (int)$matches[1];
                        $minutes = (int)$matches[2];
                        if ($hours === 0 && $minutes === 0) {
                            $fail('The duration must be greater than 0h 0m.');
                        }
                    }
                },
            ],
            'video_source' => 'required|in:upload,link',
            'video_link' => 'nullable|string|url|max:500',
            'category_id' => 'required|exists:categories,id',
            'details' => 'nullable|string',
            'cast_info' => 'nullable|array',
            'status' => 'required|in:active,inactive',
        ];
        
        // Conditional validation based on video_source
        if (isset($data['video_source'])) {
            if ($data['video_source'] === 'link') {
                $rules['video_link'] = 'required|string|url|max:500';
            } else if ($data['video_source'] === 'upload') {
                // video_file validation is handled separately for file uploads
            }
        }

        // File validation rules - only apply when files are actually being uploaded
        $hasImageFile = $request && $request->hasFile('image');
        $hasVideoFile = $request && $request->hasFile('video_file');
        
        if ($hasImageFile) {
            $rules['image'] = 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:10240';
        }
        // If no image file is being uploaded, don't validate image field (allow existing path to pass through)
        
        if ($hasVideoFile) {
            $rules['video_file'] = 'nullable|file|mimes:mp4,mov,avi,wmv|max:512000';
        }
        // If no video file is being uploaded, don't validate video_file field (allow existing path to pass through)

        // If we have an ID, it's an update, so exclude current record from unique check
        if (isset($data['id'])) {
            $rules['name'] .= '|unique:Movies,name,' . $data['id'] . ',id,deleted_at,NULL';
        } else {
            $rules['name'] .= '|unique:Movies,name,NULL,id,deleted_at,NULL';
        }

        return validator($data, $rules)->validate();
    }
}
