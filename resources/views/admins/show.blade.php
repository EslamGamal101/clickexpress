@extends('layouts.admin')

@section('page-title', 'تفاصيل الطلب #' . $order->id)

@section('content')
<div class="container-fluid">
    {{-- 🔹 العنوان --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-dark-blue fw-bold">
            <i class="fas fa-box me-2"></i> تفاصيل الطلب <span class="text-primary">#{{ $order->id }}</span>
        </h3>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> العودة للقائمة
        </a>
    </div>

    {{-- رسائل النجاح / الخطأ --}}
    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>⚠️ {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- 🔹 تفاصيل الطلب --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="fas fa-info-circle me-2"></i> معلومات عامة
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.orders.update', $order->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    {{-- المستخدم --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted">اسم المستخدم</label>
                        <input type="text" class="form-control" value="{{ $order->user->profile->first_name ?? 'غير محدد' }}" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted">هاتف المستخدم</label>
                        <input type="text" class="form-control" value="{{ $order->user->phone ?? 'غير محدد' }}" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted">نوع المستخدم</label>
                        <input type="text" class="form-control" value="{{ $order->user->type ?? 'شخصي' }}" disabled>
                    </div>

                    {{-- السائق --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted">اسم السائق</label>
                        <input type="text" class="form-control" value="{{ $order->driver->profile->first_name ?? 'غير محدد' }}" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted">هاتف السائق</label>
                        <input type="text" class="form-control" value="{{ $order->driver->phone ?? 'غير محدد' }}" disabled>
                    </div>

                    {{-- الحالة والسعر --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ $order->status=='pending'?'selected':'' }}>بانتظار التنفيذ</option>
                            <option value="in_progress" {{ $order->status=='in_progress'?'selected':'' }}>قيد التنفيذ</option>
                            <option value="completed" {{ $order->status=='completed'?'selected':'' }}>مكتمل</option>
                            <option value="cancelled" {{ $order->status=='cancelled'?'selected':'' }}>ملغي</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted">سعر الطلب</label>
                        <input type="number" name="price" class="form-control" value="{{ $order->price }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted">أجرة التوصيل</label>
                        <input type="number" name="delivery_fee" class="form-control" value="{{ $order->delivery_fee }}">
                    </div>

                    {{-- ملاحظات --}}
                    <div class="col-12">
                        <label class="form-label fw-bold text-muted">ملاحظات</label>
                        <textarea name="notes" rows="3" class="form-control">{{ $order->notes }}</textarea>
                    </div>
                </div>

                {{-- 🔹 عناوين الطلب --}}
                <hr class="my-4">
                <h5 class="fw-bold text-dark-blue"><i class="fas fa-map-marker-alt me-2 text-danger"></i> عناوين الالتقاط والتوصيل</h5>
                <div class="row g-3 mt-1">
                    {{-- مكان الالتقاط --}}
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold">مدينة الالتقاط</label>
                        <input type="text" name="pickup_city" class="form-control" value="{{ $order->address->pickup_city ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold">منطقة الالتقاط</label>
                        <input type="text" name="pickup_area" class="form-control" value="{{ $order->address->pickup_area ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold">اسم المرسل</label>
                        <input type="text" name="pickup_name" class="form-control" value="{{ $order->address->pickup_name ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold">هاتف المرسل</label>
                        <input type="text" name="pickup_phone" class="form-control" value="{{ $order->address->pickup_phone ?? '' }}">
                    </div>

                    {{-- مكان التوصيل --}}
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold">مدينة التوصيل</label>
                        <input type="text" name="delivery_city" class="form-control" value="{{ $order->address->delivery_city ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold">منطقة التوصيل</label>
                        <input type="text" name="delivery_area" class="form-control" value="{{ $order->address->delivery_area ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold">اسم المستلم</label>
                        <input type="text" name="delivery_name" class="form-control" value="{{ $order->address->delivery_name ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold">هاتف المستلم</label>
                        <input type="text" name="delivery_phone" class="form-control" value="{{ $order->address->delivery_phone ?? '' }}">
                    </div>
                </div>

                {{-- 🔹 أزرار التحكم --}}
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="fas fa-save me-1"></i> حفظ التعديلات
                    </button>

                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary px-4 fw-bold">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- 🔹 معلومات إضافية --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light fw-bold"><i class="fas fa-clock me-2"></i> تفاصيل الوقت والتقييم</div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="text-muted">تاريخ الإنشاء</div>
                    <div class="fw-bold">{{ $order->created_at->format('Y/m/d H:i') }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">تاريخ التحديث</div>
                    <div class="fw-bold">{{ $order->updated_at->format('Y/m/d H:i') }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">تقييم المستخدم</div>
                    <div class="fw-bold text-warning">
                        @if($order->rating)
                        ⭐ {{ $order->rating->value }}/5
                        @else
                        <span class="text-muted">لم يتم التقييم بعد</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">رمز التتبع</div>
                    <div class="fw-bold text-primary">{{ $order->tracking_code ?? 'غير متوفر' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection