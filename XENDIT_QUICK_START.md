# 🚀 Xendit Payment Testing - Quick Start

Konfigurasi Xendit telah selesai! Berikut cara memulai testing:

## 1. Verify Configuration ✓

Keys sudah tersimpan di `.env`:
```bash
XENDIT_SECRET_KEY=xnd_development_o8obYbCN51rhHdsgCKbx4xsDQu37ZaTYtQxuMXuO66qMFIepL81FKhCPPgrjLCP0
XENDIT_PUBLIC_KEY=xnd_public_development_2kJkE8Yp6QJsMxafDLUB2fKsSnLf8XPeDR2vOtPv4dCiD7AvkgY5Ny8t58r2ZvK
XENDIT_MODE=development
```

## 2. Quick Test Commands

### Test Invoice Creation
```bash
php artisan xendit:test
```

Output akan menampilkan invoice URL yang bisa dibuka di browser.

### Custom Amount
```bash
php artisan xendit:test --amount=250000 --description="Test Order"
```

## 3. Test via API

### Create Test Invoice
```bash
curl -X POST http://localhost:8000/xendit/test/invoice \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 100000,
    "description": "Test Payment",
    "customer_name": "John Doe",
    "email": "john@example.com",
    "phone": "08123456789"
  }'
```

Response:
```json
{
  "success": true,
  "invoice_id": "61c88b01e26d640b1bfa7c2e",
  "invoice_url": "https://invoice.xendit.co/...",
  "amount": 100000,
  "status": "PENDING"
}
```

## 4. Test Payment Methods

Xendit memberikan test credentials untuk berbagai metode pembayaran:

### Bank Transfer
- Virtual Account akan auto-generate
- Pembayaran langsung dikonfirmasi

### E-Wallet
- **OVO**: 08123456789
- **DANA**: Test credentials provided
- **LINKAJA**: Test credentials provided

### Card
- **Visa Test**: 4111 1111 1111 1111
- **MasterCard**: 5555 5555 5555 4444
- **Expiry**: Any future date
- **CVV**: Any 3 digits

## 5. Production Checklist

Sebelum production:

- [ ] Update Xendit keys untuk production
- [ ] Configure webhook URL di Xendit Dashboard
- [ ] Update `XENDIT_MODE=production`
- [ ] Implement proper error handling
- [ ] Add logging untuk debugging
- [ ] Test semua payment methods
- [ ] Verify email notifications
- [ ] Check payment status updates

## 6. Webhook Setup

Di Xendit Dashboard:
1. Dashboard → Developers → API Callback URL
2. Add URL: `https://yourdomain.com/xendit/payment/callback`
3. Test webhook notification

## 7. Useful Commands

```bash
# Check Laravel artisan commands
php artisan list xendit

# Check payment history
php artisan tinker
> App\Models\Payment::latest()->first();

# Clear cache if needed
php artisan cache:clear
php artisan config:clear
```

## 8. Debugging

### Check Xendit Connection
```bash
php artisan tinker
> Xendit::setApiKey(config('services.xendit.secret_key'))
> \Xendit\Invoice::list()
```

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Test Invoice Status
```bash
GET /xendit/payment/{order-id}/status
```

## 9. Common Issues

### "Unauthorized" Error
- Check XENDIT_SECRET_KEY is correct
- Verify .env file is loaded: `php artisan env`

### Invoice Not Found
- Verify invoice was created successfully
- Check database for Payment record

### Webhook Not Working
- Check firewall/SSL issues
- Verify CSRF token is whitelisted ✓ (already done)
- Test webhook in Xendit Dashboard

## 10. Next Steps

1. ✅ Run first test: `php artisan xendit:test`
2. ✅ Open invoice URL di browser
3. ✅ Complete test payment
4. ✅ Check webhook callback (check logs)
5. ✅ Verify order status updated
6. 🚀 Ready for real testing!

---

**Need Help?**
- 📖 Full Guide: See `XENDIT_TESTING.md`
- 🔗 Xendit API: https://docs.xendit.co
- 📧 Support: Your support team
