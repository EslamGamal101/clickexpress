<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول المسؤول - دراي كلين إيلين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* الألوان الجديدة المستوحاة من Click Express (الأزرق والبرتقالي للعناصر البارزة) */
        :root {
            --primary-blue: #007bff;
            /* أزرق أساسي */
            --dark-blue: #0056b3;
            /* أزرق داكن للأزرار/الهوفر */
            --accent-orange: #ffc107;
            /* برتقالي للمسة مميزة */
            --bg-start: #e9f5ff;
            /* أزرق فاتح جداً للخلفية */
            --bg-end: #cce5ff;
            /* أزرق فاتح للخلفية */
        }

        body {
            background: linear-gradient(135deg, var(--bg-start) 0%, var(--bg-end) 100%);
            min-height: 100vh;
            font-family: 'Cairo', sans-serif;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
            margin: 20px;
            overflow: hidden;
            /* لضمان عدم خروج الهيدر عن الحواف */
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--dark-blue) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .logo-icon {
            width: 55px;
            height: 55px;
            background: var(--accent-orange);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #333;
        }

        .logo-text h2 {
            color: white;
            font-weight: 700;
            margin-bottom: 3px;
            font-size: 2rem;
        }

        .logo-text p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            font-weight: 400;
            margin-bottom: 0;
        }

        .arabic-text {
            color: var(--accent-orange);
            font-size: 1rem;
            font-weight: 700;
            margin-top: 5px;
        }

        .login-body {
            padding: 2rem;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 12px 15px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .input-group-text {
            border-radius: 8px 0 0 8px;
            background-color: #f8f9fa;
            color: var(--primary-blue);
        }

        .btn-login {
            background: var(--primary-blue);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-login:hover {
            background: var(--dark-blue);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
            transform: translateY(-1px);
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
            font-size: 0.9rem;
        }

        .password-requirements {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 5px;
        }

        .password-field {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            left: 10px;
            /* تم التعديل ليناسب اتجاه RTL */
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--primary-blue);
            cursor: pointer;
            padding: 0;
            z-index: 10;
        }

        .password-toggle:hover {
            color: var(--dark-blue);
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                    <div class="login-card">
                        <div class="login-header">
                            <div class="logo-container">
                                <div class="logo-icon">
                                    <i class="fas fa-tshirt"></i>
                                </div>
                                <div class="logo-text">
                                    <h2 class="mb-2">Admin Panel</h2>
                                    <p class="mb-1">clickexpress</p>
                                    <div class="arabic-text">لوحة تحكم clickexpress </div>
                                </div>
                            </div>
                        </div>
                        <div class="login-body">
                            {{-- عرض رسائل الأخطاء --}}
                            @if ($errors->any())
                            <div class="error-message">
                                <p class="mb-1 fw-bold">حدث خطأ في عملية تسجيل الدخول:</p>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <form method="POST" action="{{ route('admin.login') }}">
                                @csrf

                                {{-- حقل البريد الإلكتروني --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold" for="email">البريد الإلكتروني</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            id="email"
                                            name="email"
                                            placeholder="أدخل البريد الإلكتروني للمسؤول"
                                            value="{{ old('email') }}"
                                            required autofocus>
                                    </div>
                                    @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- حقل كلمة المرور --}}
                                <div class="mb-4">
                                    <label for="password" class="form-label fw-bold">كلمة المرور</label>
                                    <div class="input-group password-field">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            id="password"
                                            name="password"
                                            placeholder="أدخل كلمة المرور"
                                            required>
                                        <button type="button" class="password-toggle" onclick="togglePassword()">
                                            <i class="fas fa-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                    <div class="password-requirements">
                                        <small>لأغراض الأمان، يفضل أن تكون كلمة المرور:</small>
                                        <ul class="mb-0 mt-1 list-unstyled">
                                            <li><i class="fas fa-check-circle me-1"></i>حرف إنجليزي واحد على الأقل</li>
                                            <li><i class="fas fa-check-circle me-1"></i>رقم واحد على الأقل</li>
                                            <li><i class="fas fa-check-circle me-1"></i>6 أحرف على الأقل</li>
                                        </ul>
                                    </div>
                                    @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- حذف حقل user_type المخفي وزرار الاختيار لتبسيطها للـ Admin فقط --}}

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-login">
                                        <i class="fas fa-sign-in-alt me-2"></i>تسجيل دخول المسؤول
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // وظيفة عرض/إخفاء كلمة المرور (تم تحديث مكان الزر)
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // تم إزالة وظيفة selectUserType لعدم الحاجة إليها

        // التحقق من صحة كلمة المرور في الوقت الفعلي
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const hasLetter = /[a-zA-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasMinLength = password.length >= 6;

            // تغيير لون الحدود بناءً على المتطلبات
            if (hasLetter && hasNumber && hasMinLength) {
                this.style.borderColor = '#198754'; // لون أخضر للنجاح
            } else {
                this.style.borderColor = '#dc3545'; // لون أحمر للخطأ
            }
        });

        // التأكد من تطبيق التنسيق الصحيح عند تحميل الصفحة إذا كانت هناك أخطاء
        document.addEventListener('DOMContentLoaded', () => {
            // إزالة الألوان المحددة لنوع المستخدم لأننا ألغينا الاختيار
        });
    </script>
</body>

</html>