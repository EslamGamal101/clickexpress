@php
// تحديد ما إذا كانت الصفحة للتعديل أم للإضافة بناءً على وجود كائن المستخدم
$isEdit = isset($user) && $user->id;
$pageTitle = $isEdit ? 'تعديل حساب المستخدم: ' . optional($user->profile)->first_name : 'إضافة مستخدم جديد';
$formAction = $isEdit ? route('admin.users.update', $user->id) : route('admin.users.store');
$method = $isEdit ? 'PUT' : 'POST';

// استرجاع البيانات القديمة أو تعيين قيم افتراضية
$profile = $user->profile ?? new \App\Models\Profile();
@endphp

@extends('layouts.admin')

@section('page-title', $pageTitle)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h3 class="text-dark-blue fw-light">
            <i class="fas fa-{{ $isEdit ? 'user-edit' : 'user-plus' }} me-2"></i> {{ $pageTitle }}
        </h3>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-right me-2"></i> العودة لقائمة المستخدمين
        </a>
    </div>

    {{-- رسائل التنبيهات (مثل النجاح/الخطأ) --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">يرجي مراجعه الحقول </div>
    @endif

    {{-- نموذج الإضافة/التعديل --}}


    <div class="card shadow-lg">
       

        <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method($method)

            <div class="card-body">
                <div class="row">

                    {{-- =========================================== --}}
                    {{-- 🛑 القسم الأول: بيانات الحساب الأساسية --}}
                    {{-- =========================================== --}}
                    <div class="col-md-6 mb-4">
                        <div class="border-start border-5 border-primary p-3 bg-light rounded">
                            <h5 class="text-primary mb-3"><i class="fas fa-lock me-2"></i> بيانات تسجيل الدخول</h5>

                            {{-- رقم الهاتف (مفتاح الحساب) --}}
                            <div class="mb-3">
                                <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $user->phone ?? '') }}" required placeholder="مثال: 079xxxxxxx" dir="ltr">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- كلمة المرور (تظهر فقط في الإضافة) --}}
                            @if(!$isEdit)
                            <div class="mb-3">
                                <label for="password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                                    required placeholder="الرجاء إدخال كلمة مرور قوية">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            @else
                            {{-- خيار تغيير كلمة المرور في التعديل --}}
                            <p class="text-muted small">اترك حقل كلمة المرور فارغاً إذا كنت لا ترغب في تغييرها.</p>
                            <div class="mb-3">
                                <label for="password" class="form-label">كلمة المرور الجديدة</label>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                                    placeholder="أدخل كلمة مرور جديدة">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            @endif

                            {{-- البريد الإلكتروني (اختياري) --}}
                            <div class="mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email ?? '') }}" placeholder="example@domain.com">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- حالة الحساب ونوعه --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="type" class="form-label">نوع المستخدم <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="customer" {{ old('type', $user->type ?? '') == 'customer' ? 'selected' : '' }}>عميل (Customer)</option>
                                        <option value="driver" {{ old('type', $user->type ?? '') == 'driver' ? 'selected' : '' }}>سائق توصيل (Driver)</option>
                                        <option value="vendor" {{ old('type', $user->type ?? '') == 'vendor' ? 'selected' : '' }}>متجر (Vendor)</option>
                                        <option value="admin" {{ old('type', $user->type ?? '') == 'admin' ? 'selected' : '' }}>إدارة عليا (Admin)</option>
                                        <option value="management_producers" {{ old('type', $user->type ?? '') == 'management_producers' ? 'selected' : '' }}>منتج إدارة (Management)</option>
                                    </select>
                                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="is_active" class="form-label">حالة الحساب <span class="text-danger">*</span></label>
                                    <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                        <option value="1" {{ old('is_active', $user->is_active ?? 1) == 1 ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ old('is_active', $user->is_active ?? 0) == 0 ? 'selected' : '' }}>محظور</option>
                                    </select>
                                    @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- =========================================== --}}
                    {{-- 📋 القسم الثاني: بيانات الملف الشخصي (Profile) --}}
                    {{-- =========================================== --}}
                    <div class="col-md-6 mb-4">
                        <div class="border-start border-5 border-info p-3 bg-light rounded">
                            <h5 class="text-info mb-3"><i class="fas fa-address-card me-2"></i> البيانات الشخصية والعنوان</h5>

                            <div class="row">
                                {{-- الاسم الأول --}}
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                        value="{{ old('first_name', $profile->first_name ?? '') }}" required placeholder="الاسم الأول">
                                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- اسم العائلة --}}
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">اسم العائلة <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                        value="{{ old('last_name', $profile->last_name ?? '') }}" required placeholder="اسم العائلة">
                                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- الرقم الوطني --}}
                            <div class="mb-3">
                                <label for="national_id" class="form-label">الرقم الوطني / رقم السجل التجاري</label>
                                <input type="text" name="national_id" id="national_id" class="form-control @error('national_id') is-invalid @enderror"
                                    value="{{ old('national_id', $profile->national_id ?? '') }}" placeholder="أرقام فقط" dir="ltr">
                                @error('national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="row">
                                {{-- المدينة --}}
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">المدينة</label>
                                    <select name="city" id="city" class="form-select @error('city') is-invalid @enderror">
                                        <option value="">اختر المدينة</option>
                                        {{-- يجب تمرير المدن من الكنترولر، أو استخدامها مباشرة --}}
                                        @foreach(['عمان', 'إربد', 'الزرقاء', 'العقبة'] as $city)
                                        <option value="{{ $city }}" {{ old('city', $profile->city ?? '') == $city ? 'selected' : '' }}>{{ $city }}</option>
                                        @endforeach
                                    </select>
                                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- المنطقة / الحي --}}
                                <div class="col-md-6 mb-3">
                                    <label for="area" class="form-label">المنطقة / الحي</label>
                                    <input type="text" name="area" id="area" class="form-control @error('area') is-invalid @enderror"
                                        value="{{ old('area', $profile->area ?? '') }}" placeholder="المنطقة أو الحي">
                                    @error('area')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- صورة الملف الشخصي --}}
                            <div class="mb-3">
                                <label for="profile_image" class="form-label">صورة الملف الشخصي</label>
                                <input type="file" name="profile_image" id="profile_image" class="form-control @error('profile_image') is-invalid @enderror" accept="image/*">
                                <small class="text-muted">صيغ مسموحة: jpeg, png, jpg</small>
                                @error('profile_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if($isEdit && $profile->profile_image)
                                <div class="mt-2">
                                    <p class="small text-muted mb-1">الصورة الحالية:</p>
                                    <img src="{{ asset('storage/' . $profile->profile_image) }}" alt="صورة الملف الشخصي" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- =========================================== --}}
                    {{-- 🚚 القسم الثالث: بيانات المتجر/المركبة (مشروطة) --}}
                    {{-- =========================================== --}}
                    <div class="col-12">
                        <div class="border-start border-5 border-warning p-3 bg-light rounded" id="vendor-fields">
                            <h5 class="text-warning mb-3"><i class="fas fa-store me-2"></i> بيانات إضافية (للمتاجر/السائقين)</h5>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="vendor_name" class="form-label">اسم المتجر / الشركة</label>
                                    <input type="text" name="vendor_name" id="vendor_name" class="form-control @error('vendor_name') is-invalid @enderror"
                                        value="{{ old('vendor_name', $profile->vendor_name ?? '') }}" placeholder="اسم المتجر أو الكيان">
                                    @error('vendor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="vehicle_type" class="form-label">نوع المركبة</label>
                                    <input type="text" name="vehicle_type" id="vehicle_type" class="form-control @error('vehicle_type') is-invalid @enderror"
                                        value="{{ old('vehicle_type', $profile->vehicle_type ?? '') }}" placeholder="مثال: سكوتر، شاحنة صغيرة">
                                    @error('vehicle_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="vehicle_plate" class="form-label">رقم لوحة المركبة</label>
                                    <input type="text" name="vehicle_plate" id="vehicle_plate" class="form-control @error('vehicle_plate') is-invalid @enderror"
                                        value="{{ old('vehicle_plate', $profile->vehicle_plate ?? '') }}" placeholder="أرقام وحروف">
                                    @error('vehicle_plate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- حقول رفع الصور الخاصة (اختياري) --}}
                            <div class="row mt-3">
                                <div class="col-md-4 mb-3">
                                    <label for="license_image" class="form-label">صورة رخصة القيادة</label>
                                    <input type="file" name="license_image" id="license_image" class="form-control @error('license_image') is-invalid @enderror">
                                    @if($isEdit && $profile->license_image)<a href="{{ asset('storage/' . $profile->license_image) }}" target="_blank" class="small text-decoration-none">عرض الحالي</a>@endif
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="car_image" class="form-label">صورة المركبة</label>
                                    <input type="file" name="car_image" id="car_image" class="form-control @error('car_image') is-invalid @enderror">
                                    @if($isEdit && $profile->car_image)<a href="{{ asset('storage/' . $profile->car_image) }}" target="_blank" class="small text-decoration-none">عرض الحالي</a>@endif
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="id_image" class="form-label">صورة الهوية الشخصية</label>
                                    <input type="file" name="id_image" id="id_image" class="form-control @error('id_image') is-invalid @enderror">
                                    @if($isEdit && $profile->id_image)<a href="{{ asset('storage/' . $profile->id_image) }}" target="_blank" class="small text-decoration-none">عرض الحالي</a>@endif
                                </div>
                            </div>

                            {{-- حالة توثيق الهوية --}}
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_verified_id" name="is_verified_id" value="1"
                                    {{ old('is_verified_id', $user->is_verified_id ?? 0) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_verified_id">توثيق الهوية (تم مراجعة المستندات)</label>
                            </div>
                        </div>
                    </div>
                </div> {{-- End of row --}}
            </div>

            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-{{ $isEdit ? 'warning' : 'primary' }} btn-lg px-5">
                    <i class="fas fa-save me-2"></i> {{ $isEdit ? 'حفظ التعديلات' : 'إضافة المستخدم' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection