# 🔒 Security Troubleshooting Guide

## Content Security Policy (CSP) Issues

### ❌ Common CSP Errors

#### 1. Font Loading Error
```
Refused to load the stylesheet 'https://fonts.bunny.net/css?family=instrument-sans:400,500,600' 
because it violates the following Content Security Policy directive: "style-src 'self' 'unsafe-inline' 
https://fonts.googleapis.com https://cdn.jsdelivr.net"
```

#### 2. Script Loading Error
```
Refused to load the script '<URL>' because it violates the following Content Security Policy directive: 
"script-src 'self' 'unsafe-inline' 'unsafe-eval' <URL> <URL> <URL> <URL>"
```

### ✅ Solutions

#### **Development Environment (Automatic)**
The `SecurityHeadersMiddleware` automatically detects `local` environment and applies permissive CSP:

```php
// For APP_ENV=local
"default-src *",
"script-src * 'unsafe-inline' 'unsafe-eval'",
"style-src * 'unsafe-inline'",
"font-src * data:",
"img-src * data: blob:",
"connect-src *",
"frame-src *"
```

#### **Production Environment (Strict)**
For production, strict CSP is applied with specific allowed domains:

```php
// For APP_ENV=production
"style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net https://cdn.jsdelivr.net",
"font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net https://cdn.jsdelivr.net",
"script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://api.tokopay.id"
```

### 🔧 Manual Configuration

#### **1. Check Environment**
```bash
php artisan config:show app.env
```

#### **2. Clear Caches**
```bash
php artisan optimize:clear
```

#### **3. Test CSP Headers**
```bash
php artisan test:csp http://localhost:8000
```

#### **4. Add New Domains (Production)**
Edit `app/Http/Middleware/SecurityHeadersMiddleware.php`:

```php
private function setProductionCSP(Response $response): void
{
    $csp = [
        // Add your domain to appropriate directive
        "style-src 'self' 'unsafe-inline' https://your-new-domain.com",
        "font-src 'self' https://your-new-domain.com",
        // ... other directives
    ];
}
```

### 🛠️ Debugging CSP Issues

#### **1. Browser Console**
- Open Developer Tools (F12)
- Check Console tab for CSP violations
- Note the blocked resource and directive

#### **2. CSP Report Mode**
Temporarily use report-only mode for testing:

```php
// In SecurityHeadersMiddleware.php
$response->headers->set('Content-Security-Policy-Report-Only', implode('; ', $csp));
```

#### **3. Disable CSP Temporarily**
For urgent debugging, comment out CSP in development:

```php
// In setDevelopmentCSP method
// $response->headers->set('Content-Security-Policy', implode('; ', $csp));
```

## 🔐 Other Security Issues

### **Rate Limiting Errors**
```
Too Many Attempts. Please try again in X seconds.
```

**Solution:**
```bash
# Clear rate limit cache
php artisan cache:clear

# Or reset specific rate limits
php artisan tinker
>>> RateLimiter::clear('login:user@example.com|127.0.0.1')
```

### **IP Blocking Issues**
```
Access denied. Your IP address has been blocked due to suspicious activity.
```

**Solution:**
```bash
# Check blocked IPs
php artisan security:monitor

# Clear IP blocks (development only)
php artisan cache:forget "temp_block:127.0.0.1"
php artisan cache:forget "perm_block:127.0.0.1"
```

### **File Upload Security Errors**
```
File type not allowed or potentially dangerous file detected.
```

**Solution:**
1. Check allowed file types in `SecureFileUploadMiddleware`
2. Ensure file size is within limits (10MB default)
3. Verify file is not corrupted

### **Strong Password Validation**
```
Password does not meet security requirements.
```

**Requirements:**
- Minimum 8 characters, maximum 128
- At least 1 uppercase letter
- At least 1 lowercase letter  
- At least 1 number
- At least 1 special character
- No common passwords (password123, qwerty, etc.)
- No sequential characters (123, abc)
- No keyboard patterns (qwerty, asdf)
- Maximum 3 consecutive identical characters

## 🚨 Emergency Procedures

### **Disable All Security (Emergency Only)**
```bash
# 1. Disable security middleware temporarily
# Comment out in bootstrap/app.php:
# IpBlockingMiddleware::class,
# SecurityHeadersMiddleware::class,
# RateLimitMiddleware::class,

# 2. Clear all caches
php artisan optimize:clear

# 3. Restart server
php artisan serve
```

### **Reset Security Configuration**
```bash
# 1. Clear all security-related cache
php artisan cache:forget "blocked_ips_count"
php artisan cache:forget "suspicious_requests_count"
php artisan cache:forget "failed_logins_24h"

# 2. Reset rate limits
php artisan cache:flush

# 3. Restart application
php artisan config:clear
php artisan route:clear
```

## 📞 Getting Help

### **Security Audit**
```bash
php artisan security:audit
```

### **Security Monitoring**
```bash
php artisan security:monitor --alert
```

### **Log Files**
Check these log files for security events:
- `storage/logs/laravel.log` - General application logs
- `storage/logs/security.log` - Security-specific events
- Browser console - CSP violations

### **Support Channels**
- GitHub Issues: Report security bugs
- Documentation: Check README.md security section
- Email: security@donation-platform.com

---

**⚠️ Important:** Never disable security features in production. Always test security changes in development environment first.
