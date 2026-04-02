# Customer API Documentation (Exhaustive)

This document details the Customer management APIs.
**Base URL**: `/api/customers`
**Auth**: Bearer Token (Passport) - *Note: Requires `auth:api` middleware*

---

## 1. Customers API

### [GET] `/`
List customers with filters.
**Filters**: `full_name`, `phone_number`, `email`, `customer_type`, `customer_status`.

**Response (Item)**:
```json
{
    "id": "uuid",
    "fullName": "string",
    "phoneNumber": "string",
    "email": "string|null",
    "customerType": "Individual|Business",
    "customerStatus": "Lead|Active|Inactive|VIP",
    "assignedStaffId": "uuid|null",
    "address": "string|null",
    "gender": "Male|Female|Other|null",
    "source": "string|null",
    "dateOfBirth": "date|null",
    "createdAt": "date-time",
    "updatedAt": "date-time"
}
```

### [POST] `/` | [PUT/PATCH] `/{id}`
**Payload**:
- `full_name`: string (required on create)
- `phone_number`: string (required on create, unique)
- `customer_type`: "Individual" | "Business" (required on create)
- `email`: string (email, unique, optional)
- `customer_status`: "Lead" | "Active" | "Inactive" | "VIP" (optional)
- `assigned_staff_id`: UUID (optional)
- `address`: string (optional)
- `gender`: "Male" | "Female" | "Other" (optional)
- `date_of_birth`: date (optional)
- `source`: string (optional)
- `notes`: string (optional, hidden in GET responses)

#### Example Request (JSON)
```json
{
    "full_name": "Nguyễn Văn A",
    "phone_number": "0987654321",
    "customer_type": "Individual",
    "email": "vana@example.com",
    "customer_status": "Active"
}
```

### [GET] `/{id}`
Retrieve a single customer by ID.

### [DELETE] `/{id}`
Delete a customer.
