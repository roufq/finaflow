# API Documentation

## Authentication
- All endpoints below require authenticated web session (`auth` middleware).
- Rate limit: `60 req/min` via `throttle:60,1`.

## Versioning
- Prefix: `/api/v1/...`

## Endpoints

### GET `/api/v1/transactions`
List transactions for the authenticated user.

**Query Params**
- `per_page` (int, optional, 1-100, default 15)
- `type` (string, optional, `income` or `expense`)
- `account_id` (int, optional)
- `category_id` (int, optional)

**Response 200**
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 5,
      "account_id": 3,
      "category_id": 7,
      "transaction_date": "2025-11-27",
      "type": "expense",
      "amount": 120000,
      "description": "Coffee shop",
      "account": { "id": 3, "name": "Main Wallet" },
      "category": { "id": 7, "name": "Food & Drinks" }
    }
  ],
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

**Errors**
- 401/403 if session invalid.
- Validation 422 for invalid query params.

## Notes
- Responses use `TransactionResource` for consistent shape.
- Extend versioning with additional endpoints under `/api/v1` to keep compatibility.
