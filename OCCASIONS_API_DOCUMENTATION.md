# Occasions API Documentation

Complete REST API for managing occasions with repository, service, and controller pattern.

## Base URL
```
/api/occasions
```

## Authentication
- **Public endpoints**: No authentication required
- **Vendor endpoints**: Requires `Authorization: Bearer {token}` (Sanctum)

---

## API Endpoints

### 1. Get All Occasions (Public)
**Endpoint:** `GET /api/occasions`

**Description:** Retrieve all active occasions with optional filters and pagination.

**Query Parameters:**
- `search` (string, optional) - Search by occasion name
- `active` (boolean, optional) - Filter by active status (default: true)
- `vendor_id` (integer, optional) - Filter by vendor
- `per_page` (integer, optional) - Items per page (default: 15)
- `page` (integer, optional) - Page number (default: 1)

**Response:**
```json
{
  "status": true,
  "message": "Occasions retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "slug": "summer-sale-2024",
        "vendor_id": 5,
        "start_date": "2024-06-01",
        "end_date": "2024-08-31",
        "is_active": true,
        "is_featured": true,
        "created_at": "2024-01-15T10:30:00Z",
        "vendor": {
          "id": 5,
          "name": "Electronics Store"
        },
        "translations": [
          {
            "lang_key": "name",
            "lang_value": "Summer Sale 2024"
          }
        ]
      }
    ],
    "total": 25,
    "per_page": 15,
    "last_page": 2
  }
}
```

---

### 2. Get Featured Occasions (Public)
**Endpoint:** `GET /api/occasions/featured`

**Description:** Get featured occasions.

**Query Parameters:**
- `limit` (integer, optional) - Number of occasions to return (default: 10)

**Response:**
```json
{
  "status": true,
  "message": "Occasions retrieved successfully",
  "data": [
    {
      "id": 1,
      "slug": "summer-sale-2024",
      "is_featured": true,
      ...
    }
  ]
}
```

---

### 3. Get Occasion by ID (Public)
**Endpoint:** `GET /api/occasions/{id}`

**Description:** Retrieve a single occasion with all details and products.

**Response:**
```json
{
  "status": true,
  "message": "Occasion retrieved successfully",
  "data": {
    "id": 1,
    "slug": "summer-sale-2024",
    "vendor_id": 5,
    "start_date": "2024-06-01",
    "end_date": "2024-08-31",
    "is_active": true,
    "is_featured": true,
    "vendor": {
      "id": 5,
      "name": "Electronics Store"
    },
    "occasionProducts": [
      {
        "id": 1,
        "occasion_id": 1,
        "vendor_product_variant_id": 10,
        "special_price": 99.99,
        "position": 1,
        "vendorProductVariant": {
          "id": 10,
          "sku": "PROD-001",
          "price": 149.99,
          "vendorProduct": {
            "id": 5,
            "product": {
              "id": 20,
              "title": "Product Name",
              "mainImage": {
                "path": "products/image.jpg"
              }
            }
          }
        }
      }
    ]
  }
}
```

---

### 4. Get Occasions by Vendor (Public)
**Endpoint:** `GET /api/occasions/vendor/{vendorId}`

**Description:** Get all active occasions for a specific vendor.

**Query Parameters:**
- `search` (string, optional) - Search by occasion name
- `per_page` (integer, optional) - Items per page (default: 15)
- `page` (integer, optional) - Page number (default: 1)

**Response:** Same as endpoint #1

---

### 5. Get Occasion Products (Public)
**Endpoint:** `GET /api/occasions/{id}/products`

**Description:** Get all products in an occasion with special prices.

**Query Parameters:**
- `per_page` (integer, optional) - Items per page (default: 15)
- `page` (integer, optional) - Page number (default: 1)

**Response:**
```json
{
  "status": true,
  "message": "Products retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "occasion_id": 1,
        "vendor_product_variant_id": 10,
        "special_price": 99.99,
        "position": 1,
        "vendorProductVariant": {
          "id": 10,
          "sku": "PROD-001",
          "price": 149.99,
          "product": {
            "id": 20,
            "title": "Product Name",
            "brand": {
              "id": 1,
              "name": "Brand Name"
            },
            "mainImage": {
              "path": "products/image.jpg"
            }
          }
        }
      }
    ],
    "total": 50,
    "per_page": 15,
    "last_page": 4
  }
}
```

---

### 6. Create Occasion (Authenticated - Vendors Only)
**Endpoint:** `POST /api/occasions`

**Authentication:** Required (Bearer token)

**Request Body:**
```json
{
  "start_date": "2024-06-01",
  "end_date": "2024-08-31",
  "is_active": true,
  "is_featured": false,
  "image": "file",
  "translations": {
    "1": {
      "name": "Summer Sale 2024",
      "title": "Amazing Summer Discounts",
      "sub_title": "Up to 50% off"
    },
    "2": {
      "name": "تخفيف الصيف 2024",
      "title": "خصومات صيفية مذهلة",
      "sub_title": "حتى 50% خصم"
    }
  },
  "variants": [
    {
      "vendor_product_variant_id": 10,
      "special_price": 99.99
    },
    {
      "vendor_product_variant_id": 11,
      "special_price": 149.99
    }
  ]
}
```

**Response:**
```json
{
  "status": true,
  "message": "Occasion created successfully",
  "data": {
    "id": 1,
    "slug": "summer-sale-2024",
    "vendor_id": 5,
    "start_date": "2024-06-01",
    "end_date": "2024-08-31",
    "is_active": true,
    "is_featured": false,
    "created_at": "2024-01-15T10:30:00Z"
  }
}
```

**Error Response (403):**
```json
{
  "status": false,
  "message": "Unauthorized"
}
```

---

### 7. Update Occasion (Authenticated - Vendors Only)
**Endpoint:** `PUT /api/occasions/{id}`

**Authentication:** Required (Bearer token)

**Request Body:** Same as Create (all fields optional for update)

**Response:** Same as Create

**Error Response (403):**
```json
{
  "status": false,
  "message": "Unauthorized"
}
```

**Error Response (404):**
```json
{
  "status": false,
  "message": "Occasion not found"
}
```

---

### 8. Delete Occasion (Authenticated - Vendors Only)
**Endpoint:** `DELETE /api/occasions/{id}`

**Authentication:** Required (Bearer token)

**Response:**
```json
{
  "status": true,
  "message": "Occasion deleted successfully"
}
```

**Error Response (403):**
```json
{
  "status": false,
  "message": "Unauthorized"
}
```

**Error Response (404):**
```json
{
  "status": false,
  "message": "Occasion not found"
}
```

---

## Error Handling

### Common Error Responses

**400 Bad Request:**
```json
{
  "status": false,
  "message": "Validation failed",
  "errors": {
    "translations": ["Translations are required"],
    "start_date": ["Start date must be a valid date"]
  }
}
```

**401 Unauthorized:**
```json
{
  "status": false,
  "message": "Unauthenticated"
}
```

**403 Forbidden:**
```json
{
  "status": false,
  "message": "Unauthorized"
}
```

**404 Not Found:**
```json
{
  "status": false,
  "message": "Occasion not found"
}
```

**500 Server Error:**
```json
{
  "status": false,
  "message": "Error: {error details}"
}
```

---

## Authentication

### Getting a Bearer Token

1. **Login Endpoint:**
```
POST /api/login
Content-Type: application/json

{
  "email": "vendor@example.com",
  "password": "password123"
}
```

2. **Response:**
```json
{
  "token": "your_bearer_token_here"
}
```

3. **Using the Token:**
```
Authorization: Bearer your_bearer_token_here
```

---

## Usage Examples

### cURL Examples

**Get All Occasions:**
```bash
curl -X GET "http://localhost:8000/api/occasions?per_page=10" \
  -H "Accept: application/json"
```

**Get Featured Occasions:**
```bash
curl -X GET "http://localhost:8000/api/occasions/featured?limit=5" \
  -H "Accept: application/json"
```

**Get Occasion by ID:**
```bash
curl -X GET "http://localhost:8000/api/occasions/1" \
  -H "Accept: application/json"
```

**Create Occasion (with authentication):**
```bash
curl -X POST "http://localhost:8000/api/occasions" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "start_date": "2024-06-01",
    "end_date": "2024-08-31",
    "is_active": true,
    "translations": {
      "1": {
        "name": "Summer Sale 2024"
      }
    }
  }'
```

**Update Occasion:**
```bash
curl -X PUT "http://localhost:8000/api/occasions/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "is_active": false
  }'
```

**Delete Occasion:**
```bash
curl -X DELETE "http://localhost:8000/api/occasions/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## JavaScript/Fetch Examples

**Get All Occasions:**
```javascript
fetch('/api/occasions?per_page=10')
  .then(response => response.json())
  .then(data => console.log(data));
```

**Create Occasion:**
```javascript
fetch('/api/occasions', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    start_date: '2024-06-01',
    end_date: '2024-08-31',
    is_active: true,
    translations: {
      1: {
        name: 'Summer Sale 2024'
      }
    }
  })
})
  .then(response => response.json())
  .then(data => console.log(data));
```

---

## Rate Limiting

No rate limiting is currently implemented. Please use responsibly.

---

## Versioning

Current API Version: **v1**

All endpoints are under `/api/occasions`

---

## Support

For issues or questions about the API, please contact the development team.

---

## Status: PRODUCTION READY ✅

The Occasions API is fully implemented with complete CRUD operations, proper authentication, and comprehensive error handling.
