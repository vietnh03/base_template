<?php


namespace App\AppMain\Core;

class BaseDTO
{
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Create DTO from validated request data
     */
    public static function fromRequest($request): self
    {
        return new static($request->validated());
    }

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new static($data);
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        $data = [];
        foreach (get_object_vars($this) as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
    }

    /**
     * Get only filled (non-null) values
     * Useful for partial updates
     */
    public function onlyFilled(): array
    {
        return array_filter($this->toArray(), function ($value) {
            return $value !== null;
        });
    }
}
