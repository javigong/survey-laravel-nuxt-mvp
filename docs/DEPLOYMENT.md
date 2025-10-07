# Deployment Guide

## Overview

This guide covers deploying the Survey MVP application to production environments. The application consists of a Laravel 12 backend API and a Nuxt.js 4 frontend, both designed for scalable, production-ready deployment.

## Architecture Overview

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Load Balancer │    │   Web Server    │    │   Database      │
│   (Nginx)       │◄──►│   (PHP-FPM)     │◄──►│   (MySQL/       │
│                 │    │                 │    │    PostgreSQL)  │
│ • SSL/TLS       │    │ • Laravel API   │    │                 │
│ • Static Files  │    │ • File Storage  │    │ • Data Storage  │
│ • API Proxy     │    │ • Logs          │    │ • Backups       │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │
         ▼
┌─────────────────┐
│   Frontend      │
│   (Nuxt.js)     │
│                 │
│ • Static Files  │
│ • CDN Ready     │
│ • SPA Routing   │
└─────────────────┘
```

## Prerequisites

### System Requirements

#### Minimum Requirements

- **CPU**: 2 cores
- **RAM**: 4GB
- **Storage**: 20GB SSD
- **OS**: Ubuntu 20.04+ / CentOS 8+ / Debian 11+

#### Recommended Requirements

- **CPU**: 4+ cores
- **RAM**: 8GB+
- **Storage**: 50GB+ SSD
- **OS**: Ubuntu 22.04 LTS

### Software Dependencies

#### Backend Requirements

- **PHP**: 8.4+
- **Composer**: 2.0+
- **Database**: MySQL 8.0+ / PostgreSQL 13+ / SQLite 3.35+
- **Web Server**: Nginx 1.18+ / Apache 2.4+
- **Process Manager**: Supervisor / systemd

#### Frontend Requirements

- **Node.js**: 18+
- **npm**: 8+ / **yarn**: 1.22+
- **Build Tools**: Vite (included)

## Backend Deployment

### 1. Server Setup

#### Update System

```bash
sudo apt update && sudo apt upgrade -y
```

#### Install PHP 8.4

```bash
# Add PHP repository
sudo add-apt-repository ppa:ondrej/php
sudo apt update

# Install PHP and extensions
sudo apt install -y php8.4-fpm php8.4-cli php8.4-mysql php8.4-xml php8.4-mbstring php8.4-curl php8.4-zip php8.4-bcmath php8.4-gd
```

#### Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

#### Install Database (MySQL)

```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

### 2. Application Deployment

#### Clone Repository

```bash
cd /var/www
sudo git clone <repository-url> survey-mvp
sudo chown -R www-data:www-data survey-mvp
cd survey-mvp/backend
```

#### Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
```

#### Environment Configuration

```bash
cp .env.example .env
sudo nano .env
```

**Production Environment Variables:**

```env
APP_NAME="Survey MVP"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=survey_mvp
DB_USERNAME=survey_user
DB_PASSWORD=secure_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

#### Generate Application Key

```bash
php artisan key:generate
```

#### Database Setup

```bash
# Create database
mysql -u root -p
CREATE DATABASE survey_mvp;
CREATE USER 'survey_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON survey_mvp.* TO 'survey_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
php artisan migrate --force

# Seed database (optional)
php artisan db:seed --force
```

#### Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/survey-mvp
sudo chmod -R 755 /var/www/survey-mvp
sudo chmod -R 775 /var/www/survey-mvp/storage
sudo chmod -R 775 /var/www/survey-mvp/bootstrap/cache
```

#### Optimize for Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 3. Web Server Configuration

#### Nginx Configuration

Create `/etc/nginx/sites-available/survey-mvp`:

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/survey-mvp/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # API routes
    location /api {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Handle PHP files
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to hidden files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Security headers
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self' https:; frame-ancestors 'self';";

    # Rate limiting
    limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
    location /api {
        limit_req zone=api burst=20 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

#### Enable Site

```bash
sudo ln -s /etc/nginx/sites-available/survey-mvp /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### PHP-FPM Configuration

Edit `/etc/php/8.4/fpm/pool.d/www.conf`:

```ini
[www]
user = www-data
group = www-data
listen = /var/run/php/php8.4-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

; Security
php_admin_value[disable_functions] = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source
php_admin_value[open_basedir] = /var/www/survey-mvp/backend
```

#### Restart Services

```bash
sudo systemctl restart php8.4-fpm
sudo systemctl restart nginx
```

## Frontend Deployment

### 1. Build Process

#### Install Dependencies

```bash
cd /var/www/survey-mvp/frontend
npm ci --only=production
```

#### Build for Production

```bash
npm run build
```

#### Verify Build

```bash
ls -la .output/public
```

### 2. Static File Serving

#### Nginx Configuration for Frontend

Add to your Nginx configuration:

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;

    # Frontend static files
    location / {
        root /var/www/survey-mvp/frontend/.output/public;
        index index.html;
        try_files $uri $uri/ /index.html;
    }

    # API proxy
    location /api {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # Static assets caching
    location /_nuxt/ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header X-Content-Type-Options "nosniff";
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";
}
```

## SSL/TLS Configuration

### 1. Obtain SSL Certificate

#### Using Let's Encrypt (Certbot)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

#### Automatic Renewal

```bash
sudo crontab -e
# Add this line:
0 12 * * * /usr/bin/certbot renew --quiet
```

### 2. Nginx SSL Configuration

```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;

    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;

    # SSL configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # HSTS
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Rest of your configuration...
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}
```

## Database Optimization

### 1. MySQL Configuration

Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:

```ini
[mysqld]
# Performance
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT

# Connection
max_connections = 200
max_connect_errors = 1000

# Query cache
query_cache_type = 1
query_cache_size = 64M
query_cache_limit = 2M

# Logging
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 2
```

### 2. Database Indexing

```sql
-- Add indexes for better performance
CREATE INDEX idx_surveys_user_status ON surveys(user_id, status);
CREATE INDEX idx_questions_survey_order ON questions(survey_id, order);
CREATE INDEX idx_answers_survey_respondent ON answers(survey_id, respondent_id);
```

## Monitoring and Logging

### 1. Application Logs

#### Laravel Logs

```bash
# View Laravel logs
tail -f /var/www/survey-mvp/backend/storage/logs/laravel.log

# Log rotation
sudo nano /etc/logrotate.d/survey-mvp
```

**Logrotate Configuration:**

```
/var/www/survey-mvp/backend/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
}
```

### 2. System Monitoring

#### Install Monitoring Tools

```bash
# Install htop for process monitoring
sudo apt install htop

# Install iotop for I/O monitoring
sudo apt install iotop

# Install nethogs for network monitoring
sudo apt install nethogs
```

#### System Service Monitoring

```bash
# Check service status
sudo systemctl status nginx
sudo systemctl status php8.4-fpm
sudo systemctl status mysql

# Enable auto-start
sudo systemctl enable nginx
sudo systemctl enable php8.4-fpm
sudo systemctl enable mysql
```

### 3. Performance Monitoring

#### Install New Relic (Optional)

```bash
# Add New Relic repository
echo 'deb http://apt.newrelic.com/debian/ newrelic non-free' | sudo tee /etc/apt/sources.list.d/newrelic.list
wget -O- https://download.newrelic.com/548C16BF.gpg | sudo apt-key add -
sudo apt update

# Install New Relic PHP agent
sudo apt install newrelic-php5
sudo newrelic-install install
```

## Backup Strategy

### 1. Database Backups

#### Automated Database Backup Script

```bash
sudo nano /usr/local/bin/backup-database.sh
```

```bash
#!/bin/bash

# Configuration
DB_NAME="survey_mvp"
DB_USER="survey_user"
DB_PASS="secure_password"
BACKUP_DIR="/var/backups/survey-mvp"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Create database backup
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/database_$DATE.sql

# Compress backup
gzip $BACKUP_DIR/database_$DATE.sql

# Remove backups older than 30 days
find $BACKUP_DIR -name "database_*.sql.gz" -mtime +30 -delete

echo "Database backup completed: database_$DATE.sql.gz"
```

#### Make Script Executable

```bash
sudo chmod +x /usr/local/bin/backup-database.sh
```

#### Schedule Backup

```bash
sudo crontab -e
# Add this line for daily backups at 2 AM:
0 2 * * * /usr/local/bin/backup-database.sh
```

### 2. Application Backups

#### Backup Script

```bash
sudo nano /usr/local/bin/backup-application.sh
```

```bash
#!/bin/bash

# Configuration
APP_DIR="/var/www/survey-mvp"
BACKUP_DIR="/var/backups/survey-mvp"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Create application backup
tar -czf $BACKUP_DIR/application_$DATE.tar.gz -C /var/www survey-mvp

# Remove backups older than 7 days
find $BACKUP_DIR -name "application_*.tar.gz" -mtime +7 -delete

echo "Application backup completed: application_$DATE.tar.gz"
```

## Security Hardening

### 1. Server Security

#### Firewall Configuration

```bash
# Install UFW
sudo apt install ufw

# Configure firewall
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

#### Fail2Ban Configuration

```bash
# Install Fail2Ban
sudo apt install fail2ban

# Configure Fail2Ban
sudo nano /etc/fail2ban/jail.local
```

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 3

[nginx-http-auth]
enabled = true

[nginx-limit-req]
enabled = true
```

### 2. Application Security

#### Laravel Security Headers

```php
// Add to App\Http\Middleware\TrustProxies.php
protected $headers = [
    Request::HEADER_FORWARDED => 'FORWARDED',
    Request::HEADER_X_FORWARDED_FOR => 'X_FORWARDED_FOR',
    Request::HEADER_X_FORWARDED_HOST => 'X_FORWARDED_HOST',
    Request::HEADER_X_FORWARDED_PORT => 'X_FORWARDED_PORT',
    Request::HEADER_X_FORWARDED_PROTO => 'X_FORWARDED_PROTO',
    Request::HEADER_X_FORWARDED_AWS_ELB => 'X_AWS_ELB',
];
```

#### Environment Security

```bash
# Secure environment file
sudo chmod 600 /var/www/survey-mvp/backend/.env
sudo chown www-data:www-data /var/www/survey-mvp/backend/.env
```

## Docker Deployment (Alternative)

### 1. Docker Compose Configuration

Create `docker-compose.yml`:

```yaml
version: "3.8"

services:
  app:
    build:
      context: ./backend
      dockerfile: Dockerfile
    container_name: survey-mvp-backend
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./backend:/var/www
      - ./storage:/var/www/storage
    networks:
      - survey-mvp

  nginx:
    image: nginx:alpine
    container_name: survey-mvp-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
      - ./frontend/.output/public:/var/www/html
      - ./ssl:/etc/nginx/ssl
    depends_on:
      - app
    networks:
      - survey-mvp

  mysql:
    image: mysql:8.0
    container_name: survey-mvp-mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: survey_mvp
      MYSQL_USER: survey_user
      MYSQL_PASSWORD: secure_password
      MYSQL_ROOT_PASSWORD: root_password
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - survey-mvp

volumes:
  mysql_data:

networks:
  survey-mvp:
    driver: bridge
```

### 2. Backend Dockerfile

Create `backend/Dockerfile`:

```dockerfile
FROM php:8.4-fpm-alpine

WORKDIR /var/www

RUN apk add --no-cache \
    libzip-dev \
    zip \
    unzip \
    mysql-client

RUN docker-php-ext-install pdo pdo_mysql zip

COPY . .

RUN composer install --optimize-autoloader --no-dev

RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www
RUN chmod -R 775 /var/www/storage
RUN chmod -R 775 /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
```

### 3. Deploy with Docker

```bash
# Build and start services
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate --force

# Cache configuration
docker-compose exec app php artisan config:cache
```

## Troubleshooting

### Common Issues

#### 1. Permission Errors

```bash
# Fix Laravel permissions
sudo chown -R www-data:www-data /var/www/survey-mvp
sudo chmod -R 755 /var/www/survey-mvp
sudo chmod -R 775 /var/www/survey-mvp/backend/storage
sudo chmod -R 775 /var/www/survey-mvp/backend/bootstrap/cache
```

#### 2. Database Connection Issues

```bash
# Check MySQL status
sudo systemctl status mysql

# Test database connection
mysql -u survey_user -p survey_mvp
```

#### 3. Nginx Configuration Errors

```bash
# Test Nginx configuration
sudo nginx -t

# Check Nginx error logs
sudo tail -f /var/log/nginx/error.log
```

#### 4. PHP-FPM Issues

```bash
# Check PHP-FPM status
sudo systemctl status php8.4-fpm

# Check PHP-FPM logs
sudo tail -f /var/log/php8.4-fpm.log
```

### Performance Issues

#### 1. Slow Database Queries

```bash
# Enable slow query log
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
# Add: slow_query_log = 1

# Check slow queries
sudo tail -f /var/log/mysql/slow.log
```

#### 2. High Memory Usage

```bash
# Check memory usage
htop

# Optimize PHP-FPM settings
sudo nano /etc/php/8.4/fpm/pool.d/www.conf
# Adjust pm.max_children based on available memory
```

## Maintenance

### 1. Regular Updates

#### System Updates

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Update Composer dependencies
cd /var/www/survey-mvp/backend
composer update

# Update npm dependencies
cd /var/www/survey-mvp/frontend
npm update
```

#### Application Updates

```bash
# Pull latest changes
cd /var/www/survey-mvp
sudo git pull origin main

# Update backend
cd backend
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache

# Update frontend
cd ../frontend
npm ci --only=production
npm run build
```

### 2. Health Checks

#### Application Health Check Script

```bash
sudo nano /usr/local/bin/health-check.sh
```

```bash
#!/bin/bash

# Check if services are running
if ! systemctl is-active --quiet nginx; then
    echo "Nginx is not running"
    exit 1
fi

if ! systemctl is-active --quiet php8.4-fpm; then
    echo "PHP-FPM is not running"
    exit 1
fi

if ! systemctl is-active --quiet mysql; then
    echo "MySQL is not running"
    exit 1
fi

# Check if application responds
if ! curl -f http://localhost/api/v1/health > /dev/null 2>&1; then
    echo "Application is not responding"
    exit 1
fi

echo "All services are healthy"
```

#### Schedule Health Checks

```bash
sudo chmod +x /usr/local/bin/health-check.sh
sudo crontab -e
# Add: */5 * * * * /usr/local/bin/health-check.sh
```

---

**Last Updated**: January 7, 2025  
**Deployment Version**: 1.0.0
