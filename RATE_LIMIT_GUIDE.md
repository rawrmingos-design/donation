# 🚦 Rate Limiting Guide - Donation Platform

## ❌ "Too Many Attempts" Error Solutions

### **Quick Fix Commands**

#### **1. Clear All Rate Limits (Immediate Relief)**
```bash
# Clear all rate limiting data
php artisan rate-limit:clear

# Clear all caches
php artisan optimize:clear

# Restart development server
php artisan serve
```

#### **2. Clear Specific IP Rate Limits**
```bash
# Clear rate limits for your IP
php artisan rate-limit:clear --ip=127.0.0.1

# Clear rate limits for specific user
php artisan rate-limit:clear --user=1
```

#### **3. Emergency Reset (Nuclear Option)**
```bash
# Clear everything and reset
php artisan rate-limit:clear --all
php artisan cache:flush
php artisan config:clear
```

---

## 🔧 **Rate Limiting Configuration**

### **Current Limits (After Optimization)**

#### **Development Environment (APP_ENV=local)**
- **GET Requests**: No rate limiting (unlimited browsing)
- **Authenticated Users**: 50% more requests allowed
- **General Limits**: 3x more generous than production

#### **Production Environment**
```yaml
Global Browsing: 120 requests/minute (2x normal)
API Calls: 60 requests/minute
Authentication: 15 attempts/5 minutes
Donations: 15 requests/2 minutes
Campaign Management: 15 requests/10 minutes
File Uploads: 30 requests/minute
Social Sharing: 180 requests/minute (3x normal)
```

### **Smart Adjustments**
- ✅ **Authenticated users** get 50% more requests
- ✅ **Development environment** gets 3x more requests
- ✅ **GET requests** are unlimited in development
- ✅ **Faster decay** times for quicker recovery

---

## 🛠️ **Troubleshooting Steps**

### **Step 1: Identify the Issue**
Check browser console or network tab for 429 errors:
```
Status: 429 Too Many Requests
Response: "Rate limit exceeded. Try again in X seconds."
```

### **Step 2: Quick Resolution**
```bash
# Method 1: Clear rate limits
php artisan rate-limit:clear

# Method 2: Clear all caches
php artisan optimize:clear

# Method 3: Restart server
php artisan serve
```

### **Step 3: Check Environment**
```bash
# Verify you're in development
php artisan config:show app.env

# Should show: local
```

### **Step 4: Test Navigation**
- Try browsing different pages
- Check if error persists
- Monitor browser console

---

## ⚙️ **Customizing Rate Limits**

### **Adjust Limits in Routes**
Edit `routes/web.php`:
```php
// Current: 10 donations per 2 minutes
Route::post('/campaigns/{campaign:slug}/donate', [DonationController::class, 'store'])
    ->middleware('rate.limit:donation,10,2');

// Increase to: 20 donations per 5 minutes
Route::post('/campaigns/{campaign:slug}/donate', [DonationController::class, 'store'])
    ->middleware('rate.limit:donation,20,5');
```

### **Modify Middleware Logic**
Edit `app/Http/Middleware/RateLimitMiddleware.php`:
```php
// Increase development multiplier
if ($isLocal) {
    $maxAttempts *= 5; // Change from 3 to 5
    $decayMinutes = max(1, $decayMinutes / 3); // Faster decay
}
```

### **Disable Rate Limiting (Emergency)**
Comment out in `bootstrap/app.php`:
```php
$middleware->web(append: [
    // RateLimitMiddleware::class, // Temporarily disabled
    SecurityHeadersMiddleware::class,
    // ... other middleware
]);
```

---

## 📊 **Rate Limit Monitoring**

### **Check Current Status**
```bash
# View security monitoring
php artisan security:monitor

# Check specific metrics
php artisan tinker
>>> RateLimiter::attempts('global:127.0.0.1')
>>> RateLimiter::availableIn('global:127.0.0.1')
```

### **Real-time Monitoring**
Check response headers in browser:
```
X-RateLimit-Limit: 120
X-RateLimit-Remaining: 95
Retry-After: 60 (if rate limited)
```

---

## 🎯 **Best Practices**

### **For Development**
1. ✅ Use `APP_ENV=local` for generous limits
2. ✅ Clear rate limits when testing: `php artisan rate-limit:clear`
3. ✅ Monitor browser console for 429 errors
4. ✅ Use authenticated accounts for higher limits

### **For Production**
1. ✅ Keep strict limits for security
2. ✅ Monitor rate limit metrics
3. ✅ Implement user feedback for rate limits
4. ✅ Use CDN for static assets

### **For Testing**
1. ✅ Clear limits before test runs
2. ✅ Use different test users/IPs
3. ✅ Test rate limit recovery
4. ✅ Verify error messages are user-friendly

---

## 🚨 **Emergency Procedures**

### **Complete Rate Limit Disable (Last Resort)**
```bash
# 1. Backup current configuration
cp bootstrap/app.php bootstrap/app.php.backup

# 2. Edit bootstrap/app.php and comment out:
# RateLimitMiddleware::class,

# 3. Clear all caches
php artisan optimize:clear

# 4. Restart server
php artisan serve

# 5. Remember to re-enable later!
```

### **Restore Rate Limiting**
```bash
# 1. Restore configuration
cp bootstrap/app.php.backup bootstrap/app.php

# 2. Clear caches
php artisan optimize:clear

# 3. Test with cleared limits
php artisan rate-limit:clear
```

---

## 📞 **Getting Help**

### **Common Error Messages**
```
"Too many requests. Please try again later."
→ Solution: php artisan rate-limit:clear

"Rate limit exceeded. Try again in X seconds."
→ Solution: Wait X seconds OR clear rate limits

"429 Too Many Requests"
→ Solution: Check network tab, clear rate limits
```

### **Debug Information**
```bash
# Check environment
php artisan config:show app.env

# Check rate limit status
php artisan security:monitor

# View logs
tail -f storage/logs/laravel.log
```

### **Support Channels**
- 🐛 **Bug Reports**: GitHub Issues
- 💬 **Quick Help**: Clear rate limits first
- 📧 **Email**: Include error message and steps to reproduce

---

## ✅ **Summary**

**The rate limiting system has been optimized for better user experience:**

1. **🔓 Development Mode**: Unlimited GET requests, 3x more generous limits
2. **👤 Authenticated Users**: 50% more requests allowed
3. **⚡ Faster Recovery**: Shorter decay times
4. **🛠️ Easy Management**: Simple commands to clear limits
5. **📊 Better Monitoring**: Clear error messages and headers

**Most "too many attempts" errors can be resolved with:**
```bash
php artisan rate-limit:clear && php artisan optimize:clear
```

**Happy browsing! 🎉**
