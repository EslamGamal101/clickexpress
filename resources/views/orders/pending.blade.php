@extends('layouts.admin')

@section('page-title', 'طلبات تسجيل السائقين المعلقة')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h3 class="text-dark-blue fw-light">
            <i class="fas fa-user-clock me-2"></i> طلبات التسجيل المعلقة
            <span class="badge bg-warning text-dark">{{ $pendingDrivers->total() }}</span>
        </h3>
        <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-motorcycle me-2"></i> قائمة السائقين المُعتمدين
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            @if($pendingDrivers->isEmpty())
            <div class="alert alert-info text-center">لا توجد طلبات تسجيل معلقة حالياً.</div>
            @else
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>الاسم الكامل</th>
                            <th>رقم الهاتف</th>
                            <th>تاريخ التسجيل</th>
                            <th>حالة التوثيق (الهوية)</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingDrivers as $driver)
                        <tr>
                            <td>
                                <a href="{{ route('admin.drivers.show', $driver->id) }}" class="fw-bold text-primary">
                                    {{ optional($driver->profile)->first_name ?? 'بدون اسم' }} {{ optional($driver->profile)->last_name ?? '' }}
                                </a>
                            </td>
                            <td dir="ltr">{{ $driver->phone }}</td>
                            <td>{{ $driver->created_at->format('Y-m-d H:i A') }}</td>
                            <td>
                                @if(optional($driver->profile)->is_verified_id)
                                <span class="badge bg-success"><i class="fas fa-check-circle"></i> مكتمل</span>
                                @else
                                <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle"></i> ينقصه التحقق</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.drivers.show', $driver->id) }}" class="btn btn-sm btn-info me-2" title="مراجعة البيانات">
                                    <i class="fas fa-eye"></i> مراجعة
                                </a>

                                {{-- نموذج الموافقة المباشرة --}}
                                <form action="{{ route('approve', $driver->id) }}" method="POST" class="d-inline" onsubmit="return confirm('تأكيد الموافقة على السائق؟ سيتم تفعيل حسابه.')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success" title="الموافقة">
                                        <i class="fas fa-check"></i> موافقة
                                    </button>
                                </form>

                                {{-- زر الرفض (يفتح مودال لإدخال السبب) --}}
                                <button type="button" class="btn btn-sm btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $driver->id }}" title="الرفض">
                                    <i class="fas fa-times"></i> رفض
                                </button>
                            </td>
                        </tr>

                        {{-- مودال الرفض لكل سائق --}}
                        <div class="modal fade" id="rejectModal-{{ $driver->id }}" tabindex="-1" aria-labelledby="rejectModalLabel-{{ $driver->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="rejectModalLabel-{{ $driver->id }}">رفض تسجيل السائق</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('reject', $driver->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-body">
                                            <p>هل أنت متأكد من رفض تسجيل السائق **{{ optional($driver->profile)->first_name }}**؟</p>
                                            <div class="mb-3">
                                                <label for="rejection_reason-{{ $driver->id }}" class="form-label">سبب الرفض (اختياري)</label>
                                                <textarea name="rejection_reason" id="rejection_reason-{{ $driver->id }}" class="form-control" rows="3" placeholder="اكتب سبباً واضحاً للرفض"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $pendingDrivers->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection