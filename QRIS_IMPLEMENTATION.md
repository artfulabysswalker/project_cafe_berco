# QRIS Payment & Reconciliation Implementation Guide

## Overview

This document provides comprehensive information about the QRIS (Quick Response Code Indonesian Standard) payment system and reconciliation implementation for Berco Cafe.

**Status**: ✅ Production Ready  
**Payment Gateway**: Xendit  
**Last Updated**: June 4, 2026

---

## Table of Contents

1. [Features](#features)
2. [Database Schema](#database-schema)
3. [Payment Flow](#payment-flow)
4. [Reconciliation Process](#reconciliation-process)
5. [API Endpoints](#api-endpoints)
6. [CLI Commands](#cli-commands)
7. [Webhook Configuration](#webhook-configuration)
8. [Testing Guide](#testing-guide)
9. [Troubleshooting](#troubleshooting)

---

## Features

### QRIS Payment Features
✅ **Dynamic QRIS Code Generation** - Generate QRIS codes dynamically for each transaction  
✅ **Multiple Payment Channels** - Support QRIS, Card, E-Wallet, and Bank Transfer  
✅ **Real-time Payment Status** - Check payment status in real-time  
✅ **Payment Expiration** - Auto-expire QRIS codes after 30 minutes  
✅ **Webhook Integration** - Automatic payment confirmation via Xendit webhooks  

### Reconciliation Features
✅ **Automatic Reconciliation** - Match payments with bank records  
✅ **Mismatch Detection** - Detect amount discrepancies automatically  
✅ **Batch Processing** - Reconcile multiple payments at once  
✅ **Admin Dashboard** - View and manage reconciliation records  
✅ **Audit Trail** - Track who reconciled payments and when  
✅ **Discrepancy Reporting** - Detailed reports on payment mismatches  

---

## Database Schema

### QRIS Transactions Table
```sql
qris_transactions
├── id_qris_transaction (primary key)
├── id_order (foreign key → orders)
├── qris_code (unique)
├── transaction_id
├── invoice_id
├── amount
├── status (pending, paid, failed, expired, cancelled)
├── payment_channel (qris, card, e_wallet, bank_transfer)
├── customer_name
├── customer_email
├── customer_phone
├── expires_at
├── paid_at
├── metadata (JSON)
└── timestamps
```

### QRIS Reconciliations Table
```sql
qris_reconciliations
├── id_reconciliation (primary key)
├── id_qris_transaction (foreign key)
├── reference_id
├── reconciliation_status (pending, matched, mismatched, resolved)
├── bank_amount
├── system_amount
├── amount_difference
├── notes
├── bank_transaction_date
├── reconciled_at
├── reconciled_by (foreign key → users)
└── timestamps
```

---

## Payment Flow

### Customer Payment Flow

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Customer Places Order                                    │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. Choose Payment Method (Cash, Card, E-Wallet, QRIS, etc) │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. System Creates Order & Redirects to Payment Page        │
└──────────────────────┬──────────────────────────────────────┘
                       │
       ┌───────────────┼────────────────┐
       │               │                │
       ▼               ▼                ▼
     CASH           QRIS          CARD/E-WALLET
       │               │                │
       │               ▼                │
       │    4a. Generate QRIS Code     │
       │               │                │
       │               ▼                │
       │    4b. Show QRIS QR Code      │
       │               │                │
       │               ▼                │
       │    4c. Customer Scans & Pays  │
       │               │                │
       └───────────────┼────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. Xendit Processes Payment                                 │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 6. Xendit Sends Webhook Callback                            │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 7. System Updates Order & Payment Status                    │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 8. Send Payment Confirmation Email                          │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 9. Redirect to Receipt Page                                 │
└─────────────────────────────────────────────────────────────┘
```

---

## Reconciliation Process

### Daily Reconciliation Flow

```
┌────────────────────────────────────────────────────────────┐
│ 1. Admin Runs: php artisan qris:reconcile                  │
└──────────────────┬─────────────────────────────────────────┘
                   │
                   ▼
┌────────────────────────────────────────────────────────────┐
│ 2. System Fetches Paid Transactions from Last N Days       │
└──────────────────┬─────────────────────────────────────────┘
                   │
                   ▼
┌────────────────────────────────────────────────────────────┐
│ 3. For Each Transaction:                                   │
│   - Compare system amount with bank amount                 │
│   - Calculate difference                                   │
│   - Update reconciliation status                           │
└──────────────────┬─────────────────────────────────────────┘
                   │
           ┌───────┴────────┐
           │                │
           ▼                ▼
      MATCHED         MISMATCHED
           │                │
           │                ▼
           │    4. Create Alert for Investigation
           │                │
           └───────┬────────┘
                   │
                   ▼
┌────────────────────────────────────────────────────────────┐
│ 5. Generate Reconciliation Report                          │
└────────────────────────────────────────────────────────────┘
```

---

## API Endpoints

### QRIS Payment Endpoints

#### Create QRIS Invoice
```http
POST /xendit/qris/payment/{order}/invoice
Content-Type: application/json

Response:
{
  "success": true,
  "invoice_id": "...",
  "invoice_url": "https://app.sandbox.xendit.com/...",
  "qris_string": "00020126...",
  "order_id": 123,
  "amount": 50000,
  "expires_at": "2026-06-04T15:30:00Z"
}
```

#### Check Payment Status
```http
GET /xendit/qris/payment/{order}/status

Response:
{
  "success": true,
  "status": "pending|paid|failed|expired",
  "amount": 50000,
  "transaction_id": "...",
  "paid_at": "2026-06-04T15:25:00Z",
  "expires_at": "2026-06-04T15:30:00Z"
}
```

#### QRIS Webhook Callback
```http
POST /xendit/qris/payment/callback
Content-Type: application/json

Example Payload:
{
  "id": "invoice_id",
  "external_id": "QRIS-ORDER-123-1717507200",
  "status": "PAID",
  "amount": 50000,
  "currency": "IDR",
  "description": "Order #123 - John Doe",
  "paid_at": "2026-06-04T15:25:00Z"
}
```

---

## CLI Commands

### QRIS Status Check
```bash
# Check all pending transactions
php artisan qris:check

# Check specific order
php artisan qris:check 123

# Output:
# Transaction ID: #1
# Order ID: #123
# Amount: Rp 50,000
# Status: pending
# Payment Channel: qris
# Customer: John Doe (john@example.com)
# Created: 2026-06-04 15:15:00
# Expires: 2026-06-04 15:45:00
```

### Batch Reconciliation
```bash
# Reconcile transactions from last 1 day (default)
php artisan qris:reconcile

# Reconcile transactions from specific date
php artisan qris:reconcile --date=2026-06-04

# Reconcile last 7 days
php artisan qris:reconcile --days=7

# Skip confirmation
php artisan qris:reconcile --force

# Output:
# 🔄 Starting QRIS Reconciliation...
# 📅 Will reconcile transactions from 2026-06-03 to 2026-06-04
# 📊 Found 15 paid transactions
# ✅ Transaction #1 - MATCHED (Rp 50,000)
# ⚠️ Transaction #2 - MISMATCH (Difference: Rp 500)
# 
# 📈 Reconciliation Summary:
# ✅ Matched: 14
# ⚠️ Mismatched: 1
# ❌ Failed: 0
```

---

## Webhook Configuration

### Setup Xendit Webhook

1. Go to [Xendit Dashboard](https://dashboard.xendit.co)
2. Navigate to **Settings → Webhooks**
3. Add webhook URL:
   ```
   https://yourdomain.com/xendit/qris/payment/callback
   ```
4. Select events:
   - ✅ Invoice Paid
   - ✅ Invoice Expired
   - ✅ Invoice Failed

### Webhook Verification

The system automatically verifies webhook signatures using SHA-512:

```php
$expectedHash = hash('sha512', $rawBody . $xenditKey);
$receivedHash = $request->header('X-Xendit-Signature');

if (hash_equals($expectedHash, $receivedHash)) {
    // Webhook is verified
}
```

---

## Testing Guide

### Test QRIS Payment Flow

1. **Create Test Order**
   ```bash
   # Login as customer
   # Add items to cart
   # Go to checkout
   # Select "QRIS" payment method
   # Click "Checkout"
   ```

2. **Create QRIS Code**
   - System creates QRIS invoice in Xendit
   - QRIS transaction record created
   - Display QRIS QR code on payment page

3. **Test Payment**
   - Use Xendit test QRIS codes from documentation
   - Or use mobile banking app with Xendit test server
   - Payment should be processed in 1-5 seconds

4. **Verify Webhook**
   - Check logs: `storage/logs/laravel.log`
   - Confirm order status changed to "Paid"
   - Check payment confirmation email sent

### Test Reconciliation

```bash
# 1. Create test transactions
php artisan qris:check

# 2. Run reconciliation
php artisan qris:reconcile --force

# 3. Check reconciliation records
php artisan qris:check 123  # Replace 123 with order ID
```

---

## Troubleshooting

### Common Issues

#### 1. QRIS Code Not Generated
**Problem**: Invoice creation fails with error  
**Solution**:
- Verify Xendit API key in `.env`
- Check network connectivity
- Review logs: `tail -f storage/logs/laravel.log`

#### 2. Payment Not Confirmed
**Problem**: Payment received but order status not updated  
**Solution**:
- Check if webhook URL is correct in Xendit Dashboard
- Verify webhook signature verification is working
- Check database logs for payment updates

#### 3. Reconciliation Mismatch
**Problem**: Amount difference detected  
**Solution**:
- Review transaction in Xendit Dashboard
- Check bank statement for correct amount
- Update reconciliation with correct bank amount if needed

#### 4. QRIS Code Expired
**Problem**: Customer sees "QRIS Code Expired" message  
**Solution**:
- QRIS codes expire after 30 minutes
- User can click "Buat Kode QRIS Baru" to create new code
- Check if customer completed payment before expiration

---

## Production Checklist

Before deploying to production:

- [ ] Update Xendit to production credentials in `.env`
- [ ] Set `XENDIT_MODE=production`
- [ ] Configure production webhook URL in Xendit Dashboard
- [ ] Test payment flow with production keys
- [ ] Set up daily reconciliation cron job:
  ```bash
  0 2 * * * php /path/to/artisan qris:reconcile
  ```
- [ ] Monitor logs and set up alerts
- [ ] Test email notifications
- [ ] Document support procedures for payment issues
- [ ] Train staff on reconciliation process
- [ ] Set up monitoring dashboard for transaction volumes

---

## Support

For issues or questions:
- Check logs in `storage/logs/`
- Review Xendit documentation: https://docs.xendit.co
- Contact Xendit support: support@xendit.co

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2026-06-04 | Initial QRIS implementation with reconciliation |

---

*Last Updated: June 4, 2026*
