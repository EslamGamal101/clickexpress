@extends('layouts.admin')

@section('page-title', 'إدارة العروض والخصومات')

@section('content')
<style>
    /* 1. خطوط وألوان احترافية */
    body {
        background-color: #f4f7fa;
        /* خلفية فاتحة ونظيفة */
        font-family: 'Cairo', sans-serif;
    }

    :root {
        --primary-color: #007bff;
        /* الأزرق الأساسي */
        --primary-hover: #0056b3;
        --secondary-bg: #ffffff;
        /* خلفية البطاقات */
        --border-color: #e9ecef;
        --header-color: #343a40;
        /* لون العناوين الداكن */
    }

    /* 2. تحسين تصميم البطاقات والظلال */
    .card.shadow-sm {
        border: none;
        /* إزالة الحدود */
        border-radius: 12px;
        /* زوايا مستديرة أكثر */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        /* ظل أنعم وأعمق قليلاً */
    }

    /* 3. تنسيق العناوين والأزرار */
    .d-flex h4 {
        color: var(--header-color);
        font-size: 1.5rem;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        border-radius: 8px;
        transition: background-color 0.3s;
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
    }

    .btn-outline-primary {
        border-radius: 8px;
        transition: all 0.3s;
    }

    /* 4. تحسين حقول البحث */
    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        transition: border-color 0.3s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
    }

    /* 5. تنسيق الجدول الاحترافي */
    .table-striped>tbody>tr:nth-of-type(odd)>* {
        background-color: #fcfcfc;
        /* لون افتراضي أخف لصفحة بيضاء */
    }

    .table thead th {
        background-color: var(--primary-color);
        /* رأس جدول بلون متميز */
        color: white;
        font-weight: 600;
        border-bottom: none;
        padding: 1rem 0.75rem;
    }

    .table td {
        padding: 1rem 0.75rem;
        /* مساحة أكبر لخلايا الجدول */
        vertical-align: middle;
        font-size: 0.95rem;
    }

    .btn-group .btn {
        border-radius: 6px !important;
        /* زوايا مستديرة للأزرار الصغيرة */
        margin-left: 5px;
    }

    .btn-group form {
        display: inline;
    }
</style>

<div class="container-fluid">
    {{-- رسائل التنبيه (Success/Error Messages) --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- 🏷️ العنوان وزر الإضافة --}}
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h4 class="fw-bold mb-0">
            <i class="fas fa-tags text-primary me-2"></i> إدارة العروض والخصومات
        </h4>
        <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus-circle me-1"></i> إضافة خصم جديد
        </a>
    </div>

    {{-- 🔍 أدوات البحث والفلترة - داخل بطاقة منفصلة --}}
    <div class="card shadow-sm mb-5 p-4">
        <h6 class="fw-bold text-secondary mb-3"><i class="fas fa-filter me-1"></i> فلاتر البحث</h6>
        <form method="GET" action="{{ route('admin.discounts.index') }}">
            <div class="row g-4 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-normal">اسم المسؤول / البريد</label>
                    <input type="text" name="admin_query" class="form-control" placeholder="بحث بالاسم أو البريد" value="{{ request('admin_query') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-normal">اسم السائق</label>
                    <input type="text" name="driver_query" class="form-control" placeholder="بحث باسم السائق" value="{{ request('driver_query') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-normal">المناسبة</label>
                    <select name="occasion" class="form-select">
                        <option value="">جميع المناسبات</option>
                        {{-- تم تمرير المناسبة المختارة مسبقاً للحفاظ على حالة الفلتر --}}
                        @php $selected_occasion = request('occasion'); @endphp
                        <option value="رمضان" {{ $selected_occasion == 'رمضان' ? 'selected' : '' }}>رمضان</option>
                        <option value="عيد الأضحى" {{ $selected_occasion == 'عيد الأضحى' ? 'selected' : '' }}>عيد الأضحى</option>
                        <option value="عيد الأم" {{ $selected_occasion == 'عيد الأم' ? 'selected' : '' }}>عيد الأم</option>
                        <option value="عيد العمال" {{ $selected_occasion == 'عيد العمال' ? 'selected' : '' }}>عيد العمال</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-normal">تاريخ الإنشاء</label>
                    <input type="date" name="created_at" class="form-control" value="{{ request('created_at') }}">
                </div>

                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">
                        <i class="fas fa-search me-1"></i> بحث وتصفية
                    </button>
                    {{-- زر لمسح الفلاتر --}}
                    @if (request()->hasAny(['admin_query', 'driver_query', 'occasion', 'created_at']))
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-outline-secondary px-4 me-2">
                        <i class="fas fa-redo-alt me-1"></i> مسح الفلاتر
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- 📋 جدول الخصومات - داخل بطاقة --}}
    <div class="card shadow-sm">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>#</th>
                        <th>المسؤول</th>
                        <th>نوع الخصم</th>
                        <th>قيمة الخصم</th>
                        <th>المستهدف</th>
                        <th>المناسبة</th>
                        <th>مدة الصلاحية</th>
                        <th>تاريخ الإنشاء</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($discounts as $discount)
                    <tr>
                        <td>{{ $discount->id }}</td>
                        <td>
                            {{ optional($discount->admin->profile)->first_name }} {{ optional($discount->admin->profile)->last_name }}
                        </td>
                        <td>
                            @if($discount->discount_type === 'percentage')
                            <span class="badge bg-success">نسبة</span>
                            @else
                            <span class="badge bg-info">مبلغ ثابت</span>
                            @endif
                        </td>
                        <td class="fw-bold text-primary">
                            @if($discount->discount_type === 'percentage')
                            {{ $discount->value }}%
                            @else
                            {{ $discount->value }} د.أ
                            @endif
                        </td>
                        <td>
                            @switch($discount->target_type)
                            @case('all_drivers') <span class="badge bg-secondary">جميع السائقين</span> @break
                            @case('specific_driver') <span class="badge bg-warning text-dark">{{ optional($discount->driver->profile)->first_name }}</span> @break
                            @case('specific_package') <span class="badge bg-primary">{{ optional($discount->package)->name }}</span> @break
                            @default -
                            @endswitch
                        </td>
                        <td>{{ $discount->occasion ?? '-' }}</td>
                        <td>
                            <span class="text-success">{{ $discount->start_date->format('Y-m-d') }}</span>
                            <i class="fas fa-arrow-right mx-1 text-muted"></i>
                            <span class="text-danger">{{ $discount->end_date->format('Y-m-d') }}</span>
                        </td>
                        <td class="text-muted">{{ $discount->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                               
                                <form method="POST" action="{{ route('admin.discounts.destroy', $discount->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الخصم؟ لا يمكن التراجع عن هذا الإجراء!');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="حذف"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="fas fa-frown me-1"></i> لا توجد عروض أو خصومات مطابقة لمعايير البحث حالياً.
                            @if (request()->hasAny(['admin_query', 'driver_query', 'occasion', 'created_at']))
                            <a href="{{ route('admin.discounts.index') }}" class="d-block mt-2 text-primary fw-bold">إزالة فلاتر البحث</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- يمكنك إضافة شريط الترقيم (Pagination) هنا إذا كنت تستخدمه في الـ Controller --}}
            {{-- @if ($discounts->hasPages())
                <div class="card-footer py-3">
                    {{ $discounts->links() }}
        </div>
        @endif --}}

    </div>
</div>
</div>
@endsection