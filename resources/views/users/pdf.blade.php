<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>بيانات المستخدم: {{ $user->id }}</title>
    <style>
        @page {
            header: page-header;
            footer: page-footer;
        }

        /* إزالة محاولة تضمين الخطوط المخصصة لأنها لا تعمل دون إعداد Dompdf */

        body {
            /* العودة لخط مدمج (DejaVu Sans) مع تأكيد RTL */
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            direction: rtl;
            text-align: right;
            margin: 20mm;
        }

        h1,
        h2,
        h3 {
            font-weight: bold;
            color: #333;
            margin-top: 15px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        /* تطبيق الاتجاه على خلايا الجدول بشكل صارم */
        .info-table td {
            padding: 8px;
            border: 1px solid #eee;
            direction: rtl;
            text-align: right;
        }

        .info-table td:first-child {
            width: 30%;
            background-color: #f7f7f7;
            font-weight: bold;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .orders-table th,
        .orders-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
            direction: rtl;
        }

        .orders-table th {
            background-color: #007bff;
            color: white;
            font-weight: normal;
        }

        .footer {
            text-align: center;
            font-size: 8pt;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>

<body lang="ar" dir="rtl">

    <div class="header">
        <h1>تقرير بيانات المستخدم</h1>
        <h2>{{ $user->name ?? optional($user->profile)->full_name ?? 'اسم غير متوفر' }}</h2>
    </div>

    <!-- بيانات المستخدم الأساسية -->
    <h3>1. البيانات الشخصية والحساب</h3>
    <table class="info-table">
        <tr>
            <td>رقم المستخدم (ID)</td>
            <td>{{ $user->id }}</td>
        </tr>
        <tr>
            <td>الاسم الكامل</td>
            <td>{{ optional($user->profile)->full_name ?? $user->name }}</td>
        </tr>
        <tr>
            <td>البريد الإلكتروني</td>
            <td>{{ $user->email ?? 'لا يوجد' }}</td>
        </tr>
        <tr>
            <td>رقم الهاتف</td>
            <td>{{ $user->phone }}</td>
        </tr>
        <tr>
            <td>تاريخ التسجيل</td>
            <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
        </tr>
        <tr>
            <td>حالة الحساب</td>
            <td>{{ $user->is_active ? 'مُفعّل' : 'مُعطل' }}</td>
        </tr>
    </table>

    <!-- بيانات الملف الشخصي الإضافية -->
    @if ($user->profile)
    <h3>2. تفاصيل الملف الشخصي</h3>
    <table class="info-table">
        <tr>
            <td>المدينة</td>
            <td>{{ $user->profile->city ?? 'غير محدد' }}</td>
        </tr>
        <tr>
            <td>العنوان</td>
            <td>{{ $user->profile->address ?? 'غير محدد' }}</td>
        </tr>
        <tr>
            <td>تاريخ الميلاد</td>
            <td>{{ $user->profile->date_of_birth ?? 'غير محدد' }}</td>
        </tr>
        <tr>
            <td>صورة الهوية مُوثقة</td>
            <td>{{ $user->profile->is_verified_id ? 'نعم' : 'لا' }}</td>
        </tr>
    </table>
    @endif

    <!-- سجل الطلبات -->
    <h3>3. سجل الطلبات ({{ $user->orders->count() }} طلب)</h3>
    @if($user->orders->isEmpty())
    <p>لم يقم هذا المستخدم بإنشاء أي طلبات حتى الآن.</p>
    @else
    <table class="orders-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 15%;">رقم الطلب</th>
                <th style="width: 25%;">تاريخ الإنشاء</th>
                <th style="width: 20%;">الإجمالي</th>
                <th style="width: 15%;">الحالة</th>
                <th style="width: 20%;">التقييم للسائق</th>
            </tr>
        </thead>
        <tbody>
            @foreach($user->orders as $index => $order)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $order->id }}</td>
                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ number_format($order->total_price, 2) }}</td>
                <td>{{ $order->status }}</td>
                <td>{{ optional($order->ratingForDriver)->score ? $order->ratingForDriver->score . ' نجوم' : 'لم يُقيَّم' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>تم إنشاء هذا التقرير في: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

</body>

</html>