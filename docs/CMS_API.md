# CMS API Documentation (Exhaustive)

This document details the CMS management APIs.
**Base URL**: `/api/admin/cms`
**Auth**: Bearer Token (Sanctum)

---

## 1. CMS Sections API
Manage content sections for pages.

### [PUT] `/sections/{id}`
Update the translation content of a CMS Section.

**Payload**:
- `locale`: string (`vi` or `en`)
- `content`: array (JSON object containing sectional data like `title`, `description`, `images`, etc.)

#### Example Request (JSON)
```json
{
    "locale": "vi",
    "content": {
        "title": "Chào mừng đến với cửa hàng",
        "subtitle": "Khám phá bộ sưu tập mới nhất",
        "button_text": "Mua ngay",
        "banner_url": "/storage/banners/home-hero.jpg"
    }
}
```

**Response**:
```json
{
    "status": "success",
    "message": "CMS Section translation updated successfully",
    "data": {
        "id": "uuid",
        "cms_section_id": "uuid",
        "locale": "vi",
        "content": { ... },
        "createdAt": "date-time",
        "updatedAt": "date-time"
    }
}
```
