# FinaFlow API Documentation (v1)

## Authentication
- **Base URL**: `/api/v1`
- **Auth Strategy**: Laravel Sanctum (Bearer Token)
- **Rate Limit**: 60 req/min

---

## 1. Authentication & Profile

### `POST /login`
Login and get access token.
- **Payload**: `email`, `password`, `device_name`
- **Response**: User object and `token`.

### `POST /logout` (Auth)
Revoke current access token.

### `GET /user` (Auth)
Get current authenticated user details.

### `PUT /profile/update` (Auth)
Update name and email.
- **Payload**: `name`, `email`

### `PUT /profile/password` (Auth)
Change password.
- **Payload**: `current_password`, `password`, `password_confirmation`

### `POST /profile/avatar` (Auth)
Upload avatar image.
- **Payload**: `avatar` (file)

---

## 2. Dashboard

### `GET /dashboard` (Auth)
Comprehensive dashboard data.
- **Includes**: Summary, Health Analysis, Distribution, Cash Flow, Accounts, Recent Transactions.

---

## 3. Transactions

### `GET /transactions` (Auth)
List transactions with filters.
- **Params**: `per_page`, `type`, `account_id`, `category_id`, `search`

### `POST /transactions` (Auth)
Create new transaction.
- **Payload**: `account_id`, `category_id`, `amount`, `type`, `transaction_date`, `description`

### `GET /transactions/{id}` (Auth)
Show single transaction detail.

### `PUT /transactions/{id}` (Auth)
Update existing transaction.

### `DELETE /transactions/{id}` (Auth)
Delete transaction.

### `POST /transactions/scan-receipt` (Auth)
Extract data from receipt image (OCR).
- **Payload**: `receipt_image` (file)
- **Response**: Extracted `date`, `amount`, `merchant`, etc.

---

## 4. Financial Planning

### `GET /goals` (Auth)
List all financial goals.

### `POST /goals` (Auth)
Create new financial goal.
- **Payload**: `name`, `category`, `type`, `target_amount`, `target_date`

### `POST /goals/{id}/progress` (Auth)
Add progress to a goal.
- **Payload**: `amount`, `note`

### `GET /budgets` (Auth)
List active budgets and their status.

### `PATCH /budgets/{id}/spent` (Auth)
Quick update for budget spent amount.

---

## 5. Metadata (Dropdowns)

### `GET /categories` (Auth)
List categories for forms.

### `GET /accounts` (Auth)
List active accounts for forms.

---

## Error Handling
- **401**: Unauthorized (Token invalid/missing)
- **403**: Forbidden (Accessing someone else's data)
- **422**: Validation Error (Check `errors` object)
- **500**: Server Error
