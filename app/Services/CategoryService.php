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
        return validator($data, [
            'name' => 'required|string|max:255|unique:categories,name,' . ($data['id'] ?? ''),
            'status' => 'required|in:active,inactive',
        ])->validate();
    }
} 