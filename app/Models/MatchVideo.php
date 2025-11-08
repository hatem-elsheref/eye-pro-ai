<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchVideo extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'matches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'status',
        'video_url',
        'video_path',
        'description',
        'tags',
        'duration',
        'file_size',
        'storage_disk',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the match.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the predictions for this match.
     */
    public function predictions()
    {
        return $this->hasMany(Prediction::class, 'match_id');
    }

    public function getHashNameAttribute()
    {
        return pathinfo($this->video_path, PATHINFO_FILENAME);
    }
}





