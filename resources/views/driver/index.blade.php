@extends('layouts.admin')

@section('page-title', 'إدارة السائقين')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4 text-dark-blue"><i class="fas fa-motorcycle me-2"></i> لوحة إدارة السائقين</h3>

    {{-- رسائل التنبيهات --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- بطاقات الإحصائيات (نفترض أنك تمرر $totalDrivers, $activeDrivers, $pendingDrivers) --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card stat-card border-0 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <i class="fas fa-truck-moving fa-2x stat-icon text-primary"></i>
                        </div>
                        <div class="col">
                            <div class="stat-label text-uppercase mb-1">إجمالي السائقين</div>
                            <div class="stat-number h5 mb-0">{{ number_format($totalDrivers ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card stat-card border-0 py-2" style="background: linear-gradient(45deg, var(--accent-teal) 0%, #009c7a 100%);">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x stat-icon text-white"></i>
                        </div>
                        <div class="col text-white">
                            <div class="stat-label text-uppercase mb-1">سائقون بانتظار الموافقة</div>
                            <div class="stat-number h5 mb-0">{{ number_format($pendingDrivers ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card stat-card border-0 py-2" style="background: linear-gradient(45deg, #dc3545 0%, #bd2130 100%);">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <i class="fas fa-user-slash fa-2x stat-icon text-white"></i>
                        </div>
                        <div class="col text-white">
                            <div class="stat-label text-uppercase mb-1">سائقون محظورون</div>
                            <div class="stat-number h5 mb-0">{{ number_format($bannedDrivers ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- محرك البحث وأدوات الفلترة --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-dark-blue">خيارات البحث والفلترة المتقدمة</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.drivers.index') }}" class="row g-3 align-items-end">
                {{-- البحث العام (الاسم / رقم الهاتف / ترميز السيارة) --}}
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-muted">البحث العام</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="اسم، هاتف، أو لوحة سيارة">
                </div>

                {{-- الفلترة حسب المدينة --}}
                <div class="col-lg-2 col-md-6">
                    <label class="form-label text-muted">المدينة</label>
                    <select name="location" class="form-select">
                        <option value="">كل المدن</option>
                        <option value="عمان" {{ request('location')=='عمان'?'selected':'' }}>عمان</option>
                        <option value="إربد" {{ request('location')=='إربد'?'selected':'' }}>إربد</option>
                        {{-- ... أضف مدن أخرى هنا ... --}}
                    </select>
                </div>

                {{-- الفلترة حسب الحالة --}}
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-muted">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">كل الحالات</option>
                        <option value="active" {{ request('status')=='active'?'selected':'' }}>نشط</option>
                        <option value="pending" {{ request('status')=='pending'?'selected':'' }}>بانتظار الموافقة</option>
                        <option value="banned" {{ request('status')=='banned'?'selected':'' }}>محظور</option>
                    </select>
                </div>

                {{-- الفلترة حسب عدد الرحلات --}}
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-muted">عدد الطلبات</label>
                    <select name="orders_count" class="form-select">
                        <option value="">الكل</option>
                        <option value="low" {{ request('orders_count')=='low'?'selected':'' }}>أقل من 100 طلب</option>
                        <option value="high" {{ request('orders_count')=='high'?'selected':'' }}>أكثر من 100 طلب</option>
                        <option value="very_high" {{ request('orders_count')=='very_high'?'selected':'' }}>أكثر من 1000 طلب</option>
                    </select>
                </div>

                {{-- الفلترة حسب الاشتراك (افتراضية) --}}
                <div class="col-lg-1 col-md-4">
                    <label class="form-label text-muted">الاشتراك</label>
                    <select name="subscription" class="form-select">
                        <option value="">الكل</option>
                        <option value="active" {{ request('subscription')=='active'?'selected':'' }}>فعال</option>
                        <option value="expired" {{ request('subscription')=='expired'?'selected':'' }}>منتهي</option>
                    </select>
                </div>

                {{-- أزرار الإجراء --}}
                <div class="col-lg-2 col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-50" title="تطبيق الفلاتر">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary w-50" title="إعادة تعيين">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- جدول بيانات السائقين --}}
    <div class="card shadow-lg">
        {{-- رأس البطاقة مع زر الإضافة (تم استخدام bg-secondary لتماشي مع المثال السابق) --}}
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0">
                <i class="fas fa-clipboard-list me-2"></i> قائمة السائقين المسجلين
            </h5>
            <a href="{{ route('admin.drivers.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-user-plus me-1"></i> إضافة سائق
            </a>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-hover table-striped align-middle text-center">
                <thead class="bg-light text-muted">
                    <tr>
                        <th>#ID</th>
                        <th>اسم السائق</th>
                        <th>الرقم الوطني</th>
                        <th>رقم الهاتف</th>
                        <th>المدينة</th>
                        <th>تاريخ التسجيل</th>
                        <th>عدد الطلبات</th>
                        <th>لوحة المركبة</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drivers as $driver)
                    @php
                    // تهيئة بيانات السائق
                    $profile = $driver->profile;
                    $ordersCount = $driver->orders_count ?? optional($driver->orders)->count() ?? 0;
                    $statusClass = $driver->is_active ? 'success' : 'danger';
                    $statusText = $driver->is_active ? 'نشط' : 'محظور';
                    if (!$driver->is_active) {
                    $statusClass = 'warning';
                    $statusText = 'بانتظار الموافقة';
                    }
                    @endphp
                    <tr>
                        <td class="fw-bold text-primary">{{ $driver->id }}</td>
                        <td class="text-nowrap">{{ optional($profile)->first_name }} {{ optional($profile)->last_name }}</td>
                        <td>{{ optional($profile)->national_id ?? '-' }}</td>
                        <td>{{ $driver->phone }}</td>
                        <td>{{ optional($profile)->city ?? '-' }}</td>
                        <td dir="ltr" class="small">{{ $driver->created_at->format('Y/m/d') }}</td>
                        <td class="fw-bold">{{ $ordersCount }}</td>
                        <td><span class="badge bg-secondary">{{ optional($profile)->vehicle_plate ?? 'غير محدد' }}</span></td>
                        <td>
                            <span class="badge bg-{{ $statusClass }}"><i class="fas fa-circle me-1"></i> {{ $statusText }}</span>
                        </td>

                        {{-- قائمة الإجراءات --}}
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    الإدارة
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" style="z-index: 1001;">
                                    <li><a class="dropdown-item" href="{{ route('admin.drivers.show', $driver->id) }}"><i class="fas fa-eye me-2 text-info"></i> عرض التفاصيل</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.drivers.edit', $driver->id) }}"><i class="fas fa-edit me-2 text-warning"></i> تعديل البيانات</a></li>
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#notificationModal-{{ $driver->id }}"><i class="fas fa-bell me-2 text-primary"></i> إرسال إشعار</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>

                                    {{-- حظر/فك حظر --}}
                                    <li>
                                        <form action="{{ route('admin.drivers.toggle_ban', $driver->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            @if($driver->is_active)
                                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-user-slash me-2"></i> حظر السائق</button>
                                            @else
                                            <button type="submit" class="dropdown-item text-success"><i class="fas fa-user-check me-2"></i> تفعيل السائق</button>
                                            @endif
                                        </form>
                                    </li>
                                    {{-- حذف الحساب --}}
                                    <li>
                                        <form action="{{ route('admin.drivers.destroy', $driver->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('تأكيد الحذف: سيتم حذف حساب السائق نهائياً، هل أنت متأكد؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash-alt me-2"></i> حذف الحساب</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>

                            {{-- Modal لـ إرسال إشعار --}}
                            {{-- يفضل وضعه في مكان منفصل وإظهاره بـ JS لتجنب التكرار في كل صف --}}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-4 text-center">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            **{{ $noResultsMessage ?? 'لا توجد بيانات للسائقين لعرضها.' }}**
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- الترقيم (Pagination) --}}
        <div class="card-footer d-flex justify-content-center">
            {{ $drivers->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection