<?php

namespace App\AppMain\Domain\Product\Services;

use App\AppMain\Domain\Product\Repositories\ProductRepository;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductFlat;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function __construct(
        protected ProductRepository $productRepository
    ) {
    }

    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = $this->productRepository->create($data);

            if (isset($data['categories'])) {
                $product->categories()->sync($data['categories']);
            }

            if (isset($data['attributes'])) {
                $this->saveAttributeValues($product, $data['attributes']);
            }

            if (isset($data['tags'])) {
                $product->tags()->sync($data['tags']);
            }

            $this->refreshFlat($product);

            return $product;
        });
    }

    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $this->productRepository->update($product, $data);

            if (isset($data['categories'])) {
                $product->categories()->sync($data['categories']);
            }

            if (isset($data['attributes'])) {
                $this->saveAttributeValues($product, $data['attributes']);
            }

            if (isset($data['tags'])) {
                $product->tags()->sync($data['tags']);
            }

            $this->refreshFlat($product);

            return $product;
        });
    }

    protected function saveAttributeValues(Product $product, array $attributes): void
    {
        $locale = $attributes['locale'] ?? 'en';
        unset($attributes['locale']);

        foreach ($attributes as $code => $value) {
            $attribute = \App\Models\Attribute::where('code', $code)->first();

            if (!$attribute) {
                continue;
            }

            $column = $this->getAttributeValueColumn($attribute->type);

            ProductAttributeValue::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'attribute_id' => $attribute->id,
                    'locale' => $locale,
                ],
                [
                    $column => $value,
                ]
            );
        }
    }

    protected function getAttributeValueColumn(string $type): string
    {
        return match ($type) {
            'text', 'textarea' => 'text_value',
            'boolean' => 'boolean_value',
            'integer', 'select' => 'integer_value',
            'float', 'price' => 'float_value',
            'datetime' => 'datetime_value',
            'date' => 'date_value',
            'json' => 'json_value',
            default => 'text_value',
        };
    }

    protected function refreshFlat(Product $product): void
    {
        // Get common attributes from ProductAttributeValue to sync to flat table
        $attributeValues = $product->attribute_values()
            ->join('attributes', 'product_attribute_values.attribute_id', '=', 'attributes.id')
            ->get(['attributes.code', 'attributes.type as attribute_type', 'product_attribute_values.*']);

        $flatData = [
            'sku' => $product->sku,
            'status' => $product->status,
            'cost_price' => $product->cost_price,
            'parent_id' => $product->parent_id,
        ];

        foreach ($attributeValues as $av) {
            $column = $this->getAttributeValueColumn($av->attribute_type);
            $value = $av->{$column};

            // Map specific codes to product_flat columns
            switch ($av->code) {
                case 'name':
                    $flatData['name'] = $value;
                    break;
                case 'description':
                    $flatData['description'] = $value;
                    break;
                case 'url_key':
                    $flatData['url_key'] = $value;
                    break;
                case 'price':
                    $flatData['price'] = $value;
                    break;
                case 'weight':
                    $flatData['weight'] = $value;
                    break;
                case 'meta_title':
                    $flatData['meta_title'] = $value;
                    break;
                case 'meta_keywords':
                    $flatData['meta_keywords'] = $value;
                    break;
                case 'meta_description':
                    $flatData['meta_description'] = $value;
                    break;
            }
        }

        ProductFlat::updateOrCreate(
            ['product_id' => $product->id, 'locale' => 'en'], // Defaulting to en for now
            $flatData
        );
    }
}
