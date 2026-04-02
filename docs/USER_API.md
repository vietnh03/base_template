# User API Documentation (Exhaustive)

This document details the User management APIs.
**Base URL**: `/api/users`
**Auth**: Bearer Token (Passport) - *Note: Requires `auth:api` middleware*

---

## 1. Users API

### [GET] `/`
List users with filters.
**Filters**: `name`, `phone`, `email`, `status`, `is_verified`.

**Response (Item)**:
```json
{
    "id": "uuid",
    "name": "string",
    "phone": "string",
    "email": "string|null",
    "image": "string|null",
    "status": "integer",
    "isVerified": "boolean",
    "gender": "string|null",
    "dateOfBirth": "date|null",
    "createdAt": "date-time",
    "updatedAt": "date-time"
}
```

### [POST] `/` | [PUT/PATCH] `/{id}`
**Payload**:
- `name`: string (required on create)
- `phone`: string (required on create, unique)
- `email`: string (email, unique, optional)
- `password`: string (min 8 chars, optional)
- `status`: integer (optional, default: 1)
- `is_verified`: boolean (optional)
- `token`: string (optional)
- `gender`: string (optional)
- `date_of_birth`: date (optional)
- `image`: string (optional)
- `notes`: string (optional, hidden in GET responses)

#### Example Request (JSON)
```json
{
    "name": "Nguyễn Văn A",
    "phone": "0987654321",
    "email": "vana@example.com",
    "status": 1,
    "is_verified": true
}
```

### [GET] `/{id}`
Retrieve a single user by ID.

### [DELETE] `/{id}`
Delete a user.

---

## 2. User Authentication

### [POST] `/api/auth/register`
Register a new user account.
**Payload**:
- `name`: string (required)
- `email`: string (required, unique)
- `phone`: string (required, unique)
- `password`: string (min 8, required)
- `password_confirmation`: string (required)

### [POST] `/api/auth/login`
**Payload**:
- `email`: string (required)
- `password`: string (required)

**Success Response**:
```json
{
    "id": "uuid",
    "name": "string",
    "email": "string",
    "accessToken": "jwt_token",
    "tokenType": "Bearer",
    "expiresAt": "date-time"
}
```

### [GET] `/api/auth/me`
Retrieve the authenticated user's profile.
**Auth**: Bearer Token (standard `api` guard).

### [POST] `/api/auth/logout`
Revoke the current authentication token.
**Auth**: Bearer Token (standard `api` guard).
