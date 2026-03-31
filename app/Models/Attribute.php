<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends BaseModel
{
    protected $fillable = [
        'code',
        'admin_name',
        'type',
        'is_required',
        'is_unique',
        'is_filterable',
        'is_configurable',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_unique' => 'boolean',
        'is_filterable' => 'boolean',
        'is_configurable' => 'boolean',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(AttributeOption::class);
    }

    public static function getValueColumn(string $type): string
    {
        return match ($type) {
            'text', 'textarea' => 'text_value',
            'boolean' => 'boolean_value',
            'integer', 'select' => 'integer_value',
            'float' => 'float_value',
            'datetime' => 'datetime_value',
            'date' => 'date_value',
            'multiselect', 'checkbox' => 'json_value',
            default => 'text_value',
        };
    }
}