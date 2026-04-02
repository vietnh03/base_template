# Checkout & Cart API Documentation (Exhaustive)

This document details the Cart and Checkout APIs for customers.
**Base URL**: `/api/cart`
**Auth**: Bearer Token (Passport)

---

## 1. Cart Management

### [GET] `/`
Get current user's cart.
**Response (Data)**:
```json
{
    "data": {
        "id": "uuid",
        "customer_id": "uuid",
        "is_active": boolean,
        "items_count": integer,
        "items_qty": integer,
        "grand_total": decimal,
        "sub_total": decimal,
        "items": [
            {
                "id": "uuid",
                "product_id": "uuid",
                "sku": "string",
                "name": "string",
                "qty": integer,
                "price": decimal,
                "total": decimal
            }
        ]
    }
}
```

### [POST] `/add`
Add a product to the cart.
**Payload**:
- `product_id`: UUID (required)
- `quantity`: integer (optional, default: 1)

### [PUT] `/update/{itemId}`
Update an item's quantity in the cart.
**Payload**:
- `quantity`: integer (required)

### [DELETE] `/remove/{itemId}`
Remove an item from the cart.

---

## 2. Checkout

### [POST] `/checkout`
Place an order from the current cart.
**Response (Data)**:
```json
{
    "message": "Order placed successfully",
    "data": {
        "id": "uuid",
        "increment_id": "string",
        "grand_total": decimal,
        "status": "pending",
        "items": [...]
    }
}
```
