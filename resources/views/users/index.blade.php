@extends('layouts.admin')

@section('page-title', 'إدارة المستخدمين')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4 text-dark-blue">لوحة إدارة المستخدمين</h3>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card stat-card border-0 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x stat-icon"></i>
                        </div>
                        <div class="col">
                            <div class="stat-label text-uppercase mb-1">إجمالي المستخدمين</div>
                            <div class="stat-number h5 mb-0">{{ number_format($totalUsers ?? 0) }}</div>
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
                            <i class="fas fa-user-check fa-2x stat-icon"></i>
                        </div>
                        <div class="col">
                            <div class="stat-label text-uppercase mb-1">المستخدمون النشطون</div>
                            <div class="stat-number h5 mb-0">{{ number_format($activeUsers ?? 0) }}</div>
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
                            <i class="fas fa-user-slash fa-2x stat-icon"></i>
                        </div>
                        <div class="col">
                            <div class="stat-label text-uppercase mb-1">المستخدمون المحظورون</div>
                            <div class="stat-number h5 mb-0">{{ number_format($bannedUsers ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">

            <h6 class="m-0 fw-bold text-dark-blue">خيارات البحث والفلترة المتقدمة</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-muted">الاسم أو الهاتف</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="ابحث بالاسم أو رقم الهاتف">
                </div>

                <div class="col-lg-4 col-md-6">
                    <label class="form-label text-muted">تاريخ التسجيل</label>
                    <div class="input-group">
                        <input type="date" name="registered_from" value="{{ request('registered_from') }}"
                            class="form-control" title="من تاريخ">
                        <span class="input-group-text">إلى</span>
                        <input type="date" name="registered_to" value="{{ request('registered_to') }}"
                            class="form-control" title="إلى تاريخ">
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-muted">المدينة</label>
                    <select name="city" class="form-select">
                        <option value="">كل المدن</option>
                        <option value="عمان" {{ request('city')=='عمان'?'selected':'' }}>عمان</option>
                        <option value="إربد" {{ request('city')=='إربد'?'selected':'' }}>إربد</option>
                        <option value="الزرقاء" {{ request('city')=='الزرقاء'?'selected':'' }}>الزرقاء</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-muted">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">كل الحالات</option>
                        <option value="active" {{ request('status')=='active'?'selected':'' }}>نشط</option>
                        <option value="banned" {{ request('status')=='banned'?'selected':'' }}>محظور</option>
                    </select>
                </div>

                <div class="col-lg-1 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100" title="تطبيق الفلاتر">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100" title="إعادة تعيين">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-lg">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0">
                <i class="fas fa-clipboard-list me-2"></i> بيانات المستخدمين المسجلين
            </h5>
            <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-user-plus me-1"></i> إضافة مستخدم
            </a>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-hover table-striped align-middle text-center">
                <thead class="bg-light text-muted">
                    <tr>
                        <th>#ID</th>
                        <th>الاسم الكامل</th>
                        <th>نوع المستخدم</th>
                        <th>رقم الهاتف</th>
                        <th>الحالة</th>
                        <th>تاريخ التسجيل</th>
                        <th>عدد الطلبات</th>
                        <th>المدينة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="fw-bold text-primary">{{ $user->id }}</td>
                        <td class="text-nowrap">{{ optional($user->profile)->first_name }} {{ optional($user->profile)->last_name }}</td>
                        <td>
                            @switch($user->type)
                            @case('customer')
                            <span class="badge bg-info"><i class="fas fa-user me-1"></i> عميل</span>
                            @break
                            @case('vendor')
                            <span class="badge bg-warning text-dark"><i class="fas fa-store me-1"></i> متجر</span>
                            @break
                            @case('admin')
                            <span class="badge bg-dark"><i class="fas fa-user-shield me-1"></i> مدير</span>
                            @break
                            @case('management_producers')
                            <span class="badge bg-success"><i class="fas fa-cogs me-1"></i> منتج إدارة</span>
                            @break
                            @default
                            <span class="badge bg-secondary">غير محدد</span>
                            @endswitch
                        </td>
                        <td>{{ $user->phone }}</td>
                        <td>
                            @if($user->is_active)
                            <span class="badge bg-success"><i class="fas fa-circle me-1"></i> نشط</span>
                            @else
                            <span class="badge bg-danger"><i class="fas fa-ban me-1"></i> محظور</span>
                            @endif
                        </td>
                        <td dir="ltr" class="small">{{ $user->created_at->format('Y/m/d') }}</td>
                        <td class="fw-bold">{{ $user->orders->count() }}</td>
                        <td>{{ optional($user->profile)->city ?? '-' }}</td>

                        <td>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    الإدارة
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" style="z-index: 1001;">
                                    <li><a class="dropdown-item" href="{{ route('admin.users.show', $user->id) }}"><i class="fas fa-eye me-2 text-info"></i> عرض التفاصيل</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.users.edit', $user->id) }}"><i class="fas fa-edit me-2 text-warning"></i> تعديل البيانات</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    @if($user->is_active)
                                    <li>
                                        <form action="{{ route('admin.users.ban', $user->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-user-slash me-2"></i> حظر</button>
                                        </form>
                                    </li>
                                    @else
                                    <li>
                                        <form action="{{ route('admin.users.unban', $user->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="dropdown-item text-success"><i class="fas fa-user-check me-2"></i> فك الحظر</button>
                                        </form>
                                    </li>
                                    @endif
                                    <li>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('تأكيد الحذف: سيتم حذف الحساب نهائياً، هل أنت متأكد؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash-alt me-2"></i> حذف الحساب</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-4 text-center">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            **هذا المستخدم غير موجود** أو لا توجد نتائج مطابقة لمرشحات البحث.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-center">
            {{ $users->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection