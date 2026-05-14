# 🌐 How to Access GMBSMS Website

## Quick Access (Current Setup)

### Direct URL:
```
http://localhost/GMBSMS/public
```

**Steps:**
1. Make sure XAMPP Apache and MySQL are running
2. Open your browser
3. Go to: `http://localhost/GMBSMS/public`
4. Login with your credentials

---

## 🎯 Better Setup: Clean URL (Optional)

If you want to access via `http://gmbsms.local` instead of `http://localhost/GMBSMS/public`:

### Step 1: Edit Apache Virtual Host

1. Open XAMPP Control Panel
2. Click "Config" next to Apache
3. Select "httpd-vhosts.conf"
4. Add this at the end:

```apache
<VirtualHost *:80>
    ServerName gmbsms.local
    DocumentRoot "C:/xampp/htdocs/GMBSMS/public"
    
    <Directory "C:/xampp/htdocs/GMBSMS/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Step 2: Edit Windows Hosts File

1. Open Notepad as Administrator
2. Open file: `C:\Windows\System32\drivers\etc\hosts`
3. Add this line at the end:

```
127.0.0.1    gmbsms.local
```

4. Save the file

### Step 3: Update .env File

Change APP_URL in `.env`:
```
APP_URL=http://gmbsms.local
```

### Step 4: Restart Apache

1. In XAMPP Control Panel, click "Stop" for Apache
2. Click "Start" again

### Step 5: Access Website

Now you can access via:
```
http://gmbsms.local
```

---

## 📁 Project Structure

```
c:\xampp\htdocs\GMBSMS\
├── public/              ← Web root (access this folder)
│   ├── index.php       ← Entry point
│   └── ...
├── app/                ← Application code
├── resources/          ← Views, CSS, JS
├── routes/             ← Route definitions
└── .env               ← Configuration
```

---

## ✅ Checklist Before Accessing

- [ ] XAMPP Apache is running (green in control panel)
- [ ] XAMPP MySQL is running (green in control panel)
- [ ] Database `gmbsms_db` exists
- [ ] `.env` file is configured correctly
- [ ] Browser is open

---

## 🔐 Default Login Credentials

Check your database `users` table for login credentials, or create a new user:

**Admin:**
- Email: (check database)
- Password: (check database)

**Member:**
- Email: (check database)
- Password: (check database)

---

## 🐛 Troubleshooting

### Issue: "404 Not Found"
**Solution:** Make sure you're accessing `/public` folder:
```
http://localhost/GMBSMS/public
```

### Issue: "500 Internal Server Error"
**Solution:** 
1. Check Apache error logs in XAMPP
2. Make sure `.env` file exists
3. Run: `php artisan config:clear`

### Issue: "Database connection error"
**Solution:**
1. Make sure MySQL is running in XAMPP
2. Check database name in `.env` matches your database
3. Verify DB_USERNAME and DB_PASSWORD in `.env`

### Issue: "Page not styled correctly"
**Solution:**
1. Clear browser cache (Ctrl + F5)
2. Check if CSS/JS files are loading in browser console

---

## 📞 Need Help?

- Check Apache error logs: `c:\xampp\apache\logs\error.log`
- Check Laravel logs: `c:\xampp\htdocs\GMBSMS\storage\logs\laravel.log`
- Restart Apache and MySQL in XAMPP Control Panel
