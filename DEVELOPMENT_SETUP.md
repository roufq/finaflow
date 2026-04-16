# 🛠️ FINAFLOW DEVELOPMENT ENVIRONMENT SETUP GUIDE

## 📋 Overview
Comprehensive guide to setting up the FinaFlow development environment on your local machine. This guide covers dependency installation, database configuration, and overall application setup for development purposes.

## 💻 SYSTEM PREREQUISITES

### **Operating System**
- **Windows**: 10/11 Pro (recommended) or Windows Subsystem for Linux (WSL2)
- **macOS**: 12.0+ (Monterey or newer)
- **Linux**: Ubuntu 20.04+, CentOS 8+, Fedora 34+

### **Hardware Requirements**
- **RAM**: Minimum 8GB, Recommended 16GB+
- **Storage**: 20GB free space
- **CPU**: Intel i5/AMD Ryzen 5 or better

### **Software Prerequisites**
- **Git**: 2.30+
- **PHP**: 8.2+ (with specific extensions)
- **Composer**: 2.0+
- **Node.js**: 18+ LTS
- **NPM**: 8+
- **Database**: MySQL 8.0+ or PostgreSQL 15+

## 🔧 DEPENDENCY INSTALLATION

### **Windows Setup**

#### **1. Install PHP 8.2**
```bash
# Download from https://windows.php.net/download
# Select the "VS16 x64 Thread Safe" version

# Extract to C:\php
# Add it to the PATH environment variable
# C:\php

# Verify installation
php --version
```

#### **2. Install Composer**
```bash
# Download from https://getcomposer.org/download/
# Install via the setup wizard

# Verify installation
composer --version
```

#### **3. Install Node.js**
```bash
# Download from https://nodejs.org/ (LTS version)
# Install using default settings

# Verify installation
node --version
npm --version
```

#### **4. Install Git**
```bash
# Download from https://git-scm.com/download/win
# Install using default settings

# Configure Git
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
```

#### **5. Install MySQL**
```bash
# Download MySQL Installer from https://dev.mysql.com/downloads/installer/
# Select "Developer Default" setup type
# Set root password: "password" or as desired

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

## 📁 PROJECT SETUP

### **1. Clone Repository**
```bash
# Navigate to your projects directory
cd ~/Projects  # or ~/Documents/Projects

# Clone the repository
git clone https://github.com/yourusername/finaflow.git
cd finaflow

# If relying on a specific branch
git checkout develop  # or branch you wish to use
```

### **2. Install PHP Dependencies**
```bash
# Install all backend dependencies
composer install

# In case of memory limits, try substituting with:
php -d memory_limit=-1 /usr/local/bin/composer install
```

### **3. Install Node Dependencies**
```bash
# Install all frontend dependencies
npm install

# If using Yarn (optional)
# yarn install
```

### **4. Setup Environment File**
```bash
# Copy environment template
cp .env.example .env

# Edit environment file
nano .env  # or your favorite text editor
```

**Development .env Configuration:**
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

# Redis (optional - for caching and queuing)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration (for testing)
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

# Vite (for asset compilation)
VITE_APP_NAME="${APP_NAME}"
```

### **5. Generate Application Key**
```bash
php artisan key:generate
```

### **6. Setup Database**

#### **MySQL Setup**
```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE finaflow_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

#### **PostgreSQL Setup (Alternative)**
```bash
# Install PostgreSQL (if you haven't yet)
# Ubuntu/Debian:
sudo apt install -y postgresql postgresql-contrib

# macOS:
brew install postgresql
brew services start postgresql

# Create database
createdb finaflow_dev

# Or via psql
psql -c "CREATE DATABASE finaflow_dev;"
```

### **7. Run Migrations**
```bash
# Run all migrations
php artisan migrate

# If wanting a fresh install (deletes all data)
php artisan migrate:fresh

# Run seeders for dummy data
php artisan db:seed
```

### **8. Setup Storage Permissions**
```bash
# Set permissions for storage and cache endpoints
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# For Windows (within Git Bash or WSL)
# icacls storage /grant "Users":F /T
# icacls bootstrap/cache /grant "Users":F /T
```

### **9. Build Assets**
```bash
# Build assets for development
npm run dev

# Or build for production deployment testing
npm run build

# For development involving network hot-reloading
npm run dev -- --host
```

## 🚀 RUNNING THE APPLICATION

### **Development Server**

#### **Laravel Development Server**
```bash
# Start Laravel built-in development server
php artisan serve

# The server will run at http://localhost:8000
# For custom host/port binding
php artisan serve --host=0.0.0.0 --port=8000
```

#### **Vite Development Server (for hot asset reloading)**
```bash
# Open a separate terminal - run Vite dev server
npm run dev

# Or with host binding
npm run dev -- --host
```

### **Queue Worker (for background jobs)**
```bash
# Separate terminal - run queue worker
php artisan queue:work

# Or append custom timeout options
php artisan queue:work --sleep=3 --tries=3 --timeout=90
```

### **Scheduler (for scheduled tasks)**
```bash
# Separate terminal - run the scheduled cron
php artisan schedule:work
```

## 🧪 TESTING SETUP

### **1. Setup Testing Database**
```bash
# Create testing database
mysql -u root -p -e "CREATE DATABASE finaflow_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Copy environment dedicated to testing
cp .env .env.testing

# Edit .env.testing
nano .env.testing
```

**Configuration inside .env.testing:**
```env
APP_ENV=testing
DB_DATABASE=finaflow_test

# Use in-memory database for ultra fast testing execution (optional)
# DB_CONNECTION=sqlite
# DB_DATABASE=:memory:
```

### **2. Run Tests**
```bash
# Run all core tests
php artisan test

# Run and output code coverage
php artisan test --coverage

# Run a specific test file exclusively
php artisan test tests/Feature/AuthTest.php

# Verbose output for deeper inspection
php artisan test -v
```

### **3. Setup PHPStan (Static Analysis)**
```bash
# Install PHPStan
composer require --dev phpstan/phpstan

# Run analysis mechanism
./vendor/bin/phpstan analyse
```

### **4. Setup Pint (Code Formatting)**
```bash
# Install Laravel Pint Code Standardizer
composer require --dev laravel/pint

# Format code automatically 
./vendor/bin/pint

# Check for formatting deviations
./vendor/bin/pint --test
```

## 🔧 DEVELOPMENT TOOLS

### **1. Laravel Debugbar**
```bash
# Install debugbar strictly for development
composer require barryvdh/laravel-debugbar --dev

# It will inherently activate assuming the environment is set to local
```

### **2. Laravel Telescope (Advanced Debugging)**
```bash
# Install Telescope
composer require laravel/telescope
php artisan telescope:install
php artisan migrate

# Access the interface at /telescope
```

### **3. MailHog (Email Testing)**
```bash
# Install MailHog to intercept emails safely 
# Download release from https://github.com/mailhog/MailHog/releases

# Execute MailHog application
./MailHog

# Reconfigure your .env file
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
```

### **4. Database Management**

#### **TablePlus / DBeaver**
- Download freely from https://tableplus.com/ or https://dbeaver.io/
- Authenticate the connection via matched credentials from `.env`.

#### **Laravel Tinker**
```bash
# Initiate interactive shell interface
php artisan tinker

# Example workflow usage:
User::all()
Transaction::where('type', 'income')->sum('amount')
exit
```

## 📊 MONITORING & LOGGING

### **1. Application Logs**
```bash
# View Laravel real-time logs
tail -f storage/logs/laravel.log

# Truncate and clear old logs
php artisan log:clear
```

### **2. Queue Monitoring**
```bash
# Check queue statuses
php artisan queue:status

# Output list of failed jobs
php artisan queue:failed

# Ask the system to retry failed jobs
php artisan queue:retry all
```

### **3. Cache Management**
```bash
# Clear heavily loaded data caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Retain cached variants mimicking production performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔄 WORKFLOW DEVELOPMENT

### **1. Daily Development Workflow**
```bash
# Start your day by pulling origin data
git pull origin develop
composer install
npm install
npm run dev

# Terminal 1: Application Server 
php artisan serve

# Terminal 2: Service Queue Worker
php artisan queue:work

# Terminal 3: Vite Dev Front-end Server
npm run dev
```

### **2. Before Committing Code Base**
```bash
# Verify integrity via testing 
php artisan test

# Linting and aesthetic checks
./vendor/bin/pint --test

# Syntax vulnerability checking
./vendor/bin/phpstan analyse

# Destroy cache fragments
php artisan cache:clear
php artisan config:clear
```

### **3. Git Workflow**
```bash
# Define a fresh feature implementation branch
git checkout -b feature/new-feature

# Make changes internally
# ...

# Commit final variations
git add .
git commit -m "Add new feature"

# Final push upstream
git push origin feature/new-feature

# Create an authoritative pull request afterward
```

## 🚨 TROUBLESHOOTING

### **Common Issues & Solutions**

#### **1. Permission Issues**
```bash
# Repair storage directory permissions
chmod -R 775 storage bootstrap/cache

# For Windows Environments specifically
icacls storage /grant "Users":F /T
icacls bootstrap/cache /grant "Users":F /T
```

#### **2. Database Connection Issues**
```bash
# Assess the database binding 
php artisan tinker
DB::connection()->getPdo();
exit

# Double check the MySQL service is spinning actively
sudo systemctl status mysql  # Linux
brew services list | grep mysql  # macOS
```

#### **3. Node.js Issues**
```bash
# Empty your node bundle cache explicitly
rm -rf node_modules package-lock.json
npm install

# Check global assignments 
node --version
npm --version
```

#### **4. Composer Issues**
```bash
# Flush composer cache repository 
composer clear-cache

# Self-update the internal modules
composer self-update

# Reinstall base dependencies
rm -rf vendor composer.lock
composer install
```

#### **5. Port Already in Use**
```bash
# Dig process assignments over port 8000
lsof -i :8000  # Linux/macOS
netstat -ano | findstr :8000  # Windows

# Subjugate and kill conflicting items 
kill -9 <PID>
```

#### **6. Vite Hot Reload Not Working**
```bash
# Flush hidden Vite cache folder
rm -rf node_modules/.vite

# Overload the restart method aggressively 
npm run dev -- --force
```

## 📚 USEFUL COMMANDS

### **Laravel Commands**
```bash
# Produce a new Eloquent model 
php artisan make:model Transaction

# Render a fresh structural migration script
php artisan make:migration create_transactions_table

# Spawn a dedicated functional controller
php artisan make:controller TransactionController

# Generate database seeder class format
php artisan make:seeder TransactionSeeder

# Extract structured arrays of API endpoint routes
php artisan route:list

# Purge any overarching caching mechanisms entirely
php artisan optimize:clear
```

### **Database Commands**
```bash
# Completely erase and recreate seeded content
php artisan migrate:fresh --seed

# Execute new database layout migrations progressively 
php artisan make:migration add_status_to_transactions_table

# Commit pending architectural changes to DB 
php artisan migrate

# Step backwards chronologically into earlier migration blocks
php artisan migrate:rollback
```

### **Asset Commands**
```bash
# Build raw elements mapping into functional endpoints (Dev)
npm run dev

# Construct completely unified and minified assets package (Prod)
npm run build

# Leave internal observer tracking code anomalies
npm run watch
```

## 🔒 SECURITY CONSIDERATIONS

### **Development Security**
- Stop and restrict `.env` pushes onto an external Git repository map. 
- Rely heavily on robust root and database user passwords.
- Always activate a Secondary Token standard (2FA) inside a developer’s GitHub account structure natively.
- Maintain and consistently update sub-modular dependencies frequently. 

### **Environment Variables**
```bash
# Review internally via deep text sweeps
grep -r "password\|secret\|key" .env

# Hardcode exceptions prohibiting public transparency limits
echo ".env" >> .gitignore
echo "storage/logs/*.log" >> .gitignore
```

## 📈 PERFORMANCE OPTIMIZATION

### **Development Performance Tips**
```bash
# Assert the presence of optimized PHP compilation blocks 
php -m | grep opcache

# Setup ultra-fast internal system cache queues  
# brew install redis (macOS)
# sudo apt install redis-server (Linux)

# Mirror configurations inward to .env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### **Database Optimization**
```bash
# Monitor the internal payload times strictly
# Modify globally via .env bounds
DB_LOG_QUERIES=true

# Evaluate lethargic internal API endpoints continuously  
php artisan db:monitor
```

## 🎯 DEVELOPMENT CHECKLIST

### **Initial Setup**
- [ ] PHP 8.2+ installed securely
- [ ] Composer natively distributed 
- [ ] Node.js 18+ correctly linked
- [ ] Git comprehensively aligned 
- [ ] Initial Database Server properly operational 
- [ ] Native GitHub Repo mapping cloned tightly  

### **Application Setup**
- [ ] All external dependencies injected (composer, npm)
- [ ] File Environment structure verified statically (.env)
- [ ] App Core logic keys generated efficiently
- [ ] Sub-level databases accurately constructed and mapped seamlessly 
- [ ] Read and Write filesystem rules permitted 
- [ ] Base frontend framework constructed logically

### **Testing Setup**
- [ ] Local testing structures built with isolated variables
- [ ] Comprehensive test sweeps consistently completed fully green 
- [ ] Clean and standardized formatting integrated tightly
- [ ] Native syntax analyzer errors wiped effectively clean

### **Development Tools**
- [ ] Debug utilities loaded securely
- [ ] Standardized client-app interactions synced natively
- [ ] Optional mailer endpoints validated fully
- [ ] Final version control paradigms defined openly 

---

## 🚀 QUICK START SCRIPT

**Windows (PowerShell):**
```powershell
# Run explicitly as Administrator internally 
Set-ExecutionPolicy RemoteSigned -Scope CurrentUser
.\setup-dev.ps1
```

**macOS/Linux:**
```bash
# Apply executable rights explicitly
chmod +x setup-dev.sh

# Complete auto-setup seamlessly 
./setup-dev.sh
```

**Manual Quick Start Validation Pipeline:**
```bash
# Raw clone setup
git clone <repo> && cd finaflow
composer install && npm install
cp .env.example .env && php artisan key:generate
# Validate the initial DB connection natively through .env 
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

*This particular setup repository was strictly fabricated mapping logical guidelines intending absolute ease-of-use enabling newer developers the fundamental keys navigating FinaFlow quickly. It’s deeply suggested validating components modularly as instructed above ensuring zero-day implementation efficiency.*
