@extends('layouts.admin') {{-- استبدل بـ Layouts المناسب لمشروعك --}}

@section('content')
<div class="container">
    <h2>إدارة المسؤولين</h2>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @can('create_admins') {{-- زر الإضافة يظهر فقط لمن لديه الصلاحية --}}
    <a href="{{ route('admins.create') }}" class="btn btn-primary mb-3">إضافة مسؤول جديد</a>
    @endcan

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>الرقم (ID)</th>
                    <th>الاسم الكامل</th>
                    <th>رقم الهاتف</th>
                    <th>البريد الإلكتروني</th>
                    <th>الدور</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($admins as $admin)
                <tr>
                    <td>{{ $admin->id }}</td>
                    <td>{{ $admin->name ?? 'غير مُحدد' }}</td> {{-- افترضنا وجود حقل name في الـ user model --}}
                    <td>{{ $admin->phone }}</td>
                    <td>{{ $admin->email ?? 'لا يوجد' }}</td>
                    <td>
                        {{-- عرض الدور الأساسي (Spatie) --}}
                        @if ($admin->roles->isNotEmpty())
                        @foreach ($admin->roles as $role)
                        <span class="badge bg-info">{{ $role->name }}</span>
                        @endforeach
                        @else
                        <span class="badge bg-secondary">غير مُحدد</span>
                        @endif
                    </td>
                    <td>
                        @can('delete_admins')
                        {{-- زر الحذف --}}
                        <form action="{{ route('admins.destroy', $admin->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا المسؤول؟')">حذف</button>
                        </form>
                        @endcan
                        {{-- يمكن إضافة زر التعديل هنا (Edit) بنفس طريقة التحقق من الصلاحية --}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection