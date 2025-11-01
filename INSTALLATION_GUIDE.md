# Match Analysis Platform - Installation & Setup Guide

## 🚀 Quick Start Guide

Follow these steps to get your Match Analysis Platform up and running.

## 📋 Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL or PostgreSQL
- Node.js & NPM (for frontend assets)
- Laravel 10.x

## 📦 Installation Steps

### 1. Database Configuration

Edit your `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=match_analysis
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 2. Update User Migration

Edit `database/migrations/0001_01_01_000000_create_users_table.php`:

```php
public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->boolean('is_approved')->default(false);
        $table->boolean('is_admin')->default(false);
        $table->string('phone')->nullable();
        $table->rememberToken();
        $table->timestamps();
    });
}
```

### 3. Create Matches Migration

```bash
php artisan make:migration create_matches_table
```

Edit the migration file:
```php
public function up(): void
{
    Schema::create('matches', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->string('type'); // 'file' or 'url'
        $table->string('status')->default('processing'); // processing, completed, failed
        $table->text('video_url')->nullable();
        $table->string('video_path')->nullable();
        $table->text('description')->nullable();
        $table->string('tags')->nullable();
        $table->string('duration')->nullable();
        $table->string('file_size')->nullable();
        $table->text('analysis')->nullable();
        $table->timestamps();
    });
}
```

### 4. Create Support Tickets Migration

```bash
php artisan make:migration create_support_tickets_table
```

```php
public function up(): void
{
    Schema::create('support_tickets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('subject');
        $table->string('category');
        $table->string('priority');
        $table->text('message');
        $table->string('status')->default('open');
        $table->timestamps();
    });
}
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Create Models

**Match Model** (`app/Models/Match.php`):
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'status',
        'video_url',
        'video_path',
        'description',
        'tags',
        'duration',
        'file_size',
        'analysis',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

**Update User Model** (`app/Models/User.php`):
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'is_approved',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_approved' => 'boolean',
        'is_admin' => 'boolean',
    ];

    public function matches()
    {
        return $this->hasMany(Match::class);
    }
}
```

### 7. Create Controllers

Generate all required controllers:

```bash
php artisan make:controller AuthController
php artisan make:controller DashboardController
php artisan make:controller MatchController --resource
php artisan make:controller ProfileController
php artisan make:controller SupportController
php artisan make:controller AdminController
php artisan make:controller NotificationController
```

### 8. Implement Controllers

Copy the controller implementations from `ROUTES_EXAMPLE.php` file.

### 9. Create Admin Middleware

```bash
php artisan make:middleware AdminMiddleware
```

Edit `app/Http/Middleware/AdminMiddleware.php`:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
```

Register the middleware in `bootstrap/app.php` (Laravel 11) or `app/Http/Kernel.php` (Laravel 10):

**Laravel 11:**
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

**Laravel 10:**
```php
protected $middlewareAliases = [
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
];
```

### 10. Add Routes

Copy all routes from `ROUTES_EXAMPLE.php` to `routes/web.php`.

### 11. Create Seeder for Admin User

```bash
php artisan make:seeder AdminUserSeeder
```

Edit `database/seeders/AdminUserSeeder.php`:
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_approved' => true,
        ]);

        User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_approved' => true,
        ]);

        User::create([
            'name' => 'Pending User',
            'email' => 'pending@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_approved' => false,
        ]);
    }
}
```

Run the seeder:
```bash
php artisan db:seed --class=AdminUserSeeder
```

### 12. Configure File Storage

Edit `config/filesystems.php` to ensure public disk is configured:
```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],
```

Create symbolic link:
```bash
php artisan storage:link
```

### 13. Install Frontend Dependencies

```bash
npm install
npm run build
```

Or for development:
```bash
npm run dev
```

### 14. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## 🔐 Default Login Credentials

After running the seeder:

**Admin Account:**
- Email: `admin@example.com`
- Password: `password`

**Regular User:**
- Email: `user@example.com`
- Password: `password`

**Pending User:**
- Email: `pending@example.com`
- Password: `password`

## 🧪 Testing the Application

### 1. Start Development Server

```bash
php artisan serve
```

Visit: http://localhost:8000

### 2. Test Authentication

1. Go to `/login`
2. Login with admin credentials
3. You should see the dashboard

### 3. Test User Registration

1. Go to `/register`
2. Create a new account
3. Note: New accounts require admin approval by default

### 4. Test Admin Panel

1. Login as admin
2. Go to `/admin`
3. Approve pending users
4. Manage system settings

### 5. Test Match Upload

1. Login as approved user
2. Go to `/matches/create`
3. Upload a match (file or URL)
4. View match details

## 📁 File Upload Configuration

For large file uploads, update `php.ini`:
```ini
upload_max_filesize = 2048M
post_max_size = 2048M
max_execution_time = 300
memory_limit = 512M
```

## 🔒 Security Checklist

- [ ] Change default admin password
- [ ] Set strong `APP_KEY` in `.env`
- [ ] Enable HTTPS in production
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Configure CORS if needed
- [ ] Set up rate limiting for API endpoints
- [ ] Enable CSRF protection (enabled by default)
- [ ] Configure trusted proxies if behind load balancer

## 🚀 Production Deployment

### 1. Environment Configuration

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### 2. Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

### 3. Set Proper Permissions

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Configure Queue Worker (Optional)

For processing uploads in background:

```bash
php artisan queue:work --daemon
```

Set up supervisor to keep queue worker running:
```ini
[program:match-analysis-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/worker.log
```

## 🐛 Troubleshooting

### Issue: 404 on any route
**Solution:** Run `php artisan route:clear` and check `.htaccess` file exists in `public/`

### Issue: CSRF token mismatch
**Solution:** Clear cache with `php artisan cache:clear` and ensure session is configured correctly

### Issue: File upload fails
**Solution:** Check `php.ini` settings and storage permissions

### Issue: Login redirects to login
**Solution:** Check session configuration and ensure session driver is set correctly in `.env`

### Issue: Assets not loading
**Solution:** Run `npm run build` and `php artisan storage:link`

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Blade Templates](https://laravel.com/docs/blade)
- [Laravel Authentication](https://laravel.com/docs/authentication)
- [File Storage](https://laravel.com/docs/filesystem)

## 📧 Support

For issues or questions, please refer to the documentation or contact your development team.

---

**Last Updated**: November 2025  
**Version**: 1.0



