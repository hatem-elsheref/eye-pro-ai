<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    :root {
        --primary-color: #4338ca;
        --primary-hover: #3730a3;
        --secondary-color: #6366f1;
        --bg-light: #f9fafb;
        --bg-white: #ffffff;
        --text-dark: #1f2937;
        --text-gray: #6b7280;
        --border-color: #e5e7eb;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
    }
    
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background: linear-gradient(135deg, #e0f2fe 0%, #ddd6fe 100%);
        min-height: 100vh;
        color: var(--text-dark);
    }
    
    /* Auth Pages */
    .auth-page {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 20px;
    }
    
    .auth-container {
        width: 100%;
        max-width: 480px;
    }
    
    .auth-card {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .auth-logo {
        width: 64px;
        height: 64px;
        background: var(--primary-color);
        border-radius: 50%;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 28px;
    }
    
    .auth-title {
        font-size: 24px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 8px;
        color: var(--text-dark);
    }
    
    .auth-subtitle {
        text-align: center;
        color: var(--text-gray);
        margin-bottom: 24px;
        font-size: 14px;
    }
    
    .auth-tabs {
        display: flex;
        gap: 0;
        margin-bottom: 24px;
        background: var(--bg-light);
        border-radius: 8px;
        padding: 4px;
    }
    
    .auth-tab {
        flex: 1;
        padding: 10px;
        text-align: center;
        border: none;
        background: transparent;
        color: var(--text-gray);
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        border-radius: 6px;
        transition: all 0.2s;
    }
    
    .auth-tab.active {
        background: var(--bg-white);
        color: var(--text-dark);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 6px;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-dark);
    }
    
    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s;
        outline: none;
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(67, 56, 202, 0.1);
    }
    
    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .btn-primary {
        background: var(--primary-color);
        color: white;
        width: 100%;
    }
    
    .btn-primary:hover {
        background: var(--primary-hover);
    }
    
    .btn-secondary {
        background: var(--bg-light);
        color: var(--text-dark);
    }
    
    .btn-secondary:hover {
        background: var(--border-color);
    }
    
    /* App Layout */
    .app-container {
        display: flex;
        min-height: 100vh;
    }
    
    .app-container.has-navbar {
        padding-top: 64px;
    }
    
    .main-content {
        flex: 1;
        padding: 24px;
        background: transparent;
    }
    
    .main-content.has-sidebar {
        margin-left: 240px;
    }
    
    /* Navbar */
    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 64px;
        background: var(--bg-white);
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        padding: 0 24px;
        z-index: 100;
    }
    
    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 18px;
        font-weight: 700;
        color: var(--text-dark);
        text-decoration: none;
    }
    
    .navbar-logo {
        width: 36px;
        height: 36px;
        object-fit: contain;
        border-radius: 8px;
    }
    
    .navbar-menu {
        display: flex;
        align-items: center;
        gap: 24px;
        margin-left: auto;
    }
    
    .navbar-link {
        color: var(--text-gray);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: color 0.2s;
    }
    
    .navbar-link:hover,
    .navbar-link.active {
        color: var(--primary-color);
    }
    
    .user-menu {
        position: relative;
    }
    
    .user-menu-btn {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 18px;
        color: var(--text-dark);
        padding: 8px;
    }
    
    .user-menu-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        min-width: 200px;
        display: none;
    }
    
    .user-menu-dropdown.show {
        display: block;
    }
    
    .user-menu-header {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-color);
        font-size: 14px;
        color: var(--text-gray);
    }
    
    .user-menu-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        color: var(--text-dark);
        text-decoration: none;
        font-size: 14px;
        transition: background 0.2s;
    }
    
    .user-menu-item:hover {
        background: var(--bg-light);
    }
    
    .user-menu-item.danger {
        color: var(--danger-color);
    }
    
    /* Sidebar */
    .sidebar {
        position: fixed;
        left: 0;
        top: 64px;
        width: 240px;
        height: calc(100vh - 64px);
        background: var(--bg-white);
        border-right: 1px solid var(--border-color);
        padding: 16px 0;
        overflow-y: auto;
    }
    
    .sidebar-menu {
        list-style: none;
    }
    
    .sidebar-item {
        margin-bottom: 4px;
    }
    
    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 24px;
        color: var(--text-gray);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .sidebar-link:hover,
    .sidebar-link.active {
        color: var(--primary-color);
        background: rgba(67, 56, 202, 0.05);
    }
    
    .sidebar-link.active {
        border-right: 3px solid var(--primary-color);
    }
    
    /* Dashboard Cards */
    .dashboard-header {
        margin-bottom: 32px;
    }
    
    .dashboard-title {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--text-dark);
    }
    
    .dashboard-subtitle {
        color: var(--text-gray);
        font-size: 14px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    
    .stat-card {
        background: var(--bg-white);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    
    .stat-card.primary {
        background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%);
        color: white;
    }
    
    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    
    .stat-title {
        font-size: 14px;
        font-weight: 500;
        opacity: 0.9;
    }
    
    .stat-icon {
        font-size: 20px;
        opacity: 0.7;
    }
    
    .stat-value {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 4px;
    }
    
    .stat-description {
        font-size: 12px;
        opacity: 0.7;
    }
    
    /* Alert Messages */
    .alert {
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    
    .alert-info {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #93c5fd;
    }
    
    .alert-warning {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fcd34d;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #6ee7b7;
    }
    
    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
    
    /* Empty States */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: var(--bg-white);
        border-radius: 12px;
    }
    
    .empty-state-icon {
        font-size: 64px;
        color: var(--text-gray);
        opacity: 0.5;
        margin-bottom: 16px;
    }
    
    .empty-state-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--text-dark);
    }
    
    .empty-state-text {
        color: var(--text-gray);
        font-size: 14px;
    }
    
    /* Upload Form */
    .upload-card {
        background: var(--bg-white);
        border-radius: 12px;
        padding: 32px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        max-width: 700px;
        margin: 0 auto;
    }
    
    .upload-header {
        text-align: center;
        margin-bottom: 32px;
    }
    
    .upload-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 8px;
    }
    
    .upload-subtitle {
        color: var(--text-gray);
        font-size: 14px;
    }
    
    .upload-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
    }
    
    .upload-tab {
        flex: 1;
        padding: 12px;
        text-align: center;
        border: 1px solid var(--border-color);
        background: var(--bg-light);
        color: var(--text-gray);
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        border-radius: 8px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .upload-tab.active {
        background: var(--bg-white);
        border-color: var(--primary-color);
        color: var(--primary-color);
    }
    
    .file-upload-area {
        border: 2px dashed var(--border-color);
        border-radius: 8px;
        padding: 32px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .file-upload-area:hover {
        border-color: var(--primary-color);
        background: rgba(67, 56, 202, 0.02);
    }
    
    
    .progress-bar {
        width: 100%;
        height: 8px;
        background: var(--bg-light);
        border-radius: 4px;
        overflow: hidden;
        margin: 16px 0;
    }
    
    .progress-fill {
        height: 100%;
        background: var(--primary-color);
        transition: width 0.3s;
    }
    
    @media (max-width: 768px) {
        .main-content.has-sidebar {
            margin-left: 0;
        }
        
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s;
        }
        
        .sidebar.show {
            transform: translateX(0);
        }
    }
</style>

