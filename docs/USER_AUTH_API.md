# Auth & User API Documentation (Exhaustive)

This document details the Authentication and User management APIs for both Administrators and End-Users.

---

## 1. Platform Admin Auth API
**Base URL**: `/api/admin/auth`
**Auth**: Bearer Token (Sanctum)

### [POST] `/login`
**Payload**: `email`, `password`.
**Response**: Admin object + `token`.

### [GET] `/me`
Get current admin profile.

### [POST] `/logout`
Logout current session.

### [POST] `/logout-all`
Logout from all devices.

---

## 2. Platform Admin: User Management
**Base URL**: `/api/admin/users`
**Auth**: Bearer Token (Sanctum)

### [GET] `/`
List users with filters (`name`, `email`, `role`, `status`).

### [POST] `/` | [PUT/PATCH] `/{id}`
**Payload**:
- `name`: string (required)
- `email`: string (required, unique)
- `password`: string (required for create, min 8)
- `role`: "admin" | "manager" | "user" (optional)
- `status`: "active" | "inactive" | "suspended" (optional)

### [GET] `/{id}` | [DELETE] `/{id}`

---

## 3. End-User Auth API (Storefront/Tenant)
**Base URL**: `/api/auth`
**Auth**: Bearer Token (Passport)

### [POST] `/register`
**Payload**: `name`, `email`, `password` (min 6).

### [POST] `/login`
**Payload**: `email`, `password`.

### [GET] `/me`
Get current user profile.

### [POST] `/logout` | [POST] `/logout-all`
