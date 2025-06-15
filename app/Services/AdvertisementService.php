<?php

namespace App\Services;

use App\Models\Advertisement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdvertisementService
{
    public function getAll(): Collection
    {
        return Advertisement::with(['creator:id,name', 'updater:id,name'])->get();
    }

    public function getPaginated(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        $query = Advertisement::with(['creator:id,name', 'updater:id,name']);

        // Add search functionality
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('text', 'LIKE', "%{$search}%")
                  ->orWhere('type', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%")
                  ->orWhereHas('creator', function ($subQuery) use ($search) {
                      $subQuery->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('updater', function ($subQuery) use ($search) {
                      $subQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): Advertisement
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        
        return Advertisement::create($data);
    }

    public function update(Advertisement $advertisement, array $data): bool
    {
        $data['updated_by'] = Auth::id();
        
        return $advertisement->update($data);
    }

    public function delete(Advertisement $advertisement): bool
    {
        return $advertisement->delete();
    }

    public function findById(int $id): ?Advertisement
    {
        return Advertisement::with(['creator:id,name', 'updater:id,name'])->find($id);
    }

    public function validateData(array $data): array
    {
        $rules = [
            'text' => 'required|string',
            'type' => 'required|string|in:banner,popup,sidebar,header,footer,inline,video,text',
            'status' => 'required|in:active,inactive',
        ];

        return validator($data, $rules)->validate();
    }

    /**
     * Get advertisement types
     */
    public function getAdvertisementTypes(): array
    {
        return [
            'banner' => 'Banner',
            'popup' => 'Popup',
            'sidebar' => 'Sidebar',
            'header' => 'Header',
            'footer' => 'Footer',
            'inline' => 'Inline',
            'video' => 'Video',
            'text' => 'Text',
        ];
    }
}