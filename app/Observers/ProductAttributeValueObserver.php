<?php

namespace App\Observers;

use App\Models\ProductAttributeValue;
use App\Models\ProductFlat;
use App\Models\Attribute;
use Illuminate\Support\Str;

class ProductAttributeValueObserver
{
    /**
     * Map of attribute codes to ProductFlat columns.
     */
    protected array $attributeMap = [
        'name' => 'name',
        'description' => 'description',
        'url_key' => 'url_key',
        'new' => 'new',
        'featured' => 'featured',
        'price' => 'price',
        'weight' => 'weight',
        'meta_title' => 'meta_title',
        'meta_keywords' => 'meta_keywords',
        'meta_description' => 'meta_description',
    ];

    /**
     * Handle the ProductAttributeValue "saved" event.
     */
    public function saved(ProductAttributeValue $value): void
    {
        $this->syncToFlat($value);
    }

    /**
     * Handle the ProductAttributeValue "deleted" event.
     */
    public function deleted(ProductAttributeValue $value): void
    {
        $this->syncToFlat($value, true);
    }

    /**
     * Sync attribute value to the flat table.
     */
    protected function syncToFlat(ProductAttributeValue $value, bool $isDeleted = false): void
    {
        $attribute = Attribute::find($value->attribute_id);
        if (!$attribute || !isset($this->attributeMap[$attribute->code])) {
            return;
        }

        $column = $this->attributeMap[$attribute->code];
        $flatValue = $isDeleted ? null : $this->getActualValue($value, $attribute);

        ProductFlat::updateOrCreate(
            ['product_id' => $value->product_id, 'locale' => $value->locale],
            [$column => $flatValue]
        );

        // Special handling for URL key if name is updated but url_key is empty
        if ($attribute->code === 'name' && !$isDeleted) {
            $this->ensureUrlKey($value->product_id, $value->locale, $flatValue);
        }
    }

    protected function getActualValue(ProductAttributeValue $value, Attribute $attribute)
    {
        return match ($attribute->type) {
            'text', 'textarea' => $value->text_value,
            'boolean' => $value->boolean_value,
            'integer', 'select' => $value->integer_value,
            'float' => $value->float_value,
            'datetime' => $value->datetime_value,
            'date' => $value->date_value,
            'multiselect', 'checkbox' => $value->json_value,
            default => $value->text_value,
        };
    }

    protected function ensureUrlKey(int $productId, ?string $locale, string $name): void
    {
        $flat = ProductFlat::where('product_id', $productId)->where('locale', $locale)->first();
        if ($flat && empty($flat->url_key)) {
            $flat->update(['url_key' => Str::slug($name)]);
        }
    }
}
