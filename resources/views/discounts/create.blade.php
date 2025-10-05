@extends('layouts.admin')

@section('page-title', 'إضافة خصم جديد')

@section('content')
<style>
    /* 1. خطوط وألوان احترافية (منسوخة من صفحة الإدارة) */
    :root {
        --primary-color: #007bff;
        /* الأزرق الأساسي */
        --primary-hover: #0056b3;
        --secondary-bg: #ffffff;
        --border-color: #e9ecef;
        --header-color: #343a40;
    }

    body {
        background-color: #f4f7fa;
        /* خلفية فاتحة ونظيفة */
        font-family: 'Cairo', sans-serif;
    }

    /* 2. تحسين تصميم البطاقات والظلال */
    .card.shadow-sm {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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
        transition: background-color 0.3s, transform 0.2s;
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
        transform: translateY(-1px);
    }

    .btn-secondary {
        border-radius: 8px;
        background-color: #6c757d;
        border-color: #6c757d;
        transition: background-color 0.3s;
    }

    /* 4. تحسين حقول النموذج */
    .form-label {
        font-weight: 600;
        color: var(--header-color);
        margin-bottom: 0.5rem;
    }

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

    /* تحسين لتجميع حقول المدة */
    .date-range-group>div {
        border-left: 1px solid var(--border-color);
    }

    .date-range-group>div:last-child {
        border-left: none;
    }
</style>

<div class="container-fluid">
    {{-- 🏷️ العنوان وزر العودة --}}
    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-3">
        <h4 class="fw-bold mb-0">
            <i class="fas fa-tags text-primary me-2"></i> إضافة خصم جديد
        </h4>
        <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> العودة لقائمة الخصومات
        </a>
    </div>

    {{-- 📝 نموذج الإضافة --}}
    <form action="{{ route('admin.discounts.store') }}" method="POST" class="card shadow-lg p-5">
        @csrf

        {{-- عرض رسائل الأخطاء (لتحسين تجربة المستخدم) --}}
        @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="row g-4">

            {{-- المجموعة الأولى: التفاصيل الأساسية للخصم --}}
            <h5 class="mb-3 text-primary"><i class="fas fa-info-circle me-1"></i> تفاصيل الخصم</h5>

            <div class="col-md-6">
                <label class="form-label">عنوان الخصم</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="مثال: خصم عيد الأضحى المبارك" value="{{ old('title') }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">المناسبة</label>
                <input type="text" name="occasion" class="form-control @error('occasion') is-invalid @enderror" placeholder="رمضان / عيد العمال..." value="{{ old('occasion') }}">
                @error('occasion') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">نوع الخصم</label>
                <select name="discount_type" class="form-select @error('discount_type') is-invalid @enderror">
                    <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>نسبة مئوية (%)</option>
                    <option value="amount" {{ old('discount_type') == 'amount' ? 'selected' : '' }}>مبلغ ثابت (د.أ)</option>
                </select>
                @error('discount_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">قيمة الخصم</label>
                <input type="number" name="value" class="form-control @error('value') is-invalid @enderror" step="0.01" min="0.01" placeholder="أدخل القيمة" value="{{ old('value') }}" required>
                @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">الجهة المستهدفة</label>
                <select name="target_type" class="form-select @error('target_type') is-invalid @enderror" id="targetType" onchange="toggleTargetFields()">
                    <option value="all_drivers" {{ old('target_type') == 'all_drivers' ? 'selected' : '' }}>جميع السائقين</option>
                    <option value="specific_driver" {{ old('target_type') == 'specific_driver' ? 'selected' : '' }}>سائق محدد</option>
                    <option value="specific_package" {{ old('target_type') == 'specific_package' ? 'selected' : '' }}>باقة معينة</option>
                </select>
                @error('target_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <hr class="my-4">

            {{-- المجموعة الثانية: مدة الصلاحية وتحديد المستهدف --}}
            <h5 class="mb-3 text-primary"><i class="fas fa-calendar-alt me-1"></i> مدة الصلاحية والاستهداف</h5>

            <div class="col-md-6">
                <label class="form-label">تاريخ البداية (من)</label>
                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">تاريخ الانتهاء (إلى)</label>
                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                    value="{{ old('end_date') }}" required>
                @error('end_date')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


            {{-- حقول الاستهداف المشروطة --}}
            <div class="col-md-6 {{ old('target_type') == 'specific_driver' ? '' : 'd-none' }}" id="driverField">
                <label class="form-label">اختيار السائق المستهدف</label>
                <select name="driver_id" class="form-select @error('driver_id') is-invalid @enderror">
                    {{-- إضافة خيار افتراضي فارغ --}}
                    <option value="">-- يرجى اختيار السائق --</option>
                    @foreach ($drivers as $driver)
                    <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                        {{ optional($driver->profile)->first_name }} {{ optional($driver->profile)->last_name }} - {{ $driver->phone }}
                    </option>
                    @endforeach
                </select>
                @error('driver_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 {{ old('target_type') == 'specific_package' ? '' : 'd-none' }}" id="packageField">
                <label class="form-label">اختيار الباقة المستهدفة</label>
                <select name="package_id" class="form-select @error('package_id') is-invalid @enderror">
                    {{-- إضافة خيار افتراضي فارغ --}}
                    <option value="">-- يرجى اختيار الباقة --</option>
                    @foreach ($packages as $package)
                    <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                        {{ $package->name }} - {{ $package->price }} د.أ
                    </option>
                    @endforeach
                </select>
                @error('package_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 mt-5 text-center">
                <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm">
                    <i class="fas fa-save me-1"></i> حفظ وتفعيل الخصم
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // وظيفة إظهار وإخفاء حقول الاستهداف بناءً على الاختيار
    function toggleTargetFields() {
        const type = document.getElementById('targetType').value;
        const driverField = document.getElementById('driverField');
        const packageField = document.getElementById('packageField');

        // إخفاء الكل أولاً
        driverField.classList.add('d-none');
        packageField.classList.add('d-none');

        // إظهار المطلوب
        if (type === 'specific_driver') {
            driverField.classList.remove('d-none');
        } else if (type === 'specific_package') {
            packageField.classList.remove('d-none');
        }
    }

    // لضمان عمل الدالة في حال وجود قيم قديمة (Old Input) بعد فشل التحقق
    document.addEventListener('DOMContentLoaded', () => {
        toggleTargetFields();
    });
</script>
@endsection