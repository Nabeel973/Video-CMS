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
        $rules = [
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ];

        // If we have an ID, it's an update, so exclude current record from unique check
        if (isset($data['id'])) {
            $rules['name'] .= '|unique:tags,name,' . $data['id'] . ',id,deleted_at,NULL';
        } else {
            $rules['name'] .= '|unique:tags,name,NULL,id,deleted_at,NULL';
        }

        return validator($data, $rules)->validate();
    }
} 