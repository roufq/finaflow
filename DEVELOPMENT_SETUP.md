# 🛠️ PANDUAN SETUP ENVIRONMENT DEVELOPMENT FINA FLOW

## 📋 Overview
Panduan lengkap untuk setup environment development FinaFlow di local machine. Panduan ini mencakup instalasi dependencies, konfigurasi database, dan setup aplikasi untuk development.

## 💻 PRASYARAT SYSTEM

### **Operating System**
- **Windows**: 10/11 Pro (recommended) atau Windows Subsystem for Linux (WSL2)
- **macOS**: 12.0+ (Monterey atau lebih baru)
- **Linux**: Ubuntu 20.04+, CentOS 8+, Fedora 34+

### **Hardware Requirements**
- **RAM**: Minimum 8GB, Recommended 16GB+
- **Storage**: 20GB free space
- **CPU**: Intel i5/AMD Ryzen 5 atau lebih baik

### **Software Prerequisites**
- **Git**: 2.30+
- **PHP**: 8.2+ (dengan extensions tertentu)
- **Composer**: 2.0+
- **Node.js**: 18+ LTS
- **NPM**: 8+
- **Database**: MySQL 8.0+ atau PostgreSQL 15+

## 🔧 INSTALASI DEPENDENCIES

### **Windows Setup**

#### **1. Install PHP 8.2**
```bash
# Download dari https://windows.php.net/download
# Pilih "VS16 x64 Thread Safe" version

# Extract ke C:\php
# Add ke PATH environment variable
# C:\php

# Verify installation
php --version
```

#### **2. Install Composer**
```bash
# Download dari https://getcomposer.org/download/
# Install dengan setup wizard

# Verify installation
composer --version
```

#### **3. Install Node.js**
```bash
# Download dari https://nodejs.org/ (LTS version)
# Install dengan default settings

# Verify installation
node --version
npm --version
```

#### **4. Install Git**
```bash
# Download dari https://git-scm.com/download/win
# Install dengan default settings

# Configure Git
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
```

#### **5. Install MySQL**
```bash
# Download MySQL Installer dari https://dev.mysql.com/downloads/installer/
# Pilih "Developer Default" setup type
# Set root password: "password" atau sesuai keinginan

# Verify installation
mysql --version
```

### **macOS Setup**

#### **1. Install Homebrew**
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Add to PATH
echo 'eval "$(/opt/homebrew/bin/brew shellenv)"' >> ~/.zprofile
eval "$(/opt/homebrew/bin/brew shellenv)"
```

#### **2. Install PHP 8.2**
```bash
brew install php@8.2
brew link php@8.2 --force

# Verify installation
php --version
```

#### **3. Install Composer**
```bash
brew install composer

# Verify installation
composer --version
```

#### **4. Install Node.js**
```bash
brew install node

# Verify installation
node --version
npm --version
```

#### **5. Install MySQL**
```bash
brew install mysql
brew services start mysql

# Secure installation
mysql_secure_installation

# Verify installation
mysql --version
```

### **Linux (Ubuntu/Debian) Setup**

#### **1. Update System**
```bash
sudo apt update && sudo apt upgrade -y
```

#### **2. Install PHP 8.2**
```bash
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-pgsql \
php8.2-sqlite3 php8.2-redis php8.2-xml php8.2-curl php8.2-gd php8.2-mbstring \
php8.2-zip php8.2-bcmath php8.2-intl php8.2-tokenizer php8.2-fileinfo php8.2-exif

# Verify installation
php --version
```

#### **3. Install Composer**
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Verify installation
composer --version
```

#### **4. Install Node.js**
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Verify installation
node --version
npm --version
```

#### **5. Install MySQL**
```bash
sudo apt install -y mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql

# Secure installation
sudo mysql_secure_installation

# Verify installation
mysql --version
```

## 📁 SETUP PROJECT

### **1. Clone Repository**
```bash
# Navigate to your projects directory
cd ~/Projects  # atau ~/Documents/Projects

# Clone the repository
git clone https://github.com/yourusername/finaflow.git
cd finaflow

# Jika menggunakan branch tertentu
git checkout develop  # atau branch yang diinginkan
```

### **2. Install PHP Dependencies**
```bash
# Install semua dependencies
composer install

# Jika ada masalah memory, gunakan:
php -d memory_limit=-1 /usr/local/bin/composer install
```

### **3. Install Node Dependencies**
```bash
# Install semua dependencies
npm install

# Jika menggunakan Yarn (optional)
# yarn install
```

### **4. Setup Environment File**
```bash
# Copy environment template
cp .env.example .env

# Edit environment file
nano .env  # atau gunakan editor favorit Anda
```

**Konfigurasi .env untuk development:**
```env
APP_NAME="FinaFlow Dev"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=finaflow_dev
DB_USERNAME=root
DB_PASSWORD=password

# Redis (optional - untuk caching)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration (untuk testing)
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Queue Configuration
QUEUE_CONNECTION=database

# Cache Configuration
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120

# File Storage
FILESYSTEM_DISK=local

# Vite (untuk asset compilation)
VITE_APP_NAME="${APP_NAME}"
```

### **5. Generate Application Key**
```bash
php artisan key:generate
```

### **6. Setup Database**

#### **MySQL Setup**
```bash
# Login ke MySQL
mysql -u root -p

# Create database
CREATE DATABASE finaflow_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

#### **PostgreSQL Setup (Alternative)**
```bash
# Install PostgreSQL (jika belum ada)
# Ubuntu/Debian:
sudo apt install -y postgresql postgresql-contrib

# macOS:
brew install postgresql
brew services start postgresql

# Create database
createdb finaflow_dev

# Atau via psql
psql -c "CREATE DATABASE finaflow_dev;"
```

### **7. Run Migrations**
```bash
# Run semua migrations
php artisan migrate

# Jika ingin fresh install (hapus semua data)
php artisan migrate:fresh

# Run seeders untuk dummy data
php artisan db:seed
```

### **8. Setup Storage Permissions**
```bash
# Set permissions untuk storage dan cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Untuk Windows (dalam Git Bash atau WSL)
# icacls storage /grant "Users":F /T
# icacls bootstrap/cache /grant "Users":F /T
```

### **9. Build Assets**
```bash
# Build assets untuk development
npm run dev

# Atau build untuk production
npm run build

# Untuk development dengan hot reload
npm run dev -- --host
```

## 🚀 MENJALANKAN APLIKASI

### **Development Server**

#### **Laravel Development Server**
```bash
# Jalankan Laravel development server
php artisan serve

# Server akan berjalan di http://localhost:8000
# Untuk custom host/port
php artisan serve --host=0.0.0.0 --port=8000
```

#### **Vite Development Server (untuk asset hot reload)**
```bash
# Terminal terpisah - jalankan Vite dev server
npm run dev

# Atau dengan host binding
npm run dev -- --host
```

### **Queue Worker (untuk background jobs)**
```bash
# Terminal terpisah - jalankan queue worker
php artisan queue:work

# Atau dengan custom options
php artisan queue:work --sleep=3 --tries=3 --timeout=90
```

### **Scheduler (untuk scheduled tasks)**
```bash
# Terminal terpisah - jalankan scheduler
php artisan schedule:work
```

## 🧪 TESTING SETUP

### **1. Setup Testing Database**
```bash
# Create testing database
mysql -u root -p -e "CREATE DATABASE finaflow_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Copy environment untuk testing
cp .env .env.testing

# Edit .env.testing
nano .env.testing
```

**Konfigurasi .env.testing:**
```env
APP_ENV=testing
DB_DATABASE=finaflow_test

# Use in-memory database untuk testing cepat (optional)
# DB_CONNECTION=sqlite
# DB_DATABASE=:memory:
```

### **2. Run Tests**
```bash
# Run semua tests
php artisan test

# Run dengan coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/AuthTest.php

# Run dengan verbose output
php artisan test -v
```

### **3. Setup PHPStan (Static Analysis)**
```bash
# Install PHPStan
composer require --dev phpstan/phpstan

# Run analysis
./vendor/bin/phpstan analyse
```

### **4. Setup Pint (Code Formatting)**
```bash
# Install Laravel Pint
composer require --dev laravel/pint

# Format code
./vendor/bin/pint

# Check formatting
./vendor/bin/pint --test
```

## 🔧 DEVELOPMENT TOOLS

### **1. Laravel Debugbar**
```bash
# Install debugbar untuk development
composer require barryvdh/laravel-debugbar --dev

# Akan otomatis aktif di environment local
```

### **2. Laravel Telescope (Advanced Debugging)**
```bash
# Install Telescope
composer require laravel/telescope
php artisan telescope:install
php artisan migrate

# Akses di /telescope
```

### **3. MailHog (Email Testing)**
```bash
# Install MailHog untuk testing email
# Download dari https://github.com/mailhog/MailHog/releases

# Jalankan MailHog
./MailHog

# Configure di .env
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
```

### **4. Database Management**

#### **TablePlus / DBeaver**
- Download dari https://tableplus.com/ atau https://dbeaver.io/
- Connect dengan credentials dari .env

#### **Laravel Tinker**
```bash
# Interactive shell untuk testing code
php artisan tinker

# Contoh penggunaan:
User::all()
Transaction::where('type', 'income')->sum('amount')
exit
```

## 📊 MONITORING & LOGGING

### **1. Application Logs**
```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# Clear logs
php artisan log:clear
```

### **2. Queue Monitoring**
```bash
# Check queue status
php artisan queue:status

# List failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### **3. Cache Management**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache untuk production-like performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔄 WORKFLOW DEVELOPMENT

### **1. Daily Development Workflow**
```bash
# Start development
git pull origin develop
composer install
npm install
npm run dev

# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Queue worker
php artisan queue:work

# Terminal 3: Vite dev server
npm run dev
```

### **2. Before Committing**
```bash
# Run tests
php artisan test

# Check code style
./vendor/bin/pint --test

# Static analysis
./vendor/bin/phpstan analyse

# Clear caches
php artisan cache:clear
php artisan config:clear
```

### **3. Git Workflow**
```bash
# Create feature branch
git checkout -b feature/new-feature

# Make changes
# ...

# Commit changes
git add .
git commit -m "Add new feature"

# Push to remote
git push origin feature/new-feature

# Create pull request
```

## 🚨 TROUBLESHOOTING

### **Common Issues & Solutions**

#### **1. Permission Issues**
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache

# Untuk Windows
icacls storage /grant "Users":F /T
icacls bootstrap/cache /grant "Users":F /T
```

#### **2. Database Connection Issues**
```bash
# Test database connection
php artisan tinker
DB::connection()->getPdo();
exit

# Check MySQL service
sudo systemctl status mysql  # Linux
brew services list | grep mysql  # macOS
```

#### **3. Node.js Issues**
```bash
# Clear node modules dan reinstall
rm -rf node_modules package-lock.json
npm install

# Check Node version
node --version
npm --version
```

#### **4. Composer Issues**
```bash
# Clear composer cache
composer clear-cache

# Update composer
composer self-update

# Reinstall dependencies
rm -rf vendor composer.lock
composer install
```

#### **5. Port Already in Use**
```bash
# Find process using port 8000
lsof -i :8000  # Linux/macOS
netstat -ano | findstr :8000  # Windows

# Kill process
kill -9 <PID>
```

#### **6. Vite Hot Reload Not Working**
```bash
# Clear Vite cache
rm -rf node_modules/.vite

# Restart dev server
npm run dev -- --force
```

## 📚 USEFUL COMMANDS

### **Laravel Commands**
```bash
# Create new model
php artisan make:model Transaction

# Create migration
php artisan make:migration create_transactions_table

# Create controller
php artisan make:controller TransactionController

# Create seeder
php artisan make:seeder TransactionSeeder

# List all routes
php artisan route:list

# Clear all Laravel caches
php artisan optimize:clear
```

### **Database Commands**
```bash
# Reset database
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration add_status_to_transactions_table

# Run pending migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback
```

### **Asset Commands**
```bash
# Build untuk development
npm run dev

# Build untuk production
npm run build

# Watch untuk changes
npm run watch
```

## 🔒 SECURITY CONSIDERATIONS

### **Development Security**
- Jangan commit `.env` file ke Git
- Gunakan password yang kuat untuk database
- Enable 2FA untuk GitHub account
- Regular update dependencies

### **Environment Variables**
```bash
# Check untuk sensitive data
grep -r "password\|secret\|key" .env

# Jangan commit sensitive files
echo ".env" >> .gitignore
echo "storage/logs/*.log" >> .gitignore
```

## 📈 PERFORMANCE OPTIMIZATION

### **Development Performance Tips**
```bash
# Enable OPcache untuk PHP
php -m | grep opcache

# Use local Redis untuk caching
# brew install redis (macOS)
# sudo apt install redis-server (Linux)

# Configure di .env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### **Database Optimization**
```bash
# Enable query logging untuk debugging
# Di .env
DB_LOG_QUERIES=true

# Monitor slow queries
php artisan db:monitor
```

## 🎯 DEVELOPMENT CHECKLIST

### **Initial Setup**
- [ ] PHP 8.2+ installed
- [ ] Composer installed
- [ ] Node.js 18+ installed
- [ ] Git configured
- [ ] Database server running
- [ ] Project cloned

### **Application Setup**
- [ ] Dependencies installed (composer, npm)
- [ ] Environment configured (.env)
- [ ] Application key generated
- [ ] Database created & migrated
- [ ] Storage permissions set
- [ ] Assets built

### **Testing Setup**
- [ ] Testing database configured
- [ ] Tests passing
- [ ] Code style checked
- [ ] Static analysis clean

### **Development Tools**
- [ ] Debugbar installed (optional)
- [ ] Database client configured
- [ ] Mail testing setup (optional)
- [ ] Version control workflow ready

---

## 🚀 QUICK START SCRIPT

**Windows (PowerShell):**
```powershell
# Run as Administrator
Set-ExecutionPolicy RemoteSigned -Scope CurrentUser
.\setup-dev.ps1
```

**macOS/Linux:**
```bash
# Make executable
chmod +x setup-dev.sh

# Run setup
./setup-dev.sh
```

**Manual Quick Start:**
```bash
# Clone & setup
git clone <repo> && cd finaflow
composer install && npm install
cp .env.example .env && php artisan key:generate
# Configure database di .env
php artisan migrate && php artisan db:seed
npm run dev &
php artisan serve &
php artisan queue:work &
```

---

## 📞 SUPPORT & RESOURCES

### **Learning Resources**
- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Guide](https://vuejs.org/guide/introduction.html)
- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [MySQL Documentation](https://dev.mysql.com/doc/)

### **Development Communities**
- [Laravel Indonesia Discord](https://discord.gg/laravel-id)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/laravel)
- [Laracasts](https://laracasts.com/)

### **Tools & Extensions**
- **VS Code Extensions**: PHP Intelephense, Laravel Extension Pack
- **Browser Extensions**: Vue DevTools, React DevTools
- **Database Tools**: TablePlus, DBeaver, phpMyAdmin

---

*Panduan setup development ini dirancang untuk memudahkan developer baru memulai dengan FinaFlow. Pastikan mengikuti semua langkah dengan urut dan test setiap komponen sebelum melanjutkan ke development.*
