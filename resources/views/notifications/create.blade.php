@extends('layouts.admin')

@section('content')
<div class="container">
    <h3 class="mb-4"><i class="fas fa-paper-plane"></i> إرسال إشعار يدوي</h3>

    {{-- رسالة نجاح أو خطأ --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.notifications.send') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">مستلم الإشعار (ID)</label>
            <input type="number" name="receiver_id" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">نوع المستلم</label>
            <select name="receiver_type" class="form-control" required>
                <option value="user">مستخدم</option>
                <option value="driver">سائق</option>
                <option value="vendor">بائع</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">عنوان الإشعار</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">محتوى الإشعار</label>
            <textarea name="body" rows="3" class="form-control" required></textarea>
        </div>

        <button type="submit" class="btn btn-success">
            <i class="fas fa-paper-plane"></i> إرسال الإشعار
        </button>
    </form>
</div>
@endsection