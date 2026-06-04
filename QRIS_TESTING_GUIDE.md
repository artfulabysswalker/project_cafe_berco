# QRIS Payment Testing Guide

## Quick Test Flow

### Step 1: Create Test Order
```bash
php artisan qris:test
```

**Output:**
```
✅ User found: John Doe (john@example.com)
✅ Menu item found: Americano (Rp 35,000)
✅ Order created: #123
✅ Order item added: Americano x2

📋 Order Summary:
┌─────────────────┬──────────────────┐
│ Field           │ Value            │
├─────────────────┼──────────────────┤
│ Order ID        │ #123             │
│ Customer        │ John Doe         │
│ Menu Item       │ Americano        │
│ Quantity        │ 2                │
│ Unit Price      │ Rp 35,000        │
│ Subtotal        │ Rp 70,000        │
│ Tax (10%)       │ Rp 7,000         │
│ Total           │ Rp 77,000        │
│ Payment Method  │ QRIS             │
│ Status          │ Pending          │
└─────────────────┴──────────────────┘

🚀 Next Steps for Testing:
1️⃣ Create QRIS Invoice:
   POST http://localhost:8000/xendit/qris/payment/123/invoice

2️⃣ Check Payment Status:
   GET http://localhost:8000/xendit/qris/payment/123/status

3️⃣ Simulate Webhook Callback (Test Payment):
   php artisan qris:test-webhook 123

4️⃣ Check Reconciliation:
   php artisan qris:check 123
```

---

## Testing Methods

### Method 1: Full API Testing (Recommended)

#### 1a. Create QRIS Invoice via API
```bash
curl -X POST http://localhost:8000/xendit/qris/payment/123/invoice \
  -H "Content-Type: application/json" \
  -H "X-CSRF-Token: YOUR_CSRF_TOKEN"
```

**Response:**
```json
{
  "success": true,
  "invoice_id": "inv_123abc",
  "invoice_url": "https://app.sandbox.xendit.com/web/...",
  "qris_string": "00020126360014...",
  "order_id": 123,
  "amount": 77000,
  "expires_at": "2026-06-04T15:30:00Z"
}
```

#### 1b. Test Xendit QRIS Endpoint
```bash
curl -X GET http://localhost:8000/xendit/test
```

**Response:**
```json
{
  "status": "success",
  "message": "✅ Xendit SDK berhasil diinisialisasi!",
  "config": {
    "api_key_set": true,
    "public_key_set": true,
    "environment": "development"
  }
}
```

---

### Method 2: CLI Testing (Simplest)

#### 2a. Create Test Order
```bash
php artisan qris:test
# or
php artisan qris:test --user-id=2
```

#### 2b. Simulate Payment Webhook
```bash
php artisan qris:test-webhook 123
```

**Output:**
```
📤 Webhook Payload:
{
  "id": "inv_123abc",
  "external_id": "QRIS-ORDER-123-1717507200",
  "status": "PAID",
  "amount": 77000,
  "currency": "IDR",
  "description": "Order #123 - John Doe",
  "paid_at": "2026-06-04T15:27:05Z"
}

✅ QRIS Transaction marked as PAID
✅ Reconciliation: MATCHED

📋 Updated Order Status:
┌─────────────────┬──────────────────┐
│ Field           │ Value            │
├─────────────────┼──────────────────┤
│ Order ID        │ #123             │
│ Payment Status  │ Paid             │
│ Order Status    │ pending          │
│ Total Amount    │ Rp 77,000        │
└─────────────────┴──────────────────┘

✅ Webhook simulation completed successfully!
```

#### 2c. Check Transaction Status
```bash
php artisan qris:check 123
```

**Output:**
```
Transaction ID: #1
Order ID: #123
Amount: Rp 77,000
Status: paid
Payment Channel: qris
Customer: John Doe (john@example.com)
Created: 2026-06-04 15:15:00
Expires: 2026-06-04 15:45:00
Paid: 2026-06-04 15:27:05
```

#### 2d. Run Reconciliation
```bash
php artisan qris:reconcile --force
```

**Output:**
```
🔄 Starting QRIS Reconciliation...
📅 Will reconcile transactions from 2026-06-04 to 2026-06-04
📊 Found 1 paid transactions
✅ Transaction #1 - MATCHED (Rp 77,000)

📈 Reconciliation Summary:
✅ Matched: 1
⚠️ Mismatched: 0
❌ Failed: 0
```

---

### Method 3: Browser Testing (Realistic)

#### 3a. Start Local Server
```bash
php artisan serve
# Server running: http://localhost:8000
```

#### 3b. Login as Customer
- Visit: `http://localhost:8000/`
- Login with customer credentials
- Navigate to menu

#### 3c. Place Order with QRIS
1. Add items to cart
2. Go to checkout
3. Select payment method: **QRIS**
4. Click "Order"
5. On payment page, click "Buat Kode QRIS Baru"
6. Wait for QRIS QR code to appear

#### 3d. View QRIS Payment Page
- URL: `http://localhost:8000/xendit/qris/payment/123`
- Shows order summary
- Displays QRIS payment instructions
- Real-time status polling

#### 3e. Simulate Payment Success
```bash
php artisan qris:test-webhook 123
```

Then refresh payment page - should redirect to receipt

---

## Verification Steps

### Check Database Records

#### View QRIS Transactions
```bash
php artisan tinker
>>> \App\Models\QrisTransaction::all()
>>> \App\Models\QrisTransaction::where('id_order', 123)->first()
```

#### View Reconciliation Records
```bash
php artisan tinker
>>> \App\Models\QrisReconciliation::all()
>>> \App\Models\QrisReconciliation::where('reconciliation_status', 'matched')->count()
```

### Check Logs
```bash
tail -f storage/logs/laravel.log | grep -i qris
```

**Expected Log Entries:**
```
[2026-06-04 15:15:00] local.INFO: Xendit QRIS Invoice Creation ...
[2026-06-04 15:27:00] local.INFO: QRIS Callback Received ...
[2026-06-04 15:27:00] local.INFO: QRIS Callback Processed ...
```

---

## Common Test Scenarios

### Scenario 1: Successful Payment
```bash
# 1. Create test order
php artisan qris:test

# 2. Create QRIS invoice
# POST /xendit/qris/payment/123/invoice

# 3. Simulate successful payment
php artisan qris:test-webhook 123

# 4. Verify
php artisan qris:check 123
php artisan qris:reconcile --force
```

### Scenario 2: Payment Expiration
```bash
# 1. Create test order
php artisan qris:test

# 2. Create QRIS invoice (expires in 30 min)

# 3. Wait or manually check expiration
php artisan qris:check 123

# 4. If expired, create new invoice
# POST /xendit/qris/payment/123/invoice
```

### Scenario 3: Failed Payment
```bash
# 1. Create test order
php artisan qris:test

# 2. Simulate failed payment
# Modify test-webhook to use status FAILED

# 3. Verify failure
php artisan qris:check 123
```

### Scenario 4: Amount Mismatch
```bash
# 1. Create test order
php artisan qris:test

# 2. Manually update bank amount to different value
php artisan tinker
>>> $rec = \App\Models\QrisReconciliation::first()
>>> $rec->bank_amount = 78000
>>> $rec->save()

# 3. Run reconciliation
php artisan qris:reconcile --force

# 4. Should show MISMATCH
```

---

## Expected Database State After Payment

### QRIS Transactions Table
```sql
SELECT * FROM qris_transactions WHERE id_order = 123;

┌──────────────────┬─────────────┬──────────────┬────────┐
│ id_qris_trans... │ id_order    │ amount       │ status │
├──────────────────┼─────────────┼──────────────┼────────┤
│ 1                │ 123         │ 77000.00     │ paid   │
└──────────────────┴─────────────┴──────────────┴────────┘
```

### QRIS Reconciliations Table
```sql
SELECT * FROM qris_reconciliations WHERE id_qris_transaction = 1;

┌───────────────┬──────────────────┬────────────────────┬─────────────┐
│ id_reconcil... │ system_amount    │ bank_amount        │ status      │
├───────────────┼──────────────────┼────────────────────┼─────────────┤
│ 1             │ 77000.00         │ 77000.00           │ matched     │
└───────────────┴──────────────────┴────────────────────┴─────────────┘
```

### Orders Table
```sql
SELECT id_order, status_pembayaran, payment_method FROM orders WHERE id_order = 123;

┌──────────┬──────────────────┬─────────────────┐
│ id_order │ status_pembayaran │ payment_method  │
├──────────┼──────────────────┼─────────────────┤
│ 123      │ Paid             │ qris            │
└──────────┴──────────────────┴─────────────────┘
```

---

## Troubleshooting During Testing

### Issue: "QRIS transaction not found"
**Solution:**
- Make sure QRIS invoice was created: `POST /xendit/qris/payment/{order}/invoice`
- Check if QrisTransaction record exists: `php artisan qris:check {order_id}`

### Issue: "Xendit API Error"
**Solution:**
- Verify XENDIT_SECRET_KEY in `.env`
- Check Xendit credentials: `php artisan xendit:test`
- Ensure network connectivity

### Issue: Payment not updating to "Paid"
**Solution:**
- Simulate webhook: `php artisan qris:test-webhook {order_id}`
- Check logs: `tail -f storage/logs/laravel.log`
- Verify webhook handler is working

### Issue: Amount mismatch in reconciliation
**Solution:**
- This is expected for testing - in production, amounts should match
- Use admin dashboard to review and resolve mismatches

---

## Quick Reference Commands

| Command | Purpose |
|---------|---------|
| `php artisan qris:test` | Create test order |
| `php artisan qris:test-webhook 123` | Simulate payment |
| `php artisan qris:check` | List pending transactions |
| `php artisan qris:check 123` | Check specific order |
| `php artisan qris:reconcile` | Run reconciliation |
| `php artisan xendit:test` | Test Xendit config |

---

## Testing Checklist

- [ ] Create test order: `php artisan qris:test`
- [ ] Verify order in database
- [ ] Create QRIS invoice via API
- [ ] Verify QRIS transaction created
- [ ] Check payment page displays correctly
- [ ] Simulate webhook: `php artisan qris:test-webhook {id}`
- [ ] Verify order status changed to "Paid"
- [ ] Verify reconciliation matched
- [ ] Check logs for errors
- [ ] Run reconciliation: `php artisan qris:reconcile`
- [ ] Verify email notification (check logs)
- [ ] Check admin dashboard displays transaction

---

## Next Steps After Testing

After successful testing:
1. ✅ Test in browser (place real order, don't complete payment)
2. ✅ Deploy to staging
3. ✅ Test with real Xendit sandbox credentials
4. ✅ Configure production Xendit credentials
5. ✅ Deploy to production
6. ✅ Monitor for errors and reconciliation issues

---

*Last Updated: June 4, 2026*
