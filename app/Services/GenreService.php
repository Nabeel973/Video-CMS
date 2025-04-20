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

    public function getPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Genre::with(['createdBy:id,name', 'updatedBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
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
        return validator($data, [
            'name' => 'required|string|max:255|unique:genres,name,' . ($data['id'] ?? ''),
            'status' => 'required|in:active,inactive',
        ])->validate();
    }
} 