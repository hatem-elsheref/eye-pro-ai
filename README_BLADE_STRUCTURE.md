# Match Analysis Platform - Blade Views Documentation

## 📁 Directory Structure

```
resources/views/admin/
├── layouts/
│   ├── master.blade.php         # Main layout with navbar, sidebar, footer
│   └── auth.blade.php            # Authentication layout (no navbar/sidebar)
│
├── _shared/                      # Shared components
│   ├── _head.blade.php           # CSS styles and meta tags
│   ├── _navbar.blade.php         # Top navigation bar
│   ├── _sidebar.blade.php        # Left sidebar menu
│   ├── _footer.blade.php         # Footer section
│   ├── _scripts.blade.php        # JavaScript includes
│   └── _alerts.blade.php         # Flash message alerts
│
├── auth/                         # Authentication pages
│   ├── login.blade.php           # Login page
│   ├── register.blade.php        # Registration page
│   ├── forgot-password.blade.php # Forgot password page
│   └── reset-password.blade.php  # Reset password page
│
├── matches/                      # Match management pages
│   ├── index.blade.php           # List all matches
│   ├── create.blade.php          # Upload new match
│   ├── show.blade.php            # View match details
│   └── edit.blade.php            # Edit match information
│
├── admin/                        # Admin panel pages
│   └── index.blade.php           # Admin dashboard
│
├── notifications/                # Notification pages
│   └── index.blade.php           # View notifications
│
├── dashboard.blade.php           # Main dashboard
├── profile.blade.php             # User profile settings
└── support.blade.php             # Support/Help page
```

## 🎨 Design Features

### Color Scheme
- **Primary Color**: `#4338ca` (Indigo)
- **Secondary Color**: `#6366f1` (Purple)
- **Background**: Gradient from light blue to light purple
- **Text Colors**: Dark gray for content, medium gray for secondary text

### UI Components

#### 1. Authentication Pages
- Clean, centered design with card layout
- Tab-based navigation between Sign In/Sign Up
- Form validation and error display
- Responsive design for mobile devices

#### 2. Dashboard
- Stats cards showing:
  - Total matches uploaded
  - Upload status
- Quick action buttons
- Recent matches table (if available)
- Account pending approval alert

#### 3. Matches Management
- Table view of all matches
- Upload via file or URL
- Progress indicator for uploads
- Match details with video player
- Edit and delete functionality

#### 4. Profile Page
- Personal information management
- Password change form
- Account deletion option (danger zone)

#### 5. Support Page
- Contact form with categories
- FAQ section with collapsible details
- Priority selection

#### 6. Admin Panel
- User approval management
- System statistics
- Settings configuration
- Pending users table

## 🔧 How to Use

### Extending Layouts

**For authenticated pages (with navbar/sidebar):**
```php
@extends('admin.layouts.master')

@section('title', 'Your Page Title')

@section('content')
    <!-- Your content here -->
@endsection
```

**For authentication pages (no navbar/sidebar):**
```php
@extends('admin.layouts.auth')

@section('title', 'Your Page Title')

@section('content')
    <!-- Your content here -->
@endsection
```

### Using Shared Components

The master layout automatically includes:
- `_head.blade.php` - In `<head>` section
- `_navbar.blade.php` - Top navigation
- `_sidebar.blade.php` - Left sidebar
- `_footer.blade.php` - Bottom footer
- `_scripts.blade.php` - JavaScript files
- `_alerts.blade.php` - Flash messages

### Adding Custom Styles

```php
@push('styles')
<style>
    /* Your custom CSS */
</style>
@endpush
```

### Adding Custom Scripts

```php
@push('scripts')
<script>
    // Your custom JavaScript
</script>
@endpush
```

### Flash Messages

Set flash messages in your controller:
```php
return redirect()->back()->with('success', 'Operation completed!');
return redirect()->back()->with('error', 'Something went wrong!');
return redirect()->back()->with('warning', 'Please be careful!');
return redirect()->back()->with('info', 'Good to know!');
```

## 🚀 Getting Started

### 1. Set Up Routes
Copy the routes from `ROUTES_EXAMPLE.php` to your `routes/web.php` file.

### 2. Create Controllers
Generate controllers using Artisan:
```bash
php artisan make:controller AuthController
php artisan make:controller DashboardController
php artisan make:controller MatchController
php artisan make:controller ProfileController
php artisan make:controller SupportController
php artisan make:controller AdminController
php artisan make:controller NotificationController
```

### 3. Create Models
Generate the Match model:
```bash
php artisan make:model Match -m
```

### 4. Update User Model
Add these fields to your users table migration:
```php
$table->boolean('is_approved')->default(false);
$table->boolean('is_admin')->default(false);
$table->string('phone')->nullable();
```

### 5. Create Match Migration
Add these fields to your matches table:
```php
$table->foreignId('user_id')->constrained()->onDelete('cascade');
$table->string('name');
$table->string('type'); // 'file' or 'url'
$table->string('status')->default('processing');
$table->text('video_url')->nullable();
$table->text('description')->nullable();
$table->string('tags')->nullable();
$table->string('duration')->nullable();
$table->string('file_size')->nullable();
$table->text('analysis')->nullable();
```

### 6. Create Middleware
Create admin middleware:
```bash
php artisan make:middleware AdminMiddleware
```

In `app/Http/Middleware/AdminMiddleware.php`:
```php
public function handle(Request $request, Closure $next)
{
    if (!auth()->user()->is_admin) {
        abort(403, 'Unauthorized action.');
    }
    return $next($request);
}
```

Register in `app/Http/Kernel.php`:
```php
protected $middlewareAliases = [
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
];
```

## 📝 Route Names Reference

### Authentication
- `login` - GET /login
- `login.post` - POST /login
- `register` - GET /register
- `register.post` - POST /register
- `password.request` - GET /forgot-password
- `password.email` - POST /forgot-password
- `password.reset` - GET /reset-password/{token}
- `password.update` - POST /reset-password
- `logout` - POST /logout

### Dashboard
- `dashboard` - GET /
- `dashboard.index` - GET /dashboard

### Matches
- `matches.index` - GET /matches
- `matches.create` - GET /matches/create
- `matches.store` - POST /matches
- `matches.show` - GET /matches/{id}
- `matches.edit` - GET /matches/{id}/edit
- `matches.update` - PUT /matches/{id}
- `matches.destroy` - DELETE /matches/{id}

### Profile
- `profile` - GET /profile
- `profile.update` - PUT /profile
- `profile.password.update` - PUT /profile/password
- `profile.delete` - DELETE /profile

### Support
- `support` - GET /support
- `support.submit` - POST /support

### Admin
- `admin.index` - GET /admin
- `admin.users.approve` - POST /admin/users/{id}/approve
- `admin.users.reject` - DELETE /admin/users/{id}/reject
- `admin.settings.update` - PUT /admin/settings

### Notifications
- `notifications.index` - GET /notifications
- `notifications.mark-read` - POST /notifications/{id}/read

## 🎯 Features Implemented

✅ Modern, responsive design with gradient backgrounds
✅ Clean authentication flow (login, register, forgot/reset password)
✅ User dashboard with statistics
✅ Match upload (file or URL)
✅ Match listing with status indicators
✅ Match detail view with video player
✅ Profile management with password change
✅ Support page with FAQ
✅ Admin panel for user approvals
✅ Notification system
✅ Flash message alerts
✅ Mobile-responsive sidebar
✅ User menu dropdown
✅ Empty state designs
✅ Form validation displays

## 🔐 Security Considerations

1. Always use `@csrf` in forms
2. Use `@method('PUT')` or `@method('DELETE')` for resource updates/deletes
3. Validate user permissions in controllers
4. Sanitize user input
5. Use Laravel's authorization features

## 📱 Responsive Design

The application is fully responsive with:
- Collapsible sidebar on mobile
- Stacked forms on small screens
- Touch-friendly buttons and inputs
- Optimized card layouts

## 🎨 Customization

### Change Colors
Edit the CSS variables in `_head.blade.php`:
```css
:root {
    --primary-color: #4338ca;
    --primary-hover: #3730a3;
    /* ... other colors */
}
```

### Modify Layout
- Edit `master.blade.php` for main structure
- Edit `_navbar.blade.php` for navigation
- Edit `_sidebar.blade.php` for menu items

## 📞 Support

For questions or issues, please refer to the Laravel documentation or contact your development team.

---

**Version**: 1.0  
**Created**: November 2025  
**Framework**: Laravel 10.x with Blade Templates



