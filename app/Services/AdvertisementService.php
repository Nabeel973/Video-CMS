<?php

namespace App\Services;

use App\Models\Advertisement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

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
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('type', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
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

    public function createAdvertisement(array $data, ?UploadedFile $imageFile = null): Advertisement
    {
        try {
            // Validate data first
            $validatedData = $this->validateCreateData($data);

            // Handle file upload
            if ($imageFile && $imageFile->isValid()) {
                $imageData = $this->handleFileUpload($imageFile);
                $validatedData = array_merge($validatedData, $imageData);
            }

            // Set audit fields
            $validatedData['created_by'] = Auth::id();
            $validatedData['updated_by'] = Auth::id();

            return Advertisement::create($validatedData);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exception so controller can handle it
            throw $e;
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Advertisement creation failed', [
                'data' => $data,
                'error' => $e->getMessage(),
                'validated_data' => $validatedData ?? null
            ]);
            throw $e;
        }
    }

    public function update(Advertisement $advertisement, array $data): bool
    {
        $data['updated_by'] = Auth::id();
        
        return $advertisement->update($data);
    }

    public function updateAdvertisement(int $id, array $data, ?UploadedFile $imageFile = null): ?Advertisement
    {
        try {
            $advertisement = $this->findById($id);
            
            if (!$advertisement) {
                return null;
            }

            // Validate data first
            $validatedData = $this->validateUpdateData($data, $id);

            // Handle file upload
            if ($imageFile && $imageFile->isValid()) {
                // Delete old image if exists
                if ($advertisement->image_path && Storage::disk('public')->exists($advertisement->image_path)) {
                    Storage::disk('public')->delete($advertisement->image_path);
                }

                $imageData = $this->handleFileUpload($imageFile);
                $validatedData = array_merge($validatedData, $imageData);
            }

            // Set audit fields
            $validatedData['updated_by'] = Auth::id();

            $advertisement->update($validatedData);
            
            return $advertisement->fresh(['creator:id,name', 'updater:id,name']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exception so controller can handle it
            throw $e;
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Advertisement update failed', [
                'id' => $id,
                'data' => $data,
                'error' => $e->getMessage(),
                'validated_data' => $validatedData ?? null
            ]);
            throw $e;
        }
    }

    public function delete(Advertisement $advertisement): bool
    {
        return $advertisement->delete();
    }

    public function deleteAdvertisement(int $id): bool
    {
        $advertisement = $this->findById($id);
        
        if (!$advertisement) {
            return false;
        }

        // Delete associated image file
        if ($advertisement->image_path && Storage::disk('public')->exists($advertisement->image_path)) {
            Storage::disk('public')->delete($advertisement->image_path);
        }

        return $advertisement->delete();
    }

    public function findById(int $id): ?Advertisement
    {
        return Advertisement::with(['creator:id,name', 'updater:id,name'])->find($id);
    }

    public function getAdvertisement(int $id): ?Advertisement
    {
        return $this->findById($id);
    }

    /**
     * Validate data for creating advertisement
     */
    public function validateCreateData(array $data): array
    {
        $rules = [
            'name' => 'required|string|max:255|unique:advertisements,name',
            'type' => 'required|string|in:text,image',
            'description' => 'nullable|string|required_if:type,text',
            'status' => 'required|in:active,inactive',
        ];

        // Don't validate image file here as it's handled separately
        if (isset($data['image']) && !is_file($data['image'])) {
            unset($data['image']);
        }

        return validator($data, $rules)->validate();
    }

    /**
     * Validate data for updating advertisement
     */
    public function validateUpdateData(array $data, int $id): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('advertisements')->ignore($id)],
            'type' => 'required|string|in:text,image',
            'description' => 'nullable|string|required_if:type,text',
            'status' => 'required|in:active,inactive',
        ];

        // Don't validate image file here as it's handled separately
        if (isset($data['image']) && !is_file($data['image'])) {
            unset($data['image']);
        }

        return validator($data, $rules)->validate();
    }

    /**
     * Handle file upload
     */
    private function handleFileUpload(UploadedFile $file): array
    {
        $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $imagePath = $file->storeAs('advertisements', $imageName, 'public');
        
        return [
            'image_path' => $imagePath,
            'image_url' => asset('storage/' . $imagePath)
        ];
    }

    /**
     * Get advertisement types
     */
    public function getAdvertisementTypes(): array
    {
        return [
            'text' => 'Text',
            'image' => 'Image',
        ];
    }
}