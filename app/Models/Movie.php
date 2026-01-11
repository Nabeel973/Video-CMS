<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{


    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'genre_id',
        'release',
        'rating',
        'duration',
        'video_source',
        'category_id',
        'image',
        'video_link',
        'video_file',
        'details',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'rating' => 'decimal:1',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function movieTags()
    {
        return $this->hasMany(MovieTag::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'movie_tags');
    }

    public function movieCasts()
    {
        return $this->hasMany(MovieCast::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
