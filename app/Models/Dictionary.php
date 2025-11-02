<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dictionary extends Model
{
    use HasFactory;

    protected $table = 'dictionary';

    protected $fillable = [
        'type',
        'class',
        'label_en',
        'label_ar',
        'description_en',
        'description_ar',
    ];

    /**
     * Get label based on current locale
     */
    public function getLabelAttribute(): ?string
    {
        $locale = app()->getLocale();
        $labelField = "label_{$locale}";
        
        // Fallback to English if current locale label doesn't exist
        if (!isset($this->attributes[$labelField]) || empty($this->attributes[$labelField])) {
            return $this->attributes['label_en'] ?? null;
        }
        
        return $this->attributes[$labelField] ?? null;
    }

    /**
     * Get description based on current locale
     */
    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        $descField = "description_{$locale}";
        
        // Fallback to English if current locale description doesn't exist
        if (!isset($this->attributes[$descField]) || empty($this->attributes[$descField])) {
            return $this->attributes['description_en'] ?? null;
        }
        
        return $this->attributes[$descField] ?? null;
    }

    /**
     * Get dictionary entry by type and class
     */
    public static function getByTypeAndClass(string $type, int $class): ?self
    {
        return self::where('type', $type)
            ->where('class', $class)
            ->first();
    }

    /**
     * Get label by type and class for current locale
     */
    public static function getLabel(string $type, int $class, ?string $locale = null): ?string
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }
        
        $entry = self::getByTypeAndClass($type, $class);
        if (!$entry) {
            return null;
        }
        
        $labelField = "label_{$locale}";
        $label = $entry->$labelField;
        
        // Fallback to English if current locale doesn't have a label
        if (empty($label)) {
            return $entry->label_en;
        }
        
        return $label;
    }

    /**
     * Get description by type and class for current locale
     */
    public static function getDescription(string $type, int $class, ?string $locale = null): ?string
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }
        
        $entry = self::getByTypeAndClass($type, $class);
        if (!$entry) {
            return null;
        }
        
        $descField = "description_{$locale}";
        $description = $entry->$descField;
        
        // Fallback to English if current locale doesn't have a description
        if (empty($description)) {
            return $entry->description_en;
        }
        
        return $description;
    }
}
