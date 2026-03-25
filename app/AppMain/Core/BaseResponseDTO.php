<?php

namespace App\AppMain\Core;

use Illuminate\Support\Collection;
use stdClass;

abstract class BaseResponseDTO
{
    /**
     * Create DTO from model instance
     * Must be implemented by child classes with explicit field mapping
     */
    abstract public static function fromModel($model): self;

    /**
     * Create single DTO from model
     * Returns DTO object (Laravel will auto-convert to JSON)
     *
     * @param mixed $model - Model instance or null
     * @return self|null
     */
    public static function single($model): ?self
    {
        if (!$model) {
            return null;
        }

        return static::fromModel($model);
    }

    /**
     * Create collection of DTO objects from multiple models
     * Returns array of DTO objects (Laravel will auto-convert to JSON)
     *
     * @param Collection|array $models
     * @return array
     */
    public static function collection($models): array
    {
        if ($models instanceof Collection) {
            return $models->map(fn($model) => static::fromModel($model))->all();
        }

        if (is_array($models)) {
            return array_map(fn($model) => static::fromModel($model), $models);
        }

        return [];
    }

    /**
     * Transform paginated data with metadata
     * Returns array with DTO objects (Laravel will auto-convert to JSON)
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginatedData
     * @return array
     */
    public static function paginated($paginatedData): array
    {
        return [
            'data' => static::collection($paginatedData->items()),
            'current_page' => $paginatedData->currentPage(),
            'per_page' => $paginatedData->perPage(),
            'total' => $paginatedData->total(),
            'last_page' => $paginatedData->lastPage(),
            'from' => $paginatedData->firstItem(),
            'to' => $paginatedData->lastItem(),
        ];
    }

    /**
     * Smart transform - auto-detect type and convert
     * Handles: Model, Collection, Paginator, stdClass, array
     *
     * @param mixed $data
     * @return mixed
     */
    public static function transform($data)
    {
        // Null or empty
        if (!$data) {
            return $data;
        }

        // Paginated data
        if (method_exists($data, 'items') && method_exists($data, 'total')) {
            return static::paginated($data);
        }

        // Collection or array of models
        if ($data instanceof Collection || is_array($data)) {
            // Check if it's array of models or plain array
            $first = $data instanceof Collection ? $data->first() : ($data[0] ?? null);

            if ($first && is_object($first) && !($first instanceof stdClass)) {
                // Array of models
                return static::collection($data);
            }

            // Plain array - return as-is
            return $data;
        }

        // Single model
        if (is_object($data) && !($data instanceof stdClass)) {
            return static::single($data);
        }

        // stdClass or plain data - return as-is
        return $data;
    }

    /**
     * Create DTO from array or stdClass
     * Useful for raw data transformation
     *
     * @param array|stdClass $data
     * @return self
     */
    public static function fromArray($data): self
    {
        $dto = new static();

        $data = is_object($data) ? get_object_vars($data) : $data;

        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                $dto->$key = $value;
            }
        }

        return $dto;
    }
}
