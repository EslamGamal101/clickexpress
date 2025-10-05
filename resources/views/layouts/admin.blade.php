<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة تحكم الموظف') - كليك إكسبرس</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            /* ألوان Click Express */
            --primary-blue: #0077c2;
            /* أزرق مشرق */
            --dark-blue: #003366;
            /* أزرق داكن للخلفيات */
            --accent-teal: #00c497;
            /* أخضر فيروزي/بحري للأزرار الثانوية */
            --text-light: #f8f9fa;
            /* نص فاتح للخلفيات الداكنة */
        }

        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        /* الشريط الجانبي (Sidebar) */
        .sidebar {
            background: linear-gradient(135deg, var(--dark-blue) 0%, #004d99 100%);
            min-height: 100vh;
            color: var(--text-light);
            position: fixed;
            top: 0;
            right: 0;
            width: 250px;
            z-index: 1000;
            transition: all 0.3s ease;
            transform: translateX(0);
        }

        /* قواعد تصغير الشريط الجانبي */
        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .logo-text,
        .sidebar.collapsed .arabic-text {
            display: none;
        }

        .sidebar.collapsed .logo-icon {
            margin: auto;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 12px 0;
        }

        .main-content.expanded {
            margin-right: 70px;
        }

        /* محاذاة الشعار */
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
            /* محاذاة الشعار باليمين بشكل صحيح */
            justify-content: flex-start;
        }

        .logo-icon {
            background: var(--accent-teal);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            /* منع الأيقونة من التصغير */
        }

        .logo-text h4 {
            color: white;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .logo-text small {
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
            letter-spacing: 1px;
        }

        .arabic-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8rem;
        }

        /* قائمة الشريط الجانبي (Menu) */
        .sidebar-menu {
            padding: 1rem 0;
        }

        .sidebar-menu .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            transition: all 0.3s ease;
            border-right: 4px solid transparent;
            display: flex;
            align-items: center;
            /* محاذاة النص والأيقونة لليمين */
            text-align: right;
        }

        .sidebar-menu .nav-link:hover,
        .sidebar-menu .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-right-color: var(--primary-blue);
        }

        .sidebar-menu .nav-link i {
            width: 20px;
            margin-left: 10px;
            margin-right: 0;
            /* مهم للتأكد من المحاذاة */
            flex-shrink: 0;
        }


        .main-content {
            margin-right: 250px;
            transition: all 0.3s ease;
        }


        /* شريط التنقل العلوي (Top Navbar) */
        .top-navbar {
            background: white;
            color: var(--dark-blue);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            margin-bottom: 2rem;
        }

        .top-navbar h5 {
            color: var(--dark-blue);
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--dark-blue) !important;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* الأيقونات في الـ Top Navbar */
        .top-navbar .btn-link {
            color: var(--dark-blue);
        }

        /* بطاقات الإحصائيات (Stat Cards) */
        .stat-card {
            background: linear-gradient(45deg, var(--primary-blue) 0%, #00aaff 100%);
            color: white;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 51, 102, 0.3);
            border: none;
            transition: transform 0.3s ease;
        }

        /* الاستجابة (Responsive Design) - تم دمجها بالكامل */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(100%);
                width: 280px;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-right: 0 !important;
                padding: 15px !important;
            }

            .navbar {
                margin-right: 0 !important;
            }

            .mobile-toggle {
                display: block !important;
            }

            .sidebar-header h4 {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 10px !important;
            }

            .navbar {
                padding: 8px 15px;
            }

            .sidebar {
                width: 100%;
            }
        }

        /* بقية الأنماط... */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .overlay.show {
            display: block;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--accent-teal);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: var(--primary-blue);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #005a9c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 119, 194, 0.3);
        }
    </style>
    @yield('styles')
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>
        // ... (Pusher code as before) ...
    </script>
</head>

<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <div class="logo-text">
                    <h4 class="mb-0">Click</h4>
                    <small>EXPRESS</small>
                    <div class="arabic-text">كليك إكسبرس</div>
                </div>
            </div>
        </div>
        <nav class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link " href="#">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="sidebar-text">لوحة القيادة</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/orders*') ? 'active' : '' }}"
                        href="{{ route('admin.orders.index') }}">
                        <i class="fas fa-clipboard-list"></i>
                        <span class="sidebar-text">إدارة الطلبات</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/drivers*') ? 'active' : '' }}"
                        href="{{ route('admin.drivers.index') }}">
                        <i class="fas fa-truck"></i>
                        <span class="sidebar-text">إدارة السائقين</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('pending*') ? 'active' : '' }}"
                        href="{{ route('pending') }}">
                        <i class="fas fa-user-clock"></i>
                        <span class="sidebar-text">طلبات تسجيل السائقين</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-chart-line"></i>
                        <span class="sidebar-text">التقارير والإحصائيات</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-cogs"></i>
                        <span class="sidebar-text">الإعدادات</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="main-content" id="mainContent">
        <div class="top-navbar">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link me-3" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button class="mobile-toggle me-3" id="mobileToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="mb-0">@yield('page-title', 'لوحة التحكم')</h5>
                </div>
                <div class="d-flex align-items-center">
                    <div class="dropdown position-relative me-3">
                        <button class="btn btn-link position-relative" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="padding:0; border:none;">
                            <i class="fas fa-bell fa-lg"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationDropdown" style="width:280px;">
                            <li class="dropdown-header fw-bold">الإشعارات</li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-start" href="#">
                                    <i class="fas fa-box text-primary me-2 mt-1"></i>
                                    <div>
                                        <div class="fw-semibold">طلب جديد #123</div><small class="text-muted">منذ 5 دقائق</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-start" href="#">
                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                    <div>
                                        <div class="fw-semibold">تم إنهاء مهمة</div><small class="text-muted">منذ ساعة</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-start" href="#">
                                    <i class="fas fa-user text-warning me-2 mt-1"></i>
                                    <div>
                                        <div class="fw-semibold">عميل جديد</div><small class="text-muted">اليوم</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-center text-primary fw-semibold" href="#">عرض الكل</a>
                            </li>
                        </ul>
                    </div>


                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown" style="color: var(--dark-blue);">
                            <i class="fas fa-user-circle me-2"></i>
                            eslam
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>الملف الشخصي</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-key me-2"></i>تغيير كلمة المرور</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="#" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>تسجيل الخروج
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-area">
            @yield('content')
        </div>
    </div>

    <div class="overlay" id="overlay"></div>
    <!-- Bootstrap Bundle (مع Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar Toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');

            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Mobile Toggle
        document.getElementById('mobileToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });

        // Close sidebar when clicking overlay
        document.getElementById('overlay').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    @yield('scripts')
</body>

</html>