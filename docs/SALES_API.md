# Sales API Documentation (Exhaustive)

This document details the Sales management APIs, including Orders, Invoices, and Transactions.
**Base URL**: `/api/admin/sales`
**Auth**: Bearer Token (Sanctum)

---

## 1. Orders API
Manage customer orders.

### [GET] `/orders`
**Filters**: `increment_id`, `status`, `customer_id`, `date_from`, `date_to`.

### [POST] `/orders`
Create a direct order (Admin creation).
**Payload**:
- `customer_id`: UUID
- `items`: array
  - `product_id`: UUID
  - `qty`: integer
- `shipping_address`: object
- `billing_address`: object

### [GET] `/orders/{id}`
**Response (Data)**:
```json
{
    "id": "uuid",
    "increment_id": "string",
    "status": "pending|processing|completed|canceled|...",
    "grand_total": decimal,
    "sub_total": decimal,
    "discount_amount": decimal,
    "tax_amount": decimal,
    "shipping_amount": decimal,
    "items": [
        { "id": "uuid", "sku": "string", "name": "string", "qty_ordered": integer, "price": decimal }
    ],
    "addresses": [
        { "id": "uuid", "address_type": "shipping|billing", "first_name": "string", "last_name": "string", "address1": "string" }
    ],
    "customer": { "id": "uuid", "email": "string" }
}
```

### [PUT] `/orders/{id}/status`
Update order status with optional comment.
**Payload**:
- `status`: string (required)
- `comment`: string (optional)

### [POST] `/orders/{id}/cancel`
Cancel an order.

---

## 2. Invoices API
Manage order invoices.

### [GET] `/invoices`
### [POST] `/invoices`
Create an invoice for an order.
**Payload**:
- `order_id`: UUID (required)
- `grand_total`: decimal (optional)

### [GET] `/invoices/{id}`
**Response (Data)**:
```json
{
    "id": "uuid",
    "increment_id": "string",
    "state": "pending|paid|...",
    "grand_total": decimal,
    "order_id": "uuid"
}
```

---

## 3. Order Transactions API
Manage payment transactions.

### [GET] `/transactions`
### [POST] `/transactions`
**Payload**:
- `order_id`: UUID
- `transaction_id`: string (from gateway)
- `payment_method`: string
- `amount`: decimal
- `status`: string
- `type`: string

### [GET] `/transactions/{id}`
**Response (Data)**:
```json
{
    "id": "uuid",
    "transaction_id": "string",
    "status": "string",
    "type": "string",
    "amount": decimal,
    "payment_method": "string",
    "order_id": "uuid",
    "invoice_id": "uuid|null"
}
```
