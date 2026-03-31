# Tài liệu API Catalog

Dưới đây là danh sách các API cho module **Catalog** cùng với dữ liệu mẫu (test data / payload) dựa trên các Form Requests hiện tại trong source code. Tất cả các API này dành cho Admin (`/api/admin/catalog/...`) và tuân theo chuẩn RESTful.

---

## 1. Categories (Danh mục)

**Cấu trúc API:**
- `GET    /api/admin/catalog/categories` (Danh sách)
- `POST   /api/admin/catalog/categories` (Tạo mới)
- `GET    /api/admin/catalog/categories/{id}` (Chi tiết)
- `PUT    /api/admin/catalog/categories/{id}` (Cập nhật)
- `DELETE /api/admin/catalog/categories/{id}` (Xóa)

**Test Data (POST / PUT):**
```json
{
    "parent_id": null,
    "position": 1,
    "status": true,
    "translations": {
        "en": {
            "name": "Electronics",
            "slug": "electronics",
            "description": "Electronic devices and accessories",
            "url_key": "electronics",
            "meta_title": "Electronics Shop",
            "meta_keywords": "electronics, tech",
            "meta_description": "Best electronics shop"
        },
        "vi": {
            "name": "Điện tử",
            "slug": "dien-tu",
            "description": "Các thiết bị điện tử chính hãng",
            "url_key": "dien-tu"
        }
    }
}
```

---

## 2. Products (Sản phẩm)

**Cấu trúc API:**
- `GET    /api/admin/catalog/products` (Danh sách)
- `POST   /api/admin/catalog/products` (Tạo mới)
- `GET    /api/admin/catalog/products/{id}` (Chi tiết)
- `PUT    /api/admin/catalog/products/{id}` (Cập nhật)
- `DELETE /api/admin/catalog/products/{id}` (Xóa)

**Test Data (POST / PUT):**
*Lưu ý: Payload dựa trên bảng EAV, sử dụng `flat` array chứa các dữ liệu cục bộ (localize) như tên, giá hoặc thông tin cơ bản cho sản phẩm.*
```json
{
    "sku": "MACBOOK-PRO-M3",
    "status": true,
    "cost_price": 1000,
    "attribute_family_id": 1,
    "weight": 1.5,
    "thumbnail": "products/macbook-pro.jpg",
    "new": true,
    "featured": true,
    "visible_individually": true,
    "flat": {
        "en": {
            "name": "MacBook Pro M3",
            "description": "The latest MacBook Pro",
            "price": 1499.00,
            "url_key": "macbook-pro-m3",
            "meta_title": "Macbook Pro M3",
            "meta_keywords": "apple, macbook pro",
            "meta_description": "Buy MacBook Pro M3 online at best price"
        },
        "vi": {
            "name": "MacBook Pro M3",
            "description": "MacBook Pro mới nhất từ Apple",
            "price": 35000000,
            "url_key": "macbook-pro-m3-vi"
        }
    },
    "categories": [1, 2],
    "tags": [1],
    "inventories": [
        {
            "id": 1,
            "qty": 50
        }
    ],
    "images": [
        {
            "path": "products/gallery/img1.jpg",
            "type": "image",
            "position": 1
        }
    ],
    "attribute_values": {
        "color": "Silver",
        "storage": "512GB"
    },
    "up_sells": [],
    "cross_sells": [],
    "super_attributes": []
}
```

---

## 3. Attributes (Thuộc tính sản phẩm)

**Cấu trúc API:**
- `GET    /api/admin/catalog/attributes` (Danh sách)
- `POST   /api/admin/catalog/attributes` (Tạo mới)
- `GET    /api/admin/catalog/attributes/{id}` (Chi tiết)
- `PUT    /api/admin/catalog/attributes/{id}` (Cập nhật)
- `DELETE /api/admin/catalog/attributes/{id}` (Xóa)

**Test Data (POST / PUT):**
```json
{
    "code": "color_options",
    "admin_name": "Color Options",
    "type": "select",
    "is_required": false,
    "is_unique": false,
    "is_filterable": true,
    "is_configurable": true,
    "options": [
        { "admin_name": "Red" },
        { "admin_name": "Green" },
        { "admin_name": "Blue" }
    ]
}
```

---

## 4. Attribute Families (Nhóm Thuộc tính sản phẩm)

**Cấu trúc API:**
- `GET    /api/admin/catalog/attribute-families`
- `POST   /api/admin/catalog/attribute-families`
- `GET    /api/admin/catalog/attribute-families/{id}`
- `PUT    /api/admin/catalog/attribute-families/{id}`
- `DELETE /api/admin/catalog/attribute-families/{id}`

**Test Data (POST / PUT):**
```json
{
    "code": "default_family",
    "name": "Default",
    "status": true,
    "groups": [1, 2, 3]
}
```

---

## 5. Tags (Nhãn dán/Thẻ)

**Cấu trúc API:**
- `GET    /api/admin/catalog/tags` (Danh sách)
- `POST   /api/admin/catalog/tags` (Tạo mới)
- `GET    /api/admin/catalog/tags/{id}` (Chi tiết)
- `PUT    /api/admin/catalog/tags/{id}` (Cập nhật)
- `DELETE /api/admin/catalog/tags/{id}` (Xóa)

**Test Data (POST / PUT):**
```json
{
    "translations": {
        "en": {
            "name": "Best Seller",
            "slug": "best-seller"
        },
        "vi": {
            "name": "Bán Chạy Nhất",
            "slug": "ban-chay-nhat"
        }
    }
}
```

---
*Ghi chú: Nếu bạn cần sử dụng bộ dữ liệu này bằng công cụ như Postman, hãy nhớ kèm Header `Accept: application/json` và Authorization Token cho các endpoint admin.*
