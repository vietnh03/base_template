<?php

namespace App\AppMain\Core\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot the trait and set up UUID generation
     */
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            // Only generate UUID if id is not already set and keyType is string
            if (empty($model->{$model->getKeyName()}) && $model->getKeyType() === 'string') {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the value indicating whether the IDs are incrementing
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}
