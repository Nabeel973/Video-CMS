<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory;

    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'group'
    ];

    public function getCreatedByAttribute()
    {
        return User::find($this->created_by_id);
    }

    public function getUpdatedByAttribute()
    {
        return User::find($this->updated_by_id);
    }
}