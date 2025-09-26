# Donation Platform

Platform penggalangan dana modern yang dibangun dengan Laravel, Inertia.js + React, dan Filament Admin Panel. Platform ini memungkinkan pengguna untuk membuat kampanye donasi, menerima donasi, dan mengelola transaksi dengan integrasi payment gateway Tokopay.

## 🚀 Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Inertia.js + React + TypeScript
- **Admin Panel**: Filament 3
- **Database**: PostgreSQL (Supabase compatible)
- **Payment Gateway**: Tokopay
- **Styling**: Tailwind CSS

## 👥 User Roles

1. **Donor** - Dapat berdonasi ke kampanye (guest atau registered user)
2. **Campaign Creator** - Dapat membuat dan mengelola kampanye donasi
3. **Admin** - Mengelola seluruh platform melalui Filament admin panel

## ✨ Fitur Utama

### Public Features
- ✅ Daftar kampanye donasi dengan filter dan pencarian
- ✅ Detail kampanye dengan progress bar dan informasi lengkap
- ✅ Form donasi dengan multiple payment methods
- ✅ Sistem donasi anonim
- ✅ Real-time update status pembayaran via webhook

### Campaign Management
- ✅ CRUD kampanye donasi
- ✅ Upload gambar kampanye
- ✅ Kategori kampanye
- ✅ Target dan deadline kampanye
- ✅ Campaign updates dan komentar

### Payment Integration
- ✅ Integrasi Tokopay payment gateway
- ✅ Multiple payment channels (VA, E-Wallet, Retail, QRIS, Credit Card)
- ✅ Automatic payment status updates
- ✅ Transaction tracking

### Admin Panel (Filament)
- ✅ User management
- ✅ Campaign management
- ✅ Donation tracking
- ✅ Transaction monitoring
- ✅ Analytics dashboard

## 🛠 Installation & Setup

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- PostgreSQL
- Git

### 1. Clone Repository
```bash
git clone <repository-url>
cd donation
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup
Edit `.env` file dengan konfigurasi database PostgreSQL:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=donation_platform
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### 5. Tokopay Configuration
Daftar di [Tokopay](https://tokopay.id) dan dapatkan merchant credentials:
```env
TOKOPAY_MERCHANT_ID=your_merchant_id
TOKOPAY_SECRET_KEY=your_secret_key
```

### 6. Run Migrations & Seeders
```bash
# Run database migrations
php artisan migrate

# Seed initial data
php artisan db:seed
```

### 7. Build Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 8. Start Development Server
```bash
php artisan serve
```

## 🔑 Default Login Credentials

Setelah menjalankan seeder, gunakan kredensial berikut:

### Admin Panel (`/admin`)
- **Email**: admin@donation.com
- **Password**: password

### Campaign Creator
- **Email**: creator@donation.com
- **Password**: password

### Donor
- **Email**: donor@donation.com
- **Password**: password

## 📁 Project Structure

```
app/
├── Filament/Resources/     # Filament admin resources
├── Http/Controllers/       # Laravel controllers
├── Models/                # Eloquent models
└── Services/              # Business logic services

database/
├── migrations/            # Database migrations
└── seeders/              # Database seeders

resources/
├── js/
│   ├── Pages/            # Inertia.js pages (React components)
│   ├── Components/       # Reusable React components
│   └── types/           # TypeScript type definitions
└── views/               # Blade templates

routes/
├── web.php              # Web routes
└── auth.php             # Authentication routes
```

## 🔄 Payment Flow

1. **Donor** mengisi form donasi dengan nominal dan data pribadi
2. **System** membuat record donation dan transaction
3. **Tokopay** menerima request pembayaran dan mengembalikan payment URL/instructions
4. **Donor** melakukan pembayaran melalui channel yang dipilih
5. **Tokopay** mengirim webhook notification ke aplikasi
6. **System** memperbarui status transaksi dan collected_amount kampanye

## 🔗 API Endpoints

### Public Routes
- `GET /` - Homepage
- `GET /campaigns` - List kampanye
- `GET /campaigns/{slug}` - Detail kampanye
- `GET /campaigns/{slug}/donate` - Form donasi
- `POST /campaigns/{slug}/donate` - Submit donasi

### Authenticated Routes
- `GET /dashboard` - User dashboard
- `GET /campaign/create` - Form buat kampanye
- `POST /campaigns` - Submit kampanye baru

### Webhook
- `POST /webhook/tokopay` - Tokopay payment notification

## 🎨 Customization

### Adding New Payment Channels
1. Update `PaymentProviderSeeder.php`
2. Add channel codes in `TokopayService.php`
3. Update payment form UI in `Donations/Create.tsx`

### Adding New Campaign Categories
1. Update `CategorySeeder.php` atau tambah via admin panel
2. Categories otomatis tersedia di form kampanye

### Styling Customization
- Edit `resources/css/app.css` untuk custom styles
- Modify Tailwind classes di React components
- Update `tailwind.config.js` untuk custom theme

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` dan `APP_DEBUG=false`
- [ ] Configure proper database credentials
- [ ] Set up SSL certificate
- [ ] Configure email service (SMTP)
- [ ] Set up queue worker untuk background jobs
- [ ] Configure file storage (AWS S3 recommended)
- [ ] Set up monitoring dan logging

### Queue Configuration
```bash
# Start queue worker
php artisan queue:work

# Or use supervisor for production
```

## 🔒 Security Features

- CSRF protection pada semua forms
- SQL injection protection via Eloquent ORM
- XSS protection via Laravel's built-in escaping
- Webhook signature verification
- File upload validation
- Rate limiting pada API endpoints

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📝 License

This project is licensed under the MIT License.

## 🆘 Support

Untuk bantuan dan pertanyaan:
- Create issue di GitHub repository
- Email: support@donation.com

## 🔄 Roadmap

### Phase 2 Features
- [ ] Recurring donations
- [ ] Withdrawal system untuk campaign creators
- [ ] Email notifications
- [ ] Social media sharing
- [ ] Campaign analytics dashboard
- [ ] Mobile app (React Native)
- [ ] Multi-language support
- [ ] Advanced reporting

---

**Happy Coding! 🎉**
