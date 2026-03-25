# Business Application Layer

## Purpose

This layer contains application logic for **Business/Tenant users** in a multi-tenant architecture.

## Use Cases

- **Multi-tenant SaaS applications** where each business/organization has their own workspace
- **B2B platforms** where business users manage their company's data
- **Tenant-specific features** that are isolated per organization

## Characteristics

- **Tenant Isolation**: Each business user can only access their own tenant's data
- **Business Logic**: Operations specific to business/organizational needs
- **Shared Domain Services**: Reuses domain services from `AppMain/Domain` layer
- **Custom Authorization**: Tenant-based access control and permissions