# Catalog API Documentation (Exhaustive)

This document details every field and relationship for the Catalog management APIs.
**Base URL**: `/api/admin/catalog`
**Auth**: Bearer Token (Sanctum)

---

## 1. Tags API
Manage product tags.
- **Table**: `tags`, `tag_translations`.

### [GET] `/tags`
**Filters**: `name`, `slug`, `status` (boolean).
**Response (Item)**:
```json
{
    "id": "uuid",
    "status": boolean,
    "translations": {
        "vi": { "name": "string", "slug": "string" },
        "en": { "name": "string", "slug": "string" }
    },
    "createdAt": "date-time",
    "updatedAt": "date-time"
}
```

### [POST] `/tags` | [PUT/PATCH] `/tags/{id}`
**Payload**:
- `status`: boolean
- `translations`: array of objects
  - `vi`: { "name": "required|string", "slug": "optional|string" }

#### Example Request (JSON)
```json
{
    "status": true,
    "translations": {
        "vi": { "name": "Đồ gia dụng", "slug": "do-gia-dung" },
        "en": { "name": "Home Appliances", "slug": "home-appliances" }
    }
}
```

---

## 2. Categories API
Manage product categories with tree structure and images.
- **Table**: `categories`, `category_translations`.

### [GET] `/categories`
**Response (Item)**:
```json
{
    "id": "uuid",
    "parentId": "uuid|null",
    "position": integer,
    "status": boolean,
    "displayMode": "products_and_description|products_only|description_only",
    "logoUrl": "string|null",
    "bannerUrl": "string|null",
    "additional": object|null,
    "translations": {
        "vi": {
            "name": "string",
            "slug": "string",
            "urlKey": "string",
            "description": "string|null",
            "metaTitle": "string|null",
            "metaKeywords": "string|null",
            "metaDescription": "string|null"
        }
    }
}
```

### [POST] `/categories` (Multipart/Form-Data)
To Update: use `_method=PUT`.
- **Core Fields**:
  - `parent_id`: UUID
  - `position`: integer
  - `status`: boolean
  - `logo`: File (image, max 2MB)
  - `banner`: File (image, max 4MB)
  - `additional`: JSON array/object
- **Translations Array**:
  - `translations[vi][name]`: required
  - `translations[vi][slug]`: required
  - `translations[vi][description]`: optional
  - `translations[vi][url_key]`: optional (auto-generated)
  - `translations[vi][meta_title]`: optional
  - `translations[vi][meta_keywords]`: optional
  - `translations[vi][meta_description]`: optional

#### Example Request (Multipart/Form-Data)
- **position**: `1`
- **status**: `true`
- **logo**: `[Binary File]`
- **banner**: `[Binary File]`
- **translations[vi][name]**: `Điện thoại`
- **translations[vi][slug]**: `dien-thoai`
- **translations[vi][description]**: `Mô tả danh mục...`
- **translations[en][name]**: `Phones`
- **translations[en][slug]**: `phones`

---

## 3. Attributes API
Manage custom product attributes (EAV).
- **Table**: `attributes`, `attribute_options`.

### [POST] `/attributes`
- **Fields**:
  - `code`: string (unique, e.g., 'color', 'size')
  - `admin_name`: string (e.g., 'Color')
  - `type`: "text", "textarea", "boolean", "integer", "float", "select", "multiselect", "datetime", "date", "checkbox"
  - `is_required`: boolean
  - `is_unique`: boolean
  - `is_filterable`: boolean
  - `is_configurable`: boolean
  - `options`: array (only for select/multiselect)
    - `options[0][admin_name]`: string (e.g., 'Red')
    - `options[0][swatch_value]`: string (optional)
    - `options[0][sort_order]`: integer (optional)

---

## 4. Attribute Families API
Group attributes for specific product types (e.g., 'Furniture', 'Electronics').
- **Table**: `attribute_families`, `attribute_groups`.

### [POST] `/attribute-families`
- **Fields**:
  - `code`: string (unique)
  - `name`: string
  - `status`: boolean
  - `groups`: array of objects
    - `groups[0][name]`: string (e.g., 'General')
    - `groups[0][position]`: integer
    - `groups[0][attributes]`: array of attribute UUIDs.

---

## 5. Products API
The most complex entity, combining core data, images, inventory, and EAV.
- **Table**: `products`, `product_flat`, `product_images`, `product_inventories`, `product_attribute_values`.

### [GET] `/products` (Response Details)
```json
{
    "id": "uuid",
    "sku": "string",
    "status": boolean,
    "costPrice": float|null,
    "weight": float|null,
    "thumbnail": "string|null",
    "new": boolean,
    "featured": boolean,
    "visibleIndividually": boolean,
    "attributeFamilyId": "uuid",
    "flat": {
        "vi": {
            "name": "string",
            "shortDescription": "string|null",
            "description": "string|null",
            "price": decimal,
            "specialPrice": decimal|null,
            "specialPriceFrom": "date|null",
            "specialPriceTo": "date|null",
            "urlKey": "string",
            "metaTitle": "string",
            "metaKeywords": "string",
            "metaDescription": "string"
        }
    },
    "categories": [ { "id": "uuid", "name": "string" } ],
    "tags": [ { "id": "uuid", "name": "string" } ],
    "inventories": [ { "id": "uuid", "qty": integer } ],
    "images": [ { "id": "uuid", "path": "string", "url": "string", "type": "string", "position": 0 } ],
    "attributeValues": [ { "attribute_id": "uuid", "locale": "string|null", "value": "mixed" } ]
}
```

### [POST] `/products` (Multipart/Form-Data)
To Update: use `_method=PUT`.
- **Core Info**: `sku`, `status`, `cost_price`, `weight`, `attribute_family_id`, `new`, `featured`, `visible_individually`.
- **Translations/Flat Data**:
  - `flat[vi][name]`: required
  - `flat[vi][price]`: required
  - `flat[vi][short_description]`: optional
  - `flat[vi][description]`: optional
  - `flat[vi][special_price]`: optional
  - `flat[vi][special_price_from]`: optional (YYYY-MM-DD)
  - `flat[vi][special_price_to]`: optional (YYYY-MM-DD)
- **Relations**:
  - `categories[]`: array of UUIDs
  - `tags[]`: array of UUIDs
  - `inventories[0][qty]`: integer (required)
- **Images (Multiple Upload)**:
  - `images[0][file]`: Image file (max 4MB)
  - `images[0][type]`: "base" | "thumbnail" (optional)
  - `images[0][position]`: integer
  - *To Keep Existing Image (Update only)*: Send `images[i][id]` instead of `file`.
- **EAV Attributes**:
  - `attribute_values[attribute_uuid_or_code]`: mixed value.
  - *Note*: Select/Multiselect types expect Option IDs.

#### Example Request (Multipart/Form-Data)
- **sku**: `IPHONE-15-PRO`
- **status**: `1`
- **attribute_family_id**: `e9d1a...`
- **flat[vi][name]**: `iPhone 15 Pro`
- **flat[vi][price]**: `25000000`
- **flat[vi][description]**: `Siêu phẩm 2024...`
- **inventories[0][qty]**: `50`
- **categories[0]**: `uuid-cat-1`
- **categories[1]**: `uuid-cat-2`
- **tags[0]**: `uuid-tag-1`
- **images[0][file]**: `[Binary Image]`
- **images[0][position]**: `0`
- **images[0][type]**: `base`
- **attribute_values[color_attr_id]**: `option-uuid-red`
- **attribute_values[storage_attr_code]**: `option-uuid-256gb`
