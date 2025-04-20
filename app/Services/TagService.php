<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TagService
{
    public function getAll(): Collection
    {
        return Tag::with(['createdBy:id,name', 'updatedBy:id,name'])->get();
    }

    public function getPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Tag::with(['createdBy:id,name', 'updatedBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): Tag
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        
        return Tag::create($data);
    }

    public function update(Tag $tag, array $data): bool
    {
        $data['updated_by'] = Auth::id();
        
        return $tag->update($data);
    }

    public function delete(Tag $tag): bool
    {
        return $tag->delete();
    }

    public function findById(int $id): ?Tag
    {
        return Tag::with(['createdBy:id,name', 'updatedBy:id,name'])->find($id);
    }

    public function validateData(array $data): array
    {
        return validator($data, [
            'name' => 'required|string|max:255|unique:tags,name,' . ($data['id'] ?? ''),
            'status' => 'required|in:active,inactive',
        ])->validate();
    }
} 