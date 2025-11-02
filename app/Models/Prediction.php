<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'match_id',
        'clip_path',
        'relative_time',
        'first_model_prop',
        'prediction_0',
        'prediction_1',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'first_model_prop' => 'float',
        'prediction_0' => 'array',
        'prediction_1' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the match that owns this prediction.
     */
    public function match()
    {
        return $this->belongsTo(MatchVideo::class, 'match_id');
    }
}
