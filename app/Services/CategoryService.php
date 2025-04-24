<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService
{
    public function getAll(): Collection
    {
        return Category::with(['createdBy:id,name', 'updatedBy:id,name'])->get();
    }

    public function getPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Category::with(['createdBy:id,name', 'updatedBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): Category
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        
        return Category::create($data);
    }

    public function update(Category $category, array $data): bool
    {
        $data['updated_by'] = Auth::id();
        
        return $category->update($data);
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    public function findById(int $id): ?Category
    {
        return Category::with(['createdBy:id,name', 'updatedBy:id,name'])->find($id);
    }

    public function validateData(array $data): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ];

        // If we have an ID, it's an update, so exclude current record from unique check
        if (isset($data['id'])) {
            $rules['name'] .= '|unique:categories,name,' . $data['id'] . ',id,deleted_at,NULL';
        } else {
            $rules['name'] .= '|unique:categories,name,NULL,id,deleted_at,NULL';
        }

        return validator($data, $rules)->validate();
    }
} 