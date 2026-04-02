# Post API Documentation (Exhaustive)

This document details every field and relationship for the Post (Blog) management APIs.
**Base URL**: `/api/admin/posts`
**Auth**: Bearer Token (Sanctum) - *Note: Currently public for testing*

---

## 1. Post Tags API
Manage blog post tags.
- **Table**: `post_tags`, `post_tag_translations`.

### [GET] `/tags`
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
        "vi": { "name": "Công nghệ", "slug": "cong-nghe" },
        "en": { "name": "Technology", "slug": "technology" }
    }
}
```

---

## 2. Post Categories API
Manage blog post categories.
- **Table**: `post_categories`, `post_category_translations`.

### [GET] `/categories`
**Response (Item)**:
```json
{
    "id": "uuid",
    "parentId": "uuid|null",
    "position": integer,
    "status": boolean,
    "imageUrl": "string|null",
    "translations": {
        "vi": {
            "name": "string",
            "slug": "string",
            "description": "string|null",
            "metaTitle": "string|null",
            "metaKeywords": "string|null",
            "metaDescription": "string|null"
        }
    },
    "createdAt": "date-time",
    "updatedAt": "date-time"
}
```

### [POST] `/categories` (Multipart/Form-Data)
To Update: use `_method=PUT`.
- **Core Fields**:
  - `parent_id`: UUID (optional)
  - `position`: integer
  - `status`: boolean
  - `image`: File (image, max 2MB)
- **Translations**:
  - `translations[vi][name]`: required
  - `translations[vi][slug]`: optional
  - `translations[vi][description]`: optional
  - `translations[vi][meta_title]`: optional
  - `translations[vi][meta_keywords]`: optional
  - `translations[vi][meta_description]`: optional

---

## 3. Posts API
Manage blog posts.
- **Table**: `posts`, `post_translations`, `post_category_map`, `post_tag_map`.

### [GET] `/`
**Filters**: `name`, `status`, `category_id`, `tag_id`.
**Response (Item)**:
```json
{
    "id": "uuid",
    "status": boolean,
    "imageUrl": "string|null",
    "authorId": "uuid|null",
    "publishedAt": "date-time|null",
    "translations": {
        "vi": {
            "name": "string",
            "slug": "string",
            "shortDescription": "string|null",
            "content": "string",
            "metaTitle": "string|null",
            "metaKeywords": "string|null",
            "metaDescription": "string|null"
        }
    },
    "categories": [ { "id": "uuid", "name": "string" } ],
    "tags": [ { "id": "uuid", "name": "string" } ],
    "createdAt": "date-time",
    "updatedAt": "date-time"
}
```

### [POST] `/` (Multipart/Form-Data)
To Update: use `_method=PUT`.
- **Core Fields**:
  - `status`: boolean
  - `image`: File (image, max 2MB)
  - `author_id`: UUID (optional)
  - `published_at`: date-time (optional)
  - `categories[]`: array of UUIDs
  - `tags[]`: array of UUIDs
- **Translations**:
  - `translations[vi][name]`: required
  - `translations[vi][slug]`: optional
  - `translations[vi][short_description]`: optional
  - `translations[vi][content]`: required
  - `translations[vi][meta_title]`: optional
  - `translations[vi][meta_keywords]`: optional
  - `translations[vi][meta_description]`: optional

#### Example Request (Multipart/Form-Data)
- **status**: `true`
- **categories[0]**: `uuid-cat-1`
- **translations[vi][name]**: `Hướng dẫn Laravel`
- **translations[vi][content]**: `<p>Nội dung bài viết...</p>`
- **image**: `[Binary File]`
