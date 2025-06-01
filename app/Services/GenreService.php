<?php

namespace App\Services;

use App\Models\Genre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GenreService
{
    public function getAll(): Collection
    {
        return Genre::with(['createdBy:id,name', 'updatedBy:id,name'])->get();
    }

    public function getPaginated(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        $query = Genre::with(['createdBy:id,name', 'updatedBy:id,name']);

        // Add search functionality
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%")
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

    public function create(array $data): Genre
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        
        return Genre::create($data);
    }

    public function update(Genre $genre, array $data): bool
    {
        $data['updated_by'] = Auth::id();
        
        return $genre->update($data);
    }

    public function delete(Genre $genre): bool
    {
        return $genre->delete();
    }

    public function findById(int $id): ?Genre
    {
        return Genre::with(['createdBy:id,name', 'updatedBy:id,name'])->find($id);
    }

    public function validateData(array $data): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ];

        // If we have an ID, it's an update, so exclude current record from unique check
        if (isset($data['id'])) {
            $rules['name'] .= '|unique:genres,name,' . $data['id'] . ',id,deleted_at,NULL';
        } else {
            $rules['name'] .= '|unique:genres,name,NULL,id,deleted_at,NULL';
        }

        return validator($data, $rules)->validate();
    }
}
