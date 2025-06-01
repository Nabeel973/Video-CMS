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

    public function getPaginated(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        $query = Release::with(['createdBy:id,name', 'updatedBy:id,name']);

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
        $rules = [
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ];

        // If we have an ID, it's an update, so exclude current record from unique check
        if (isset($data['id'])) {
            $rules['name'] .= '|unique:releases,name,' . $data['id'] . ',id,deleted_at,NULL';
        } else {
            $rules['name'] .= '|unique:releases,name,NULL,id,deleted_at,NULL';
        }

        return validator($data, $rules)->validate();
    }
}
