<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds system Attributes that drive the EAV flat-table (ProductFlat::ATTRIBUTE_MAP).
 * These attributes are required for the flat table sync to work correctly.
 */
class CatalogAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            ['code' => 'name', 'admin_name' => 'Name', 'type' => 'text', 'is_required' => true, 'is_unique' => false, 'is_filterable' => false, 'is_configurable' => false],
            ['code' => 'short_description', 'admin_name' => 'Short Description', 'type' => 'textarea', 'is_required' => false, 'is_unique' => false, 'is_filterable' => false, 'is_configurable' => false],
            ['code' => 'description', 'admin_name' => 'Description', 'type' => 'textarea', 'is_required' => false, 'is_unique' => false, 'is_filterable' => false, 'is_configurable' => false],
            ['code' => 'url_key', 'admin_name' => 'URL Key', 'type' => 'text', 'is_required' => false, 'is_unique' => true, 'is_filterable' => false, 'is_configurable' => false],
            ['code' => 'new', 'admin_name' => 'New', 'type' => 'boolean', 'is_required' => false, 'is_unique' => false, 'is_filterable' => true, 'is_configurable' => false],
            ['code' => 'featured', 'admin_name' => 'Featured', 'type' => 'boolean', 'is_required' => false, 'is_unique' => false, 'is_filterable' => true, 'is_configurable' => false],
            ['code' => 'visible_individually', 'admin_name' => 'Visible Individually', 'type' => 'boolean', 'is_required' => false, 'is_unique' => false, 'is_filterable' => false, 'is_configurable' => false],
            ['code' => 'thumbnail', 'admin_name' => 'Thumbnail', 'type' => 'text', 'is_required' => false, 'is_unique' => false, 'is_filterable' => false, 'is_configurable' => false],
            ['code' => 'price', 'admin_name' => 'Price', 'type' => 'float', 'is_required' => true, 'is_unique' => false, 'is_filterable' => true, 'is_configurable' => false],
            ['code' => 'cost_price', 'admin_name' => 'Cost Price', 'type' => 'float', 'is_required' => false, 'is_unique' => false, 'is_filterable' => false, 'is_configurable' => false],
            ['code' => 'special_price', 'admin_name' => 'Special Price', 'type' => 'float', 'is_required' => false, 'is_unique' => false, 'is_filterable' => false, 'is_configurable' => false],
            ['code' => 'special_price_from', 'admin_name' => 'Special Price From', 'type' => 'date', 'is_required' => false, 'is_unique' => false, 'is_filterable' => false, 'is_configurable' => false],
            ['code' => 'special_price_to', 'admin_name' => 'Special Price To', 'type' => 'date', 'is_required' => false, 'is_unique' => false, 'is_filterable' => false, 'is_configurable' => false],
            ['code' => 'weight', 'admin_name' => 'Weight', 'type' => 'float', 'is_required' => false, 'is_unique' => false, 'is_filterable' => false, 'is_configurable' => false],
            ['code' => 'meta_title', 'admin_name' => 'Meta Title', 'type' => 'text', 'is_required' => false, 'is_unique' => false, 'is_filterable' => false, 'is_configurable' => false],
            ['code' => 'meta_keywords', 'admin_name' => 'Meta Keywords', 'type' => 'text', 'is_required' => false, 'is_unique' => false, 'is_filterable' => false, 'is_configurable' => false],
            ['code' => 'meta_description', 'admin_name' => 'Meta Description', 'type' => 'textarea', 'is_required' => false, 'is_unique' => false, 'is_filterable' => false, 'is_configurable' => false],
        ];

        $now = now()->toDateTimeString();

        foreach ($attributes as $attr) {
            DB::table('attributes')->updateOrInsert(
                ['code' => $attr['code']],
                array_merge($attr, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // Create default attribute family if not present
        DB::table('attribute_families')->updateOrInsert(
            ['code' => 'default'],
            ['code' => 'default', 'name' => 'Default', 'status' => true, 'created_at' => $now, 'updated_at' => $now]
        );

        echo "✅ Catalog system attributes seeded.\n";
    }
}
