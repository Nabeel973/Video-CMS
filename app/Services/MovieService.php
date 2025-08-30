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
        return Movie::with(['createdBy:id,name', 'updatedBy:id,name'])->get();
    }

    public function getPaginated(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        $query = Movie::with(['createdBy:id,name', 'updatedBy:id,name']);

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
        return Movie::with(['createdBy:id,name', 'updatedBy:id,name'])->find($id);
    }

    public function validateData(array $data): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            // 'status' => 'required|in:active,inactive',
        ];

        // If we have an ID, it's an update, so exclude current record from unique check
        if (isset($data['id'])) {
            $rules['name'] .= '|unique:Movies,name,' . $data['id'] . ',id,deleted_at,NULL';
        } else {
            $rules['name'] .= '|unique:Movies,name,NULL,id,deleted_at,NULL';
        }

        return validator($data, $rules)->validate();
    }
}
