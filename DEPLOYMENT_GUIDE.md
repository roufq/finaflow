# 🚀 PANDUAN DEPLOYMENT FINA FLOW

## 📋 Overview
Panduan lengkap untuk deploy aplikasi FinaFlow dari development hingga production. Panduan ini mencakup setup server, konfigurasi environment, database, dan automation deployment.

## 🏗️ ARSITEKTUR DEPLOYMENT

### **Environment Setup**
```
Development → Staging → Production
     ↓           ↓           ↓
Local Machine → VPS Server → Production Server
```

### **Tech Stack**
- **Framework**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL 8.0+ / PostgreSQL 15+
- **Web Server**: Nginx 1.20+
- **Process Manager**: Supervisor
- **Cache**: Redis 6.0+
- **SSL**: Let's Encrypt
- **Deployment**: Git-based dengan automation

## 📋 PRASYARAT SERVER

### **Minimum Requirements**
- **CPU**: 2 cores
- **RAM**: 4GB
- **Storage**: 20GB SSD
- **OS**: Ubuntu 20.04 LTS / 22.04 LTS
- **Network**: 100Mbps bandwidth

### **Recommended Production**
- **CPU**: 4+ cores
- **RAM**: 8GB+
- **Storage**: 50GB+ SSD
- **Load Balancer**: Nginx/HAProxy (untuk multiple servers)

## 🔧 SETUP SERVER PRODUCTION

### **1. Update System**
```bash
sudo apt update && sudo apt upgrade -y
sudo apt autoremove -y
```

### **2. Install Essential Packages**
```bash
sudo apt install -y curl wget git unzip software-properties-common
```

### **3. Install PHP 8.2+**
```bash
# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP dan extensions
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-pgsql \
php8.2-sqlite3 php8.2-redis php8.2-memcached php8.2-xml php8.2-curl \
php8.2-gd php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-intl \
php8.2-tokenizer php8.2-fileinfo php8.2-exif
```

### **4. Install Composer**
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### **5. Install Node.js & NPM (untuk asset compilation)**
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### **6. Install MySQL 8.0**
```bash
sudo apt install -y mysql-server-8.0
sudo systemctl start mysql
sudo systemctl enable mysql

# Secure MySQL installation
sudo mysql_secure_installation
```

### **7. Install Redis**
```bash
sudo apt install -y redis-server
sudo systemctl start redis-server
sudo systemctl enable redis-server
```

### **8. Install Nginx**
```bash
sudo apt install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

### **9. Install SSL Certificate (Let's Encrypt)**
```bash
sudo apt install -y certbot python3-certbot-nginx
```

### **10. Install Supervisor (untuk queue workers)**
```bash
sudo apt install -y supervisor
sudo systemctl start supervisor
sudo systemctl enable supervisor
```

## 🔐 KONFIGURASI SECURITY

### **1. Setup Firewall**
```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable
```

### **2. Create Deployment User**
```bash
sudo adduser deploy
sudo usermod -aG sudo deploy
sudo mkdir -p /home/deploy/.ssh
sudo chown -R deploy:deploy /home/deploy/.ssh
```

### **3. Setup SSH Key Authentication**
```bash
# On local machine
ssh-keygen -t rsa -b 4096 -C "deploy@finaflow.com"
ssh-copy-id deploy@your-server-ip

# On server - disable password authentication
sudo sed -i 's/#PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config
sudo systemctl restart ssh
```

### **4. Setup Fail2Ban**
```bash
sudo apt install -y fail2ban
sudo systemctl start fail2ban
sudo systemctl enable fail2ban
```

## 📁 SETUP APPLICATION

### **1. Clone Repository**
```bash
cd /home/deploy
git clone https://github.com/yourusername/finaflow.git app
cd app
```

### **2. Install Dependencies**
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### **3. Environment Configuration**
```bash
cp .env.example .env.production
nano .env.production
```

**Isi file .env.production:**
```env
APP_NAME="FinaFlow"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=finaflow_prod
DB_USERNAME=finaflow_user
DB_PASSWORD=your-secure-password

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@domain.com
MAIL_FROM_NAME="${APP_NAME}"

# Queue Configuration
QUEUE_CONNECTION=database

# Cache Configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# File Storage
FILESYSTEM_DISK=local

# Additional Security
SANCTUM_STATEFUL_DOMAINS=yourdomain.com
SESSION_DOMAIN=.yourdomain.com
```

### **4. Generate Application Key**
```bash
php artisan key:generate
```

### **5. Setup Database**
```bash
# Create database user
sudo mysql -u root -p
```

```sql
CREATE DATABASE finaflow_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'finaflow_user'@'localhost' IDENTIFIED BY 'your-secure-password';
GRANT ALL PRIVILEGES ON finaflow_prod.* TO 'finaflow_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
# Run migrations
php artisan migrate --force
php artisan db:seed --force
```

### **6. Setup Storage Permissions**
```bash
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache
```

### **7. Setup Queue Workers**
```bash
# Create supervisor config
sudo nano /etc/supervisor/conf.d/finaflow-worker.conf
```

**Isi file /etc/supervisor/conf.d/finaflow-worker.conf:**
```ini
[program:finaflow-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/deploy/app/artisan queue:work --sleep=3 --tries=3 --max-jobs=1000
directory=/home/deploy/app
user=deploy
numprocs=2
priority=999
autostart=true
autorestart=true
startsecs=5
startretries=3
redirect_stderr=true
stdout_logfile=/home/deploy/app/storage/logs/worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start finaflow-worker:*
```

## 🌐 KONFIGURASI NGINX

### **1. Create Nginx Site Configuration**
```bash
sudo nano /etc/nginx/sites-available/finaflow
```

**Isi file /etc/nginx/sites-available/finaflow:**
```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /home/deploy/app/public;
    index index.php index.html index.htm;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;

    # Handle static files
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    # Handle PHP files
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Handle Laravel routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Deny access to sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Health check endpoint
    location /health {
        access_log off;
        return 200 "healthy\n";
        add_header Content-Type text/plain;
    }
}
```

### **2. Enable Site**
```bash
sudo ln -s /etc/nginx/sites-available/finaflow /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### **3. Setup SSL Certificate**
```bash
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

## 🔄 DEPLOYMENT AUTOMATION

### **1. Setup Git Deployment**
```bash
cd /home/deploy/app
git remote add production https://github.com/yourusername/finaflow.git
```

### **2. Create Deployment Script**
```bash
sudo nano /home/deploy/deploy.sh
```

**Isi file /home/deploy/deploy.sh:**
```bash
#!/bin/bash

echo "🚀 Starting deployment..."

# Navigate to app directory
cd /home/deploy/app

# Pull latest changes
echo "📥 Pulling latest changes..."
git pull origin main

# Install/update PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

# Install/update Node dependencies
echo "📦 Installing Node dependencies..."
npm install

# Build assets
echo "🔨 Building assets..."
npm run build

# Run database migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# Clear and cache config
echo "⚙️ Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Cache config for production
echo "⚙️ Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
echo "🔒 Setting permissions..."
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Restart queue workers
echo "🔄 Restarting queue workers..."
sudo supervisorctl restart finaflow-worker:*

# Reload PHP-FPM
echo "🔄 Reloading PHP-FPM..."
sudo systemctl reload php8.2-fpm

echo "✅ Deployment completed successfully!"
```

### **3. Make Script Executable**
```bash
sudo chmod +x /home/deploy/deploy.sh
```

### **4. Setup Webhook (Optional - untuk auto deployment)**
```bash
# Install webhook
sudo apt install -y webhook

# Create webhook config
sudo nano /etc/webhook.conf
```

**Isi file /etc/webhook.conf:**
```json
[
  {
    "id": "deploy",
    "execute-command": "/home/deploy/deploy.sh",
    "command-working-directory": "/home/deploy/app",
    "response-message": "Deployment started",
    "trigger-rule": {
      "match": {
        "type": "payload-hash-sha256",
        "secret": "your-webhook-secret",
        "parameter": {
          "source": "header",
          "name": "X-Hub-Signature-256"
        }
      }
    }
  }
]
```

## 📊 MONITORING & LOGGING

### **1. Setup Log Rotation**
```bash
sudo nano /etc/logrotate.d/finaflow
```

**Isi file /etc/logrotate.d/finaflow:**
```
/home/deploy/app/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        sudo supervisorctl restart finaflow-worker:*
    endscript
}
```

### **2. Setup Monitoring Tools**
```bash
# Install htop for system monitoring
sudo apt install -y htop

# Install monitoring tools
sudo apt install -y monitoring-plugins

# Laravel Telescope (optional - for debugging)
composer require laravel/telescope
php artisan telescope:install
php artisan migrate
```

### **3. Health Check Setup**
```bash
# Add to crontab for health monitoring
crontab -e
```

**Tambahkan ke crontab:**
```bash
# Health check every 5 minutes
*/5 * * * * curl -f https://yourdomain.com/health > /dev/null 2>&1 || echo "Health check failed at $(date)" >> /home/deploy/health.log

# Daily backup (optional)
0 2 * * * /home/deploy/backup.sh
```

## 🔄 BACKUP STRATEGY

### **1. Database Backup**
```bash
sudo nano /home/deploy/backup.sh
```

**Isi file /home/deploy/backup.sh:**
```bash
#!/bin/bash

# Database backup
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/deploy/backups"
DB_NAME="finaflow_prod"
DB_USER="finaflow_user"
DB_PASS="your-secure-password"

mkdir -p $BACKUP_DIR

# Create database backup
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Compress backup
gzip $BACKUP_DIR/db_backup_$DATE.sql

# Keep only last 7 days
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +7 -delete

echo "Backup completed: $BACKUP_DIR/db_backup_$DATE.sql.gz"
```

### **2. File Backup**
```bash
# Add to backup script
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz -C /home/deploy/app storage
```

## 🚨 TROUBLESHOOTING

### **Common Issues & Solutions**

#### **1. Permission Issues**
```bash
# Fix storage permissions
sudo chown -R www-data:www-data /home/deploy/app/storage
sudo chown -R www-data:www-data /home/deploy/app/bootstrap/cache
sudo chmod -R 775 /home/deploy/app/storage
sudo chmod -R 775 /home/deploy/app/bootstrap/cache
```

#### **2. Queue Not Working**
```bash
# Check supervisor status
sudo supervisorctl status

# Restart workers
sudo supervisorctl restart finaflow-worker:*

# Check logs
tail -f /home/deploy/app/storage/logs/worker.log
```

#### **3. 502 Bad Gateway**
```bash
# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Check Nginx error logs
sudo tail -f /var/log/nginx/error.log

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

#### **4. Database Connection Issues**
```bash
# Test database connection
php artisan tinker
DB::connection()->getPdo();
exit
```

#### **5. SSL Issues**
```bash
# Renew SSL certificate
sudo certbot renew

# Test SSL configuration
curl -I https://yourdomain.com
```

## 📈 SCALING CONSIDERATIONS

### **Horizontal Scaling**
- **Load Balancer**: Setup Nginx/HAProxy untuk multiple servers
- **Database**: Consider read replicas untuk performance
- **File Storage**: Use cloud storage (AWS S3, DigitalOcean Spaces)
- **Caching**: Redis cluster untuk high availability

### **Performance Optimization**
```bash
# Enable OPcache
php -m | grep opcache

# Enable JIT compilation (PHP 8.0+)
php -m | grep jit

# Database optimization
php artisan db:monitor
```

## 🔒 SECURITY CHECKLIST

- [ ] SSH key authentication enabled
- [ ] Firewall configured (UFW)
- [ ] Fail2Ban installed
- [ ] SSL certificate installed
- [ ] File permissions correct
- [ ] Database user with limited privileges
- [ ] Environment variables secured
- [ ] Debug mode disabled
- [ ] Sensitive files protected (.env, storage/logs)
- [ ] Regular security updates scheduled

## 📞 SUPPORT & MAINTENANCE

### **Regular Maintenance Tasks**
```bash
# Weekly
sudo apt update && sudo apt upgrade -y
php artisan queue:prune-failed
php artisan telescope:prune

# Monthly
sudo certbot renew
mysqldump backup verification
log rotation check
```

### **Monitoring Commands**
```bash
# System resources
htop
df -h
free -h

# Application logs
tail -f /home/deploy/app/storage/logs/laravel.log
tail -f /var/log/nginx/error.log

# Queue status
php artisan queue:status
sudo supervisorctl status
```

---

## 🎯 DEPLOYMENT CHECKLIST

### **Pre-Deployment**
- [ ] Server provisioned dengan correct specs
- [ ] Domain DNS configured
- [ ] SSL certificate ready
- [ ] Database created dan configured
- [ ] Environment variables prepared

### **Deployment Steps**
- [ ] Code deployed ke server
- [ ] Dependencies installed
- [ ] Database migrated
- [ ] Assets compiled
- [ ] Permissions set correctly
- [ ] Services restarted

### **Post-Deployment**
- [ ] Application accessible via HTTPS
- [ ] Database connections working
- [ ] Queue workers running
- [ ] SSL certificate valid
- [ ] Monitoring tools configured
- [ ] Backup system operational

---

## 🚀 QUICK DEPLOYMENT COMMANDS

```bash
# One-liner deployment (after initial setup)
cd /home/deploy/app && \
git pull origin main && \
composer install --optimize-autoloader --no-dev && \
npm install && npm run build && \
php artisan migrate --force && \
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache && \
sudo chown -R www-data:www-data storage bootstrap/cache && \
sudo supervisorctl restart finaflow-worker:* && \
sudo systemctl reload php8.2-fpm && \
echo "✅ Deployment completed!"
```

---

*Panduan deployment ini dirancang untuk production-ready Laravel application dengan fokus pada security, performance, dan maintainability. Pastikan untuk test setiap langkah di staging environment sebelum production deployment.*
