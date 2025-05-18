<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

    protected $fillable = [
        'name',
        'guard_name',
        'status',
        'description'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function getCreatedByAttribute()
    {
        return User::find($this->created_by_id);
    }

    public function getUpdatedByAttribute()
    {
        return User::find($this->updated_by_id);
    }

    // public function permissions()
    // {
    //     return $this->belongsToMany(Permission::class, 'role_has_permissions');
    // }

}