@extends('layouts.admin')

@section('title', 'إنشاء طلب جديد')

@section('content')
<div class="container-fluid py-4">

    <!-- العنوان -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">
            <i class="fas fa-box-open text-primary me-2"></i> إنشاء طلب جديد
        </h4>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> رجوع
        </a>
    </div>
    @if(session('error'))
    <div class="alert alert-danger mt-2">
        {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <h6 class="mb-2 fw-bold">هناك أخطاء في الإدخال:</h6>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form action="{{ route('admin.orders.store') }}" method="POST" class="card border-0 shadow-sm">
        @csrf
        <div class="card-body p-4">
            <div class="row g-4">

                {{-- قسم معلومات المستخدم --}}
                <div class="col-12">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-user me-1"></i> معلومات المستخدم والسائق
                    </h6>
                </div>

                <!-- المستخدم -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">المستخدم <span class="text-danger">*</span></label>
                    <select name="user_id" class="form-select shadow-sm @error('user_id') is-invalid @enderror" required>
                        <option value="">اختر المستخدم</option>
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}">
                            {{ optional($user->profile)->first_name }} {{ optional($user->profile)->last_name }} - {{ $user->phone }}
                        </option>
                        @endforeach
                    </select>
                    @error('user_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- السائق -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">السائق</label>
                    <select name="driver_id" class="form-select shadow-sm @error('driver_id') is-invalid @enderror">
                        <option value="">بدون سائق حالياً</option>
                        @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                            {{ optional($driver)->profile->first_name ?? 'اسم غير متوفر' }} - {{ optional($driver)->phone ?? 'هاتف غير متوفر' }}
                        </option>
                        @endforeach

                    </select>
                    @error('driver_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- قسم تفاصيل الطلب --}}
                <div class="col-12">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mt-4 mb-3">
                        <i class="fas fa-info-circle me-1"></i> تفاصيل الطلب
                    </h6>
                </div>
                <!-- نوع الطلب (Order Type) -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">نوع الطلب <span class="text-danger">*</span></label>
                    <select name="order_type" class="form-select shadow-sm @error('order_type') is-invalid @enderror" required>
                        <option value="">اختر نوع الطلب</option>
                        <option value="package" {{ old('order_type') == 'package' ? 'selected' : '' }}>Package</option>
                        <option value="cargo" {{ old('order_type') == 'cargo' ? 'selected' : '' }}>Cargo</option>
                    </select>
                    @error('order_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- طريقة التوصيل (Delivery Type) -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">طريقة التوصيل <span class="text-danger">*</span></label>
                    <select name="delivery_type" class="form-select shadow-sm @error('delivery_type') is-invalid @enderror" required>
                        <option value="">اختر طريقة التوصيل</option>
                        <option value="instant" {{ old('delivery_type') == 'instant' ? 'selected' : '' }}>عادي</option>
                        <option value="scheduled" {{ old('delivery_type') == 'scheduled' ? 'selected' : '' }}>مجدول</option>
                    </select>
                    @error('delivery_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- تاريخ التوصيل -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">تاريخ التوصيل <span class="text-danger">*</span></label>
                    <input type="date" name="delivery_date" class="form-control shadow-sm @error('delivery_date') is-invalid @enderror" value="{{ old('delivery_date') }}" required>
                    @error('delivery_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- نوع الطرد (Package Type) -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">نوع الطرد <span class="text-danger">*</span></label>
                    <select name="package_type" class="form-select shadow-sm @error('package_type') is-invalid @enderror">
                        <option value="">اختر نوع الطرد (اختياري)</option>
                        <option value="document" {{ old('package_type') == 'document' ? 'selected' : '' }}>مستندات</option>
                        <option value="food" {{ old('package_type') == 'food' ? 'selected' : '' }}>طعام</option>
                        <option value="other" {{ old('package_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                    @error('package_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>




                <!-- ملاحظات -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">ملاحظات إضافية</label>
                    <textarea name="package_other" class="form-control shadow-sm @error('package_other') is-invalid @enderror" rows="2" placeholder="ملاحظات حول الطلب (اختياري)">{{ old('package_other') }}</textarea>
                    @error('package_other')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- قسم الأسعار --}}
                <div class="col-12">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mt-4 mb-3">
                        <i class="fas fa-coins me-1"></i> الأسعار والرسوم
                    </h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">السعر (د.أ) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control shadow-sm @error('price') is-invalid @enderror" step="0.01" value="{{ old('price') }}" required>
                    @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">رسوم التوصيل (د.أ)</label>
                    <input type="number" name="delivery_fee" class="form-control shadow-sm @error('delivery_fee') is-invalid @enderror" step="0.01" value="{{ old('delivery_fee') }}">
                    @error('delivery_fee')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- عنوان الالتقاط --}}
                {{-- عنوان الالتقاط --}}
                <div class="col-12">
                    <h6 class="fw-bold text-info border-bottom pb-2 mt-4 mb-3">
                        <i class="fas fa-map-marker-alt me-1"></i> عنوان الالتقاط
                    </h6>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">المدينة <span class="text-danger">*</span></label>
                    <input type="text" name="pickup_city" class="form-control shadow-sm @error('pickup_city') is-invalid @enderror" placeholder="المدينة" value="{{ old('pickup_city') }}" required>
                    @error('pickup_city')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">المنطقة <span class="text-danger">*</span></label>
                    <input type="text" name="pickup_area" class="form-control shadow-sm @error('pickup_area') is-invalid @enderror" placeholder="المنطقة" value="{{ old('pickup_area') }}" required>
                    @error('pickup_area')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">اسم المرسل <span class="text-danger">*</span></label>
                    <input type="text" name="pickup_name" class="form-control shadow-sm @error('pickup_name') is-invalid @enderror" placeholder="اسم المرسل" value="{{ old('pickup_name') }}" required>
                    @error('pickup_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">رقم الهاتف للاستلام <span class="text-danger">*</span></label>
                    <input type="text" name="pickup_phone" class="form-control shadow-sm @error('pickup_phone') is-invalid @enderror" placeholder="رقم الهاتف" value="{{ old('pickup_phone') }}" required>
                    @error('pickup_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- عنوان التوصيل --}}
                <div class="col-12">
                    <h6 class="fw-bold text-success border-bottom pb-2 mt-4 mb-3">
                        <i class="fas fa-location-arrow me-1"></i> عنوان التوصيل
                    </h6>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">المدينة <span class="text-danger">*</span></label>
                    <input type="text" name="delivery_city" class="form-control shadow-sm @error('delivery_city') is-invalid @enderror" placeholder="المدينة" value="{{ old('delivery_city') }}" required>
                    @error('delivery_city')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">المنطقة <span class="text-danger">*</span></label>
                    <input type="text" name="delivery_area" class="form-control shadow-sm @error('delivery_area') is-invalid @enderror" placeholder="المنطقة" value="{{ old('delivery_area') }}" required>
                    @error('delivery_area')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">اسم المستلم <span class="text-danger">*</span></label>
                    <input type="text" name="delivery_name" class="form-control shadow-sm @error('delivery_name') is-invalid @enderror" placeholder="اسم المستلم" value="{{ old('delivery_name') }}" required>
                    @error('delivery_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">رقم الهاتف للتسليم <span class="text-danger">*</span></label>
                    <input type="text" name="delivery_phone" class="form-control shadow-sm @error('delivery_phone') is-invalid @enderror" placeholder="رقم الهاتف" value="{{ old('delivery_phone') }}" required>
                    @error('delivery_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">وسيلة النقل <span class="text-danger">*</span></label>
                    <select name="vehicle_type" class="form-select shadow-sm @error('vehicle_type') is-invalid @enderror" required>
                        <option value="">اختر وسيلة النقل</option>
                        <option value="motorcycle" {{ old('vehicle_type') == 'motorcycle' ? 'selected' : '' }}>دراجة نارية</option>
                        <option value="car" {{ old('vehicle_type') == 'car' ? 'selected' : '' }}>سيارة</option>
                        <option value="van" {{ old('vehicle_type') == 'van' ? 'selected' : '' }}>شاحنة صغيرة</option>
                    </select>
                    @error('vehicle_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


            </div>
        </div>

        <!-- الأزرار -->
        <div class="card-footer bg-light text-end">
            <button type="submit" class="btn btn-primary px-4 fw-bold">
                <i class="fas fa-save me-1"></i> حفظ الطلب
            </button>
        </div>
    </form>
</div>
@endsection