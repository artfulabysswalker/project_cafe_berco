# Xendit Payment Testing Guide

## Setup Completed ✓

Your Xendit payment integration has been successfully configured with:

- **Secret Key**: Configured in `.env`
- **Public Key**: Configured in `.env`  
- **Mode**: Development (xnd_development_*)
- **Base URL**: https://app.xendit.co (Development)

## Available Testing Methods

### 1. Command Line Testing

```bash
# Test with default amount (Rp 100,000)
php artisan xendit:test

# Test with custom amount
php artisan xendit:test --amount=250000

# Test with custom description
php artisan xendit:test --amount=150000 --description="Test Order #123"
```

### 2. API Endpoint Testing

#### Create Test Invoice
```bash
POST /xendit/test/invoice
Content-Type: application/json

{
  "amount": 100000,
  "description": "Test Purchase",
  "customer_name": "John Doe",
  "email": "john@example.com",
  "phone": "08123456789"
}
```

**Response:**
```json
{
  "success": true,
  "invoice_id": "61c88b01e26d640b1bfa7c2e",
  "invoice_url": "https://invoice.xendit.co/...",
  "amount": 100000,
  "status": "PENDING"
}
```

#### Create Invoice for Existing Order
```bash
POST /xendit/payment/{order-id}/invoice
```

#### Check Payment Status
```bash
GET /xendit/payment/{order-id}/status
```

**Response:**
```json
{
  "success": true,
  "payment_status": "pending",
  "invoice_status": "PENDING",
  "order_status": "Pending",
  "amount": 100000,
  "paid_amount": 0
}
```

#### Redirect to Payment Page
```bash
GET /xendit/payment/{order-id}/redirect
```

## Test Payment Methods in Development

### 1. Bank Transfer (Virtual Account)
- BCA, BNI, BRI, Mandiri, Permata, CIMB Niaga available
- Automatically generated virtual account number
- Payment confirmed immediately

### 2. E-Wallet
- **OVO**: Use test number `08123456789`
- **DANA**: Use test credentials
- **LINKAJA**: Use test credentials
- **ASTRAPAY**: Use test credentials

### 3. Card Payment (3DS)
- **Visa Test Cards**:
  - `4111 1111 1111 1111` - Success
  - `4000 0000 0000 0002` - Decline
  - `5555 5555 5555 4444` - MasterCard Success
- Expiry: Any future date
- CVV: Any 3 digits

### 4. Retail/Over-the-Counter
- Alfamart, Indomaret available
- Payment code will be generated
- Pay at retail location

## Webhook Configuration

### Setting Up Webhook in Xendit Dashboard

1. Go to **Developers** → **API Callback URL**
2. Add webhook URL: `https://yourapp.com/xendit/payment/callback`
3. Select events to monitor:
   - Invoice Paid
   - Invoice Expired
   - Invoice Failed
4. Use your secret key for webhook verification

### Webhook Sample Payload

```json
{
  "id": "61c88b01e26d640b1bfa7c2e",
  "external_id": "ORDER-123-1234567890",
  "user_id": "user-123",
  "status": "PAID",
  "merchant_name": "Cafe Berco",
  "amount": 100000,
  "paid_amount": 100000,
  "payer_email": "customer@example.com",
  "description": "Order #123 - John Doe",
  "created": "2024-01-01T10:00:00.000Z",
  "updated": "2024-01-01T10:05:00.000Z"
}
```

## Payment Statuses

| Status | Description |
|--------|-------------|
| `PENDING` | Invoice created, waiting for payment |
| `PAID` / `SETTLED` | Payment successfully received |
| `EXPIRED` | Payment deadline passed |
| `FAILED` | Payment rejected |
| `VOIDED` | Invoice canceled |

## Order Status Updates

When payment is received, the order status automatically updates:

```
Payment Status: pending → paid
Order Status: Pending → Paid
```

Confirmation email is sent to customer.

## Important Notes for Testing

⚠️ **Development Credentials Only**
- These keys are for testing only
- Never commit real production keys to git
- Rotate keys regularly

📋 **Testing Checklist**
- [ ] Test with different payment methods
- [ ] Verify webhook callback is received
- [ ] Check order status updates
- [ ] Verify email notifications sent
- [ ] Test payment failure scenarios
- [ ] Test timeout/expiry scenarios

## Troubleshooting

### Invoice Creation Fails
```
Error: Unauthorized
```
**Solution**: Check XENDIT_SECRET_KEY in .env is correct

### Webhook Not Received
1. Verify webhook URL in Xendit dashboard
2. Check firewall/SSL issues
3. Look for CSRF token issues (should be whitelisted)
4. Check Laravel logs: `storage/logs/laravel.log`

### Payment Status Not Updating
1. Verify database migrations ran
2. Check if Payment model exists
3. Review webhook logs in Xendit dashboard

## Next Steps

1. ✅ Copy configuration keys (already done)
2. 📝 Configure webhook URL in Xendit dashboard
3. 🧪 Run first test invoice: `php artisan xendit:test`
4. 💳 Complete test payment
5. ✔️ Verify webhook callback received
6. 🚀 Integrate into order flow

## Useful Links

- [Xendit API Docs](https://docs.xendit.co)
- [Xendit Dashboard](https://dashboard.xendit.co)
- [Payment Methods](https://docs.xendit.co/api-reference/#xendit-payment-methods)
- [Webhook Configuration](https://docs.xendit.co/api-reference/#xendit-webhooks)
- [Invoice API](https://docs.xendit.co/api-reference/#create-an-invoice)
