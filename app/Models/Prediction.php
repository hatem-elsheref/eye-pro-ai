<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dictionary;

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

    /**
     * Get formatted prediction with labels
     * Returns prediction data with class labels instead of integers
     */
    public function getFormattedPrediction(?string $locale = null): array
    {
        $result = [
            'prediction_0' => $this->formatPredictionData($this->prediction_0, $locale),
            'prediction_1' => $this->formatPredictionData($this->prediction_1, $locale),
        ];
        
        return $result;
    }

    /**
     * Format prediction data with labels
     */
    public function formatPredictionData(?array $prediction, ?string $locale = null): ?array
    {
        if (!$prediction || !isset($prediction['classes']) || !isset($prediction['acc'])) {
            return null;
        }

        $formatted = [
            'acc' => $prediction['acc'],
            'classes' => $prediction['classes'],
            'labels' => [],
        ];

        // Format classes with labels
        if (isset($prediction['classes'][0]) && is_array($prediction['classes'][0])) {
            $classes = $prediction['classes'][0];
            
            // First class is offence_severity, second is action
            if (isset($classes[0])) {
                $severityClass = (int) $classes[0];
                $formatted['labels']['offence_severity'] = Dictionary::getLabel('offence_severity', $severityClass, $locale);
                $formatted['labels']['offence_severity_class'] = $severityClass;
            }
            
            if (isset($classes[1])) {
                $actionClass = (int) $classes[1];
                $formatted['labels']['action'] = Dictionary::getLabel('action', $actionClass, $locale);
                $formatted['labels']['action_class'] = $actionClass;
            }
        }

        return $formatted;
    }
}
