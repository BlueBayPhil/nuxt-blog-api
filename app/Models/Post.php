<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Class Post
 * @package App\Models
 * @property string title
 * @property string slug
 * @property string description
 * @property string content
 * @property boolean published
 * @property Carbon published_at
 * @property Carbon created_at
 * @property Carbon updated_at
 * @method static paginate()
 */
class Post extends Model
{
    use HasFactory, HasSlug;

    protected $table = 'posts';
    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'published',
        'published_at'
    ];

    protected $casts = [
        'published' => 'bool'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'published_at'
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsto('slug');
    }

//    public function getRouteKeyName(): string
//    {
//        return 'slug';
//    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
