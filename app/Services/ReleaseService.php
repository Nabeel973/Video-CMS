<?php

namespace App\Services;

use App\Models\Release;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ReleaseService
{
    public function getAll(): Collection
    {
        return Release::with(['createdBy:id,name', 'updatedBy:id,name'])->get();
    }

    public function getPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Release::with(['createdBy:id,name', 'updatedBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): Release
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        
        return Release::create($data);
    }

    public function update(Release $release, array $data): bool
    {
        $data['updated_by'] = Auth::id();
        
        return $release->update($data);
    }

    public function delete(Release $release): bool
    {
        return $release->delete();
    }

    public function findById(int $id): ?Release
    {
        return Release::with(['createdBy:id,name', 'updatedBy:id,name'])->find($id);
    }

    public function validateData(array $data): array
    {
        return validator($data, [
            'name' => 'required|string|max:255|unique:releases,name,' . ($data['id'] ?? ''),
            'status' => 'required|in:active,inactive',
        ])->validate();
    }
} 