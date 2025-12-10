# API Gateway with Microservices - PHP + MySQL Version

A scalable API Gateway demonstration with modular microservices architecture built using PHP and MySQL, optimized for shared hosting deployment (IONOS, cPanel, etc.).

## 🎯 Features

- **API Gateway** - Central routing to microservices
- **3 Microservices** - Users, Products, Orders
- **MySQL Caching** - Fast response caching system
- **Rate Limiting** - Request throttling per endpoint
- **Request Logging** - Activity monitoring and metrics
- **Real-time Dashboard** - Professional monitoring interface
- **Shared Hosting Ready** - Works on IONOS, cPanel, and similar platforms

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled
- Shared hosting or VPS with PHP/MySQL support

## 🚀 Installation Guide

### Step 1: Upload Files

1. Download or clone this project
2. Upload the entire `Projekt1` folder to your web server
3. Place it in your desired location (e.g., `/Projekt1/` for subfolder deployment)

### Step 2: Create MySQL Database

1. Log in to your hosting control panel (cPanel, Plesk, etc.)
2. Create a new MySQL database (e.g., `api_gateway`)
3. Create a MySQL user and assign it to the database
4. Grant ALL privileges to the user
5. Note down:
   - Database name
   - Database user
   - Database password
   - Database host (usually `localhost`)

### Step 3: Import Database Schema

1. Go to phpMyAdmin
2. Select your database
3. Click "Import" tab
4. Choose the file: `Projekt1/database/schema.sql`
5. Click "Go" to import

This will create all necessary tables and insert sample data.

### Step 4: Configure Database Connection

Edit the file `Projekt1/includes/config.php`:

```php
// Update these lines with your database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'api_gateway');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
```

### Step 5: Verify .htaccess Configuration

If deploying to a subfolder other than `/Projekt1/`, update:

1. `Projekt1/public/.htaccess` - Change `RewriteBase /Projekt1/`
2. `Projekt1/includes/config.php` - Change `define('BASE_PATH', '/Projekt1');`

For root domain deployment, use:
- `RewriteBase /`
- `define('BASE_PATH', '');`

### Step 6: Set File Permissions

Ensure proper permissions:

```bash
chmod 755 Projekt1/
chmod 644 Projekt1/public/.htaccess
chmod 644 Projekt1/includes/config.php
```

### Step 7: Access Your Application

Visit your domain:

```
https://portfolie.wolkeshopping.de/Projekt1/
```

You should see the API Gateway Dashboard!

## 📡 API Endpoints

### Gateway Endpoints

- `GET /api/health` - Health check
- `GET /api/metrics` - Gateway metrics
- `GET /api/stats/services` - Service statistics
- `GET /api/logs` - Activity logs
- `GET /api/cache/stats` - Cache statistics
- `POST /api/cache/clear` - Clear cache

### Users Service

- `GET /api/users` - List all users
- `GET /api/users/{id}` - Get user by ID
- `POST /api/users` - Create new user
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user

### Products Service

- `GET /api/products` - List all products
- `GET /api/products/{id}` - Get product by ID
- `GET /api/products?category=Electronics` - Filter by category
- `POST /api/products` - Create new product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product

### Orders Service

- `GET /api/orders` - List all orders
- `GET /api/orders/{id}` - Get order by ID
- `POST /api/orders` - Create new order
- `PUT /api/orders/{id}` - Update order status
- `DELETE /api/orders/{id}` - Delete order

## 🔧 Configuration Options

### Cache Settings

In `includes/config.php`:

```php
define('CACHE_ENABLED', true);
define('CACHE_DEFAULT_TTL', 300); // 5 minutes
```

### Rate Limiting

```php
define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_WINDOW', 60); // 60 seconds
define('RATE_LIMIT_MAX_REQUESTS', 100); // max requests per window
```

### CORS Settings

```php
define('ENABLE_CORS', true);
```

## 🧹 Maintenance

### Clean Old Data

Run these queries periodically (via phpMyAdmin or cron):

```sql
-- Clean expired cache
DELETE FROM gateway_cache WHERE expires_at < NOW();

-- Clean old rate limits (older than 1 hour)
DELETE FROM rate_limits WHERE window_start < DATE_SUB(NOW(), INTERVAL 1 HOUR);

-- Clean old logs (older than 30 days)
DELETE FROM request_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

### Backup Database

Regular backups recommended:

```bash
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql
```

## 📊 Monitoring

Access the dashboard at:
```
https://portfolie.wolkeshopping.de/Projekt1/
```

The dashboard shows:
- Real-time metrics
- Service status
- Activity logs
- Cache performance

## 🐛 Troubleshooting

### 404 Errors on API Endpoints

- Check if mod_rewrite is enabled
- Verify .htaccess file exists in `/public/` folder
- Check RewriteBase path in .htaccess

### Database Connection Errors

- Verify database credentials in `config.php`
- Ensure MySQL user has proper privileges
- Check if database exists

### Blank Page or 500 Error

- Check PHP error logs
- Enable error display temporarily:
  ```php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ```

### Rate Limiting Issues

- Check `rate_limits` table exists
- Verify table has proper indexes
- Ensure MySQL user can INSERT/UPDATE

## 📁 Project Structure

```
Projekt1/
├── public/
│   ├── index.php          # Front controller
│   ├── .htaccess          # Apache rewrite rules
│   └── assets/            # Static files (CSS, JS, images)
├── includes/
│   ├── config.php         # Configuration
│   ├── database.php       # Database connection
│   ├── Router.php         # Request router
│   ├── Cache.php          # MySQL caching
│   ├── RateLimiter.php    # Rate limiting
│   └── Logger.php         # Request logging
├── services/
│   ├── UsersService.php   # Users microservice
│   ├── ProductsService.php # Products microservice
│   └── OrdersService.php  # Orders microservice
├── templates/
│   └── dashboard.php      # Dashboard UI
└── database/
    └── schema.sql         # Database schema
```

## 🔐 Security Notes

- Change database credentials from defaults
- Use HTTPS for production
- Implement authentication for admin endpoints
- Regularly update dependencies
- Monitor access logs
- Set proper file permissions
- Never commit credentials to version control

## 📝 License

MIT License - Free for personal and commercial use

## 👨‍💻 Developer

Created for portfolio demonstration of microservices architecture with PHP and MySQL.

## 🆘 Support

For issues or questions:
1. Check the troubleshooting section
2. Review server error logs
3. Verify database connection
4. Contact your hosting provider for server-specific issues
