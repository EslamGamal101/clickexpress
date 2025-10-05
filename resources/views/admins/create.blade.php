@extends('layouts.admin') {{-- استبدل بـ Layouts المناسب لمشروعك --}}

@section('content')
<div class="container">
    <h2>إضافة مسؤول جديد</h2>

    <form method="POST" action="{{ route('admin.store') }}">
        @csrf

        {{-- الاسم الكامل --}}
        <div class="mb-3">
            <label for="name" class="form-label">الاسم الكامل</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- رقم الهاتف --}}
        <div class="mb-3">
            <label for="phone" class="form-label">رقم الهاتف</label>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
            @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- البريد الإلكتروني (اختياري) --}}
        <div class="mb-3">
            <label for="email" class="form-label">البريد الإلكتروني (اختياري)</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- كلمة المرور --}}
        <div class="mb-3">
            <label for="password" class="form-label">كلمة المرور</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- تأكيد كلمة المرور --}}
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>

        {{-- الدور (Role) --}}
        <div class="mb-3">
            <label for="role" class="form-label">الدور</label>
            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                <option value="">اختر الدور</option>
                @foreach ($roles as $role)
                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ $role->name == 'super_admin' ? 'مسؤول رئيسي' : 'مسؤول عادي' }}</option>
                @endforeach
            </select>
            @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">حفظ المسؤول</button>
        <a href="{{ route('admin.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection