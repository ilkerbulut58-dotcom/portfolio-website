# IONOS Deployment Guide

Step-by-step guide to deploy the API Gateway on IONOS shared hosting.

## 🎯 Prerequisites

Before you begin, ensure you have:
- IONOS hosting account with PHP and MySQL
- FTP/SFTP credentials
- Access to IONOS control panel
- FileZilla or another FTP client

## 📝 Step-by-Step Deployment

### Step 1: Prepare Your IONOS Account

1. Log in to IONOS control panel
2. Navigate to **Hosting** section
3. Select your hosting package
4. Note down your FTP credentials

### Step 2: Create MySQL Database

1. In IONOS control panel, go to **Databases**
2. Click **Create New Database**
3. Enter database details:
   - **Database Name**: `api_gateway` (or your preferred name)
   - **Username**: Create a new user
   - **Password**: Use a strong password
4. Click **Create**
5. **IMPORTANT**: Note down:
   - Database name
   - Database username
   - Database password
   - Database host (usually `localhost` or similar)

### Step 3: Upload Files via FTP

#### Using FileZilla:

1. Open FileZilla
2. Connect to your server:
   - **Host**: Your FTP hostname (from IONOS)
   - **Username**: Your FTP username
   - **Password**: Your FTP password
   - **Port**: 21 (or as specified by IONOS)

3. Navigate to your web root:
   ```
   Remote path: /
   or
   Remote path: /htdocs/
   ```

4. Upload the `Projekt1` folder:
   - Select the entire `Projekt1` folder from your local computer
   - Drag and drop to `/Projekt1/` on the server
   - Wait for all files to upload (may take a few minutes)

#### File Structure on Server:

```
/Projekt1/
├── public/
├── includes/
├── services/
├── templates/
└── database/
```

### Step 4: Import Database Schema

#### Using phpMyAdmin (Recommended):

1. In IONOS control panel, click **phpMyAdmin**
2. Select your database from the left sidebar
3. Click the **Import** tab
4. Click **Choose File** and select `schema.sql` from:
   ```
   /Projekt1/database/schema.sql
   ```
5. Click **Go** at the bottom
6. Wait for success message

#### Alternative - Using IONOS Database Manager:

1. Go to **Databases** in IONOS control panel
2. Click **Manage** next to your database
3. Look for **Import** or **Execute SQL**
4. Copy contents of `schema.sql` file
5. Paste and execute

### Step 5: Configure Database Connection

1. Using FTP, download the file:
   ```
   /Projekt1/includes/config.php
   ```

2. Open it in a text editor (Notepad++, VSCode, etc.)

3. Update these lines with your IONOS database credentials:
   ```php
   define('DB_HOST', 'localhost');           // Usually localhost
   define('DB_NAME', 'api_gateway');         // Your database name
   define('DB_USER', 'your_db_username');    // Your database username
   define('DB_PASS', 'your_db_password');    // Your database password
   ```

4. Update the base path if needed:
   ```php
   define('BASE_PATH', '/Projekt1');
   ```

5. Save the file

6. Upload the modified `config.php` back to:
   ```
   /Projekt1/includes/config.php
   ```

### Step 6: Verify .htaccess Settings

1. Download `/Projekt1/public/.htaccess`

2. Verify the RewriteBase line:
   ```apache
   RewriteBase /Projekt1/
   ```

3. If deploying to a different path, update accordingly

4. Upload back to server if changed

### Step 7: Set File Permissions

In FileZilla, right-click on folders/files and set permissions:

```
Projekt1/              → 755
├── public/            → 755
│   ├── index.php      → 644
│   └── .htaccess      → 644
├── includes/          → 755
│   └── config.php     → 644
├── services/          → 755
├── templates/         → 755
└── database/          → 755
```

### Step 8: Test Your Deployment

1. Open your browser and visit:
   ```
   https://portfolie.wolkeshopping.de/Projekt1/
   ```

2. You should see the **API Gateway Dashboard**

3. Test API endpoints:
   ```
   https://portfolie.wolkeshopping.de/Projekt1/api/health
   https://portfolie.wolkeshopping.de/Projekt1/api/users
   https://portfolie.wolkeshopping.de/Projekt1/api/products
   ```

## ✅ Verification Checklist

- [ ] Database created successfully
- [ ] Database schema imported (all tables exist)
- [ ] Files uploaded to `/Projekt1/` folder
- [ ] `config.php` updated with correct credentials
- [ ] Dashboard loads at `/Projekt1/`
- [ ] Health endpoint returns JSON: `/api/health`
- [ ] Users endpoint returns data: `/api/users`
- [ ] Products endpoint returns data: `/api/products`
- [ ] Dashboard shows metrics and services

## 🐛 Common IONOS Issues

### Issue 1: 404 Error on All Pages

**Solution:**
- Check if `.htaccess` file exists in `/Projekt1/public/`
- Verify mod_rewrite is enabled (it usually is on IONOS)
- Check `RewriteBase` path in `.htaccess`

### Issue 2: Database Connection Failed

**Solution:**
- Verify credentials in `config.php`
- Check database host (might be `localhost` or specific hostname)
- Ensure database user has ALL privileges
- Test connection using phpMyAdmin

### Issue 3: Blank White Page

**Solution:**
- Check PHP error logs in IONOS control panel
- Temporarily enable error display in `config.php`:
  ```php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ```

### Issue 4: Internal Server Error (500)

**Solution:**
- Check `.htaccess` syntax
- Verify file permissions
- Check PHP version (must be 7.4+)
- Review error logs

### Issue 5: CSS/JS Not Loading

**Solution:**
- Check if `assets/` folder exists
- Verify file permissions
- Check browser console for 404 errors

## 📊 IONOS-Specific Settings

### PHP Version

1. Go to IONOS control panel
2. Navigate to **PHP Settings**
3. Ensure PHP 7.4 or higher is selected
4. Save changes

### Error Logs

Access error logs:
1. IONOS Control Panel → **Logs**
2. Select **Error Logs**
3. Filter by domain/subdomain

### Database Limits

IONOS shared hosting typically has:
- Database size: 1GB - 10GB (depends on package)
- Max connections: 10-50 concurrent
- Query timeout: 30 seconds

Adjust caching strategy if needed.

## 🔐 Security Recommendations

1. **Enable HTTPS**:
   - IONOS provides free SSL certificates
   - Enable in control panel under **SSL Certificates**

2. **Protect Config File**:
   ```apache
   # Add to .htaccess
   <Files "config.php">
       Order allow,deny
       Deny from all
   </Files>
   ```

3. **Disable Directory Listing**:
   Already configured in `.htaccess`

4. **Regular Backups**:
   - Use IONOS backup feature
   - Schedule database exports

## 📞 IONOS Support

If you encounter server-specific issues:

- **Phone**: Check IONOS website for your region
- **Email**: Available in control panel
- **Live Chat**: Usually available in control panel
- **Help Center**: https://www.ionos.com/help

## 🎉 Success!

Your API Gateway is now live on IONOS!

Dashboard URL:
```
https://portfolie.wolkeshopping.de/Projekt1/
```

API Base URL:
```
https://portfolie.wolkeshopping.de/Projekt1/api/
```

## 📝 Next Steps

- [ ] Test all API endpoints
- [ ] Monitor dashboard for activity
- [ ] Set up regular database backups
- [ ] Configure HTTPS if not already enabled
- [ ] Add authentication for admin endpoints
- [ ] Optimize MySQL indexes
- [ ] Set up cron jobs for maintenance queries

## 📧 Questions?

Refer to:
1. Main README.md for API documentation
2. IONOS support for hosting issues
3. phpMyAdmin for database management
4. Server error logs for debugging
