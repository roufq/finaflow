
# FinaFlow - Comprehensive Personal Finance Management System

FinaFlow adalah aplikasi web manajemen keuangan pribadi yang komprehensif dibangun dengan Laravel 11. Aplikasi ini membantu pengguna melacak pemasukan, pengeluaran, investasi, aset, hutang, dan mengelola seluruh aspek keuangan pribadi dengan mudah dan insightful.

## 🚀 Fitur Utama

### 💰 **Personal Finance Foundation**
- **Dashboard**: Ringkasan keuangan dengan visualisasi data dan proyeksi cash flow
- **Manajemen Transaksi**: Tambah, edit, hapus transaksi pemasukan dan pengeluaran
- **Kategori**: Kelola kategori untuk mengorganisir transaksi (income/expense)
- **Account Management**: Multiple account types (bank, cash, credit card, e-wallet, investment)
- **Transfer Management**: Transfer antar akun dengan history lengkap

### 🎯 **Financial Planning & Goals**
- **Goal Setting**: Financial goals setup dengan progress tracking
- **Budget Management**: Zero-based budgeting dengan templates (50/30/20 rule)
- **Debt Management**: Debt payoff calculators dengan snowball/avalance method

### 📈 **Investment & Asset Tracking**
- **Investment Portfolio**: Stock, mutual fund, crypto tracking dengan ROI calculations
- **Asset Management**: Real estate, vehicle, personal assets dengan depreciation tracking
- **Net Worth Dashboard**: Real-time net worth calculation dengan health score

### 🧠 **Behavioral Finance & Lifestyle**
- **Spending Triggers**: Identifikasi pola pengeluaran berdasarkan emosi dan situasi
- **Habit Formation**: Tracking kebiasaan menabung dan pengeluaran dengan streak counter
- **Financial Personality**: Assessment kepribadian finansial dengan rekomendasi personal
- **Gamification**: Sistem poin, badge, dan achievement untuk motivasi finansial
- **Subscription Management**: Tracking langganan dengan optimasi biaya dan reminder pembatalan
- **Reward & Loyalty**: Manajemen kartu kredit rewards dan program loyalty points

### Automation & Integration
- **Smart Automation**: Builder kondisi/aksi, manual run & toggle, serta reminder tagihan otomatis
- **Bank Integration**: CRUD koneksi bank, impor CSV/API, sinkron saldo, auto-category, dan deteksi duplikat
- **API Integrations**: Integrasi kredit skor, data investasi, berita finansial, dan cuaca dengan rate limit tracking
- **Integration Tools**: OCR struk, voice-to-text, parser email/invoice, dan quick entry lainnya

### 👨‍👩‍👧‍👦 **Family Finance**
- **Shared Expenses**: Tracking pengeluaran bersama keluarga dengan split method (equal/percentage/custom)
- **Family Goals**: Target keuangan bersama keluarga
- **Gift Events**: Planning dan tracking budget hadiah untuk acara spesial
- **Family Members**: Manajemen anggota keluarga untuk budgeting bersama

### ⚙️ **Additional Features**
- **Multi-Language Support**: Bahasa Indonesia dan English
- **Pengaturan**: Konfigurasi mata uang dan pengaturan lainnya
- **Autentikasi**: Sistem login dan register yang aman
- **Responsive Design**: Antarmuka yang responsif untuk desktop dan mobile dengan collapsible sidebar
- **User-Scoped Data**: Semua data terisolasi per pengguna

## 🛠️ Teknologi yang Digunakan

- **Framework**: Laravel 11
- **Database**: MySQL/SQLite
- **Frontend**: Bootstrap 4 (SB Admin 2 Template)
- **Authentication**: Laravel Sanctum
- **Icons**: Font Awesome

## 📋 Persyaratan Sistem

- PHP >= 8.1
- Composer
- Node.js & NPM (untuk asset compilation)
- MySQL atau SQLite

## ⚡ Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/your-username/finaflow.git
   cd finaflow
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup**
   ```bash
   # Konfigurasi database di file .env
   php artisan migrate
   php artisan db:seed
   ```

5. **Compile Assets**
   ```bash
   npm run build
   # atau untuk development
   npm run dev
   ```

6. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```

   Akses aplikasi di: `http://localhost:8000`

## 🔐 Akun Demo

Untuk testing, gunakan akun berikut:
- **Email**: `john@example.com` atau `jane@example.com`
- **Password**: `password`

## 📊 Struktur Database

### Tabel Utama:
- **users**: Data pengguna
- **settings**: Pengaturan pengguna (mata uang, bulan mulai)
- **categories**: Kategori transaksi (income/expense)
- **transactions**: Data transaksi keuangan
- **accounts**: Manajemen rekening (bank, cash, credit card, dll)
- **transfers**: Transfer antar rekening
- **goals**: Target keuangan pengguna
- **budgets**: Anggaran pengeluaran
- **debts**: Manajemen hutang
- **investments**: Portofolio investasi
- **assets**: Manajemen aset fisik
- **tags**: Tagging untuk transaksi

### Tabel Behavioral Finance:
- **spending_triggers**: Pola pemicu pengeluaran (emosi, situasi, dll)
- **habits**: Kebiasaan finansial dengan streak tracking
- **financial_personalities**: Assessment kepribadian finansial
- **gamifications**: Sistem poin, badge, dan achievement
- **subscriptions**: Tracking langganan dan biaya berulang
- **rewards**: Manajemen rewards kartu kredit
- **loyalty_programs**: Program loyalty points

### Tabel Automation & Integration:
- **automations**: Aturan otomatis untuk alert dan reminder
- **bank_integrations**: Integrasi dengan rekening bank
- **api_integrations**: Integrasi API eksternal

### Tabel Family Finance:
- **family_members**: Data anggota keluarga
- **shared_expenses**: Pengeluaran bersama dengan split method
- **family_goals**: Target keuangan keluarga
- **gift_events**: Planning budget hadiah untuk acara

### Relasi:
- User hasMany: Settings, Categories, Transactions, Accounts, Transfers, Goals, Budgets, Debts, Investments, Assets, SpendingTriggers, Habits, FinancialPersonalities, Gamifications, Subscriptions, Rewards, LoyaltyPrograms, Automations, BankIntegrations, ApiIntegrations, FamilyMembers, SharedExpenses, FamilyGoals, GiftEvents
- Category hasMany Transactions
- Account hasMany Transactions, Transfers
- Transaction belongsTo Category, Account
- Transfer belongsTo FromAccount, ToAccount
- Investment/Asset belongsTo User
- FamilyMember hasMany SharedExpenses
- SharedExpense belongsToMany FamilyMembers (pivot: share_amount, share_method)

## 🗂️ Struktur Proyek

```
finaflow/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/
│   │   ├── DashboardController.php
│   │   ├── SettingController.php
│   │   ├── CategoryController.php
│   │   ├── TransactionController.php
│   │   ├── AccountController.php
│   │   ├── TransferController.php
│   │   ├── GoalController.php
│   │   ├── BudgetController.php
│   │   ├── DebtController.php
│   │   ├── InvestmentController.php
│   │   ├── AssetController.php
│   │   ├── TagController.php
│   │   └── NetWorthController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Setting.php
│   │   ├── Category.php
│   │   ├── Transaction.php
│   │   ├── Account.php
│   │   ├── Transfer.php
│   │   ├── Goal.php
│   │   ├── Budget.php
│   │   ├── Debt.php
│   │   ├── Investment.php
│   │   ├── Asset.php
│   │   ├── Tag.php
│   │   └── Scopes/UserScope.php
│   └── Policies/
│       ├── AccountPolicy.php
│       ├── BudgetPolicy.php
│       ├── DebtPolicy.php
│       ├── GoalPolicy.php
│       ├── InvestmentPolicy.php
│       ├── AssetPolicy.php
│       └── TransferPolicy.php
├── database/
│   ├── migrations/
│   │   └── [20+ migration files]
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   ├── views/
│   │   ├── layouts/app.blade.php
│   │   ├── auth/
│   │   ├── dashboard.blade.php
│   │   ├── settings/
│   │   ├── categories/
│   │   ├── transactions/
│   │   ├── accounts/
│   │   ├── transfers/
│   │   ├── goals/
│   │   ├── budgets/
│   │   ├── debts/
│   │   ├── investments/
│   │   ├── assets/
│   │   └── net-worth/
│   └── css/
├── routes/
│   └── web.php
└── public/
    ├── css/
    ├── js/
    └── vendor/
```

## 🎯 Cara Penggunaan

1. **Login/Register**: Buat akun atau login dengan akun demo
2. **Dashboard**: Lihat ringkasan keuangan Anda
3. **Settings**: Atur preferensi mata uang dan bulan mulai
4. **Categories**: Buat kategori untuk mengorganisir transaksi
5. **Transactions**: Tambah transaksi pemasukan/pengeluaran

## 🔧 Perintah Artisan

```bash
# Migrasi database
php artisan migrate

# Seed database dengan data dummy
php artisan db:seed

# Reset dan seed ulang database
php artisan migrate:fresh --seed

# Jalankan server development
php artisan serve

# Generate key aplikasi
php artisan key:generate

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 📱 API Endpoints

Aplikasi ini menggunakan RESTful routes Laravel dengan middleware auth:

### Authentication Routes:
- `GET /` - Redirect ke dashboard atau login
- `GET/POST /login` - Autentikasi
- `GET/POST /register` - Registrasi
- `POST /logout` - Logout

### Main Application Routes:
- `GET /dashboard` - Dashboard utama dengan cash flow analysis
- `GET /settings` - Manajemen pengaturan pengguna
- `GET /categories` - Manajemen kategori transaksi
- `GET /transactions` - Manajemen transaksi keuangan
- `GET /accounts` - Manajemen rekening
- `GET /transfers` - Manajemen transfer antar rekening
- `GET /goals` - Manajemen target keuangan
- `GET /budgets` - Manajemen anggaran
- `GET /debts` - Manajemen hutang
- `GET /investments` - Manajemen portofolio investasi
- `GET /assets` - Manajemen aset fisik
- `GET /net-worth` - Dashboard net worth dengan health score
- `GET /tags` - Manajemen tag transaksi

### Behavioral Finance Routes:
- `GET /behavioral` - Dashboard behavioral finance
- `GET /behavioral/triggers` - Manajemen spending triggers
- `GET /behavioral/habits` - Tracking habit formation
- `GET /behavioral/personality` - Assessment kepribadian finansial
- `GET /behavioral/gamification` - Sistem poin dan achievement
- `GET /subscriptions` - Manajemen langganan
- `GET /rewards` - Manajemen rewards dan loyalty

### Automation & Integration Routes:
- `GET /automations` - Manajemen aturan otomatis
- `GET /bank-integrations` - Integrasi rekening bank
- `GET /api-integrations` - Integrasi API eksternal

### Family Finance Routes:
- `GET /family` - Dashboard keluarga
- `GET /family/members` - Manajemen anggota keluarga
- `GET /family/shared-expenses` - Pengeluaran bersama
- `GET /family/goals` - Target keuangan keluarga
- `GET /family/gift-events` - Planning hadiah dan acara

## 🤝 Kontribusi

1. Fork repository
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📝 Lisensi

Proyek ini menggunakan lisensi MIT. Lihat file `LICENSE` untuk detail lebih lanjut.

## 📞 Dukungan

Jika Anda mengalami masalah atau memiliki pertanyaan:

1. Periksa [Issues](https://github.com/your-username/finaflow/issues) yang sudah ada
2. Buat Issue baru jika diperlukan
3. Kontak developer untuk dukungan teknis

## 🔄 Update Log

### v3.0.0 (Current)
- **Behavioral Finance & Lifestyle**: Complete behavioral insights dengan spending triggers, habit formation, financial personality assessment, dan gamification system
- **Subscription Management**: Advanced subscription tracking dengan cost optimization dan cancellation reminders
- **Reward & Loyalty Programs**: Comprehensive rewards management untuk kartu kredit dan loyalty points
- **Automation & Integration**: Smart automation rules, bank integration framework, dan API integrations
- **Family Finance**: Shared expenses dengan split methods, family goals, dan gift event planning
- **Multi-Language Support**: Full localization untuk Bahasa Indonesia dan English
- **Enhanced UI/UX**: Collapsible sidebar dengan toggle functionality dan improved responsive design
- **Investment & Asset Tracking**: Complete portfolio management dengan ROI calculations
- **Net Worth Dashboard**: Real-time net worth calculation dengan financial health score
- **Advanced Account Management**: Multiple account types dengan transfer capabilities
- **Financial Planning**: Goals, budgets, dan debt management
- **Enhanced Dashboard**: Cash flow projections, burn rate, emergency fund tracking
- **User-Scoped Architecture**: All data properly isolated per user
- **Comprehensive CRUD**: Full create/read/update/delete untuk semua entities
- **Responsive UI**: Modern Bootstrap interface dengan charts dan visualizations

### v2.0.0
- **Investment & Asset Tracking**: Complete portfolio management dengan ROI calculations
- **Net Worth Dashboard**: Real-time net worth calculation dengan financial health score
- **Advanced Account Management**: Multiple account types dengan transfer capabilities
- **Financial Planning**: Goals, budgets, dan debt management
- **Enhanced Dashboard**: Cash flow projections, burn rate, emergency fund tracking
- **User-Scoped Architecture**: All data properly isolated per user
- **Comprehensive CRUD**: Full create/read/update/delete untuk semua entities
- **Responsive UI**: Modern Bootstrap interface dengan charts dan visualizations

### v1.0.0
- Fitur dasar manajemen keuangan
- Sistem autentikasi
- CRUD untuk Settings, Categories, Transactions
- Dashboard dengan ringkasan
- Responsive design

---

**Dibangun dengan ❤️ menggunakan Laravel Framework**
"# finaflow" 
"# finaflow" 

