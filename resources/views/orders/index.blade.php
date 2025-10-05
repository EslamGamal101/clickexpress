@extends('layouts.admin')

@section('page-title', 'إدارة الطلبات')

@section('content')

<div class="container-fluid">
    {{-- عنوان الصفحة --}}
    <h3 class="mb-4 text-dark-blue"><i class="fas fa-box me-2"></i> لوحة إدارة الطلبات</h3>

    {{-- تنبيهات النظام --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- محرك البحث وأدوات الفلترة --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="m-0 fw-bold text-dark-blue"><i class="fas fa-search me-2"></i> البحث والفلترة</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3 align-items-end">

                {{-- البحث عن المستخدم --}}
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-muted fw-bold">بحث المستخدم (اسم / هاتف)</label>
                    <input type="text" name="user_search" value="{{ request('user_search') }}" class="form-control"
                        placeholder="اسم المستخدم أو رقم الهاتف">
                </div>

                {{-- البحث عن السائق --}}
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-muted fw-bold">بحث السائق (اسم / هاتف)</label>
                    <input type="text" name="driver_search" value="{{ request('driver_search') }}" class="form-control"
                        placeholder="اسم السائق أو رقم الهاتف">
                </div>



                {{-- فلترة بالمدينة --}}
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-muted fw-bold">المدينة (التقاط أو توصيل)</label>
                    {{-- المدن يجب أن تتطابق مع ما هو مدخل في قاعدة البيانات --}}
                    <select name="city" class="form-select">
                        <option value="">كل المدن</option>
                        <option value="الرياض" {{ request('city')=='الرياض'?'selected':'' }}>الرياض</option>
                        <option value="جدة" {{ request('city')=='جدة'?'selected':'' }}>جدة</option>
                        <option value="الدمام" {{ request('city')=='الدمام'?'selected':'' }}>الدمام</option>
                        <option value="عمان" {{ request('city')=='عمان'?'selected':'' }}>عمان</option>
                        <option value="إربد" {{ request('city')=='إربد'?'selected':'' }}>إربد</option>
                        {{-- أضف المزيد من المدن حسب الحاجة --}}
                    </select>
                </div>

                {{-- أزرار الإجراء --}}
                <div class="col-lg-2 col-md-6 d-flex gap-2 mt-4 mt-md-0">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill fw-bold">
                        <i class="fas fa-filter me-1"></i> بحث
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm flex-fill fw-bold">
                        <i class="fas fa-redo me-1"></i> مسح
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- جدول الطلبات --}}
    <div class="card shadow-lg border-0">
        <div class="card-header bg-indigo text-dark d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2"></i> قائمة الطلبات</h5>
            {{-- افترضنا وجود مسار admin.orders.create لإنشاء طلب --}}
            <a href="{{ route('admin.orders.create') }}" class="btn btn-success btn-sm fw-bold"><i class="fas fa-plus me-1"></i> إنشاء طلب جديد</a>
        </div>

        <div class="card-body table-responsive">

            {{-- رسالة عدم وجود نتائج (من المتحكم) --}}
            @if(isset($noResultsMessage))
            <div class="alert alert-warning text-center fw-bold" role="alert">
                {{ $noResultsMessage }}
            </div>
            @endif

            <table class="table table-hover table-striped align-middle text-center">
                <thead class="bg-light text-muted fw-bold">
                    <tr>
                        <th>#</th>
                        <th>اسم المستخدم</th>
                        <th>هاتف المستخدم</th>
                        <th>المستقبل</th>
                        <th>هاتف المستقبل</th>
                        <th>السائق</th>
                        <th>هاتف السائق</th>
                        <th>المدينة</th>
                        <th>الحالة</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    @php
                    // دالة مساعدة لتحديد تلوين الحالة
                    $statusClass = match ($order->status) {
                    'completed' => 'success',
                    'cancelled', 'failed' => 'danger',
                    'pending', 'waiting' => 'info',
                    'in-progress', 'on-hold' => 'warning',
                    default => 'secondary',
                    };

                    // دالة مساعدة لجلب المدينة المعروضة (الأولوية لمدينة التوصيل)
                    $cityDisplay = optional($order->address)->delivery_city ?? optional($order->address)->pickup_city ?? 'غير محدد';

                    // جلب اسم المستقبل وهاتفه
                    $receiverName = optional($order->address)->delivery_name ?? '—';
                    $receiverPhone = optional($order->address)->delivery_phone ?? '—';

                    @endphp
                    <tr>
                        {{-- رقم الطلب --}}
                        <td class="fw-bold text-primary">{{ $order->id }}</td>

                        {{-- المستخدم (المنشئ) --}}
                        <td>{{ optional($order->user)->profile->first_name ?? '—' }}</td>
                        <td>{{ optional($order->user)->phone ?? '—' }}</td>

                        {{-- المستقبل (من OrderAddress) --}}
                        <td>{{ $receiverName }}</td>
                        <td>{{ $receiverPhone }}</td>

                        {{-- السائق --}}
                        <td>{{ optional($order->driver)->profile->first_name ?? '—' }}</td>
                        <td>{{ optional($order->driver)->phone ?? '—' }}</td>

                        {{-- المدينة --}}
                        <td>{{ $cityDisplay }}</td>

                        {{-- الحالة --}}
                        <td>
                            <span class="badge bg-{{ $statusClass }} text-uppercase py-2 px-3 fw-bold">
                                {{ $order->status ?? '-' }}
                            </span>
                        </td>

                        {{-- تاريخ الإنشاء --}}
                        <td>{{ $order->created_at ? $order->created_at->format('Y-m-d') : '—' }}</td>

                        {{-- الإجراءات --}}
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    إدارة
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    {{-- عرض التفاصيل --}}
                                    <li>
                                        {{-- افترضنا وجود مسار admin.orders.show --}}
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="dropdown-item text-info">
                                            <i class="fas fa-eye me-2"></i> عرض التفاصيل
                                        </a>
                                    </li>
                                    {{-- تعديل الطلب --}}
                                    <li>
                                        {{-- افترضنا وجود مسار admin.orders.edit --}}
                                        <a href="{{ route('admin.orders.edit', $order->id) }}" class="dropdown-item text-warning">
                                            <i class="fas fa-edit me-2"></i> تعديل الطلب
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    {{-- إلغاء الطلب --}}
                                    @if($order->status !== 'cancelled' && $order->status !== 'completed')
                                    <li>
                                        {{-- افترضنا وجود مسار admin.orders.cancel --}}
                                        <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء الطلب؟ هذا الإجراء لا يمكن التراجع عنه.')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-ban me-2"></i> إلغاء الطلب</button>
                                        </form>
                                    </li>
                                    @endif
                                    {{-- حذف الطلب --}}
                                    <li>
                                        {{-- افترضنا وجود مسار admin.orders.destroy --}}
                                        <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف الطلب نهائياً؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash-alt me-2"></i> حذف الطلب</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="py-4 text-center text-danger fw-bold">
                            <i class="fas fa-exclamation-triangle me-2"></i> لا توجد طلبات لعرضها حالياً.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ترقيم الصفحات (Pagination) --}}
        <div class="card-footer d-flex justify-content-center">
            {{ $orders->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
        </div>
    </div>

</div>

@endsection