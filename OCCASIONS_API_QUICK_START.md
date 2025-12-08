# Occasions API - Quick Start Guide

## Architecture Pattern

The API follows the **Repository → Service → Controller** pattern for clean, maintainable code.

```
Request
  ↓
Controller (OccasionApiController)
  ↓
Service (OccasionService)
  ↓
Repository (OccasionRepository)
  ↓
Database
```

---

## File Structure

```
Modules/CatalogManagement/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── OccasionApiController.php      ← API Endpoints
│   │   └── Requests/
│   │       ├── StoreOccasionRequest.php           ← Create validation
│   │       └── UpdateOccasionRequest.php          ← Update validation
│   ├── Services/
│   │   └── OccasionService.php                    ← Business logic
│   ├── Repositories/
│   │   └── OccasionRepository.php                 ← Database queries
│   └── Models/
│       └── Occasion.php                           ← Model definition
└── routes/
    └── api.php                                    ← API routes
```

---

## Quick API Reference

### Public Endpoints (No Auth Required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/occasions` | List all occasions |
| GET | `/api/occasions/featured` | Get featured occasions |
| GET | `/api/occasions/{id}` | Get single occasion |
| GET | `/api/occasions/vendor/{vendorId}` | Get vendor's occasions |
| GET | `/api/occasions/{id}/products` | Get occasion products |

### Authenticated Endpoints (Vendors Only)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/occasions` | Create occasion |
| PUT | `/api/occasions/{id}` | Update occasion |
| DELETE | `/api/occasions/{id}` | Delete occasion |

---

## Common Requests

### 1. Get All Occasions
```bash
curl "http://localhost:8000/api/occasions?per_page=10&page=1"
```

### 2. Search Occasions
```bash
curl "http://localhost:8000/api/occasions?search=summer&vendor_id=5"
```

### 3. Get Featured Occasions
```bash
curl "http://localhost:8000/api/occasions/featured?limit=5"
```

### 4. Get Single Occasion
```bash
curl "http://localhost:8000/api/occasions/1"
```

### 5. Create Occasion (Vendor)
```bash
curl -X POST "http://localhost:8000/api/occasions" \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "start_date": "2024-06-01",
    "end_date": "2024-08-31",
    "is_active": true,
    "translations": {
      "1": {"name": "Summer Sale 2024"},
      "2": {"name": "تخفيف الصيف 2024"}
    },
    "variants": [
      {"vendor_product_variant_id": 10, "special_price": 99.99}
    ]
  }'
```

### 6. Update Occasion (Vendor)
```bash
curl -X PUT "http://localhost:8000/api/occasions/1" \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"is_active": false}'
```

### 7. Delete Occasion (Vendor)
```bash
curl -X DELETE "http://localhost:8000/api/occasions/1" \
  -H "Authorization: Bearer TOKEN"
```

---

## Response Format

All responses follow this format:

```json
{
  "status": true/false,
  "message": "Human readable message",
  "data": {
    // Response data here
  }
}
```

---

## Error Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request (validation error) |
| 401 | Unauthorized (not logged in) |
| 403 | Forbidden (not allowed) |
| 404 | Not Found |
| 500 | Server Error |

---

## Authentication

1. **Get Token:**
```bash
curl -X POST "http://localhost:8000/api/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "vendor@example.com",
    "password": "password123"
  }'
```

2. **Use Token:**
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  "http://localhost:8000/api/occasions"
```

---

## Key Features

✅ **Repository Pattern** - Clean data access layer
✅ **Service Layer** - Business logic separation
✅ **Request Validation** - Form request classes
✅ **Authorization** - Vendor-only operations
✅ **Error Handling** - Comprehensive error responses
✅ **Pagination** - Built-in pagination support
✅ **Filtering** - Search and filter capabilities
✅ **Multi-language** - Translation support
✅ **RESTful** - Standard REST conventions
✅ **Documented** - Complete API documentation

---

## Testing with Postman

1. **Create Collection:** "Occasions API"
2. **Add Environment Variable:**
   - Key: `token`
   - Value: (get from login endpoint)
   - Key: `base_url`
   - Value: `http://localhost:8000`

3. **Create Requests:**
   - GET {{base_url}}/api/occasions
   - GET {{base_url}}/api/occasions/featured
   - POST {{base_url}}/api/occasions (with Bearer {{token}})
   - etc.

---

## Debugging

### Enable Query Logging
```php
// In controller
DB::enableQueryLog();
// ... your code ...
dd(DB::getQueryLog());
```

### Check Authorization
```php
// In controller
if (!auth()->check()) {
    return response()->json(['error' => 'Not authenticated'], 401);
}

if (!in_array(auth()->user()->user_type_id, [3, 4])) {
    return response()->json(['error' => 'Not a vendor'], 403);
}
```

---

## Performance Tips

1. **Use Pagination** - Always paginate large datasets
2. **Eager Load** - Use `with()` to avoid N+1 queries
3. **Cache** - Cache featured occasions
4. **Index** - Index frequently searched columns

---

## Next Steps

1. Test all endpoints with Postman or cURL
2. Implement frontend integration
3. Add rate limiting if needed
4. Monitor API performance
5. Gather user feedback

---

## Support

For detailed API documentation, see: `OCCASIONS_API_DOCUMENTATION.md`

For code examples, see: `Modules/CatalogManagement/app/Http/Controllers/Api/OccasionApiController.php`

---

## Status: PRODUCTION READY ✅

The Occasions API is fully implemented and ready for integration!
