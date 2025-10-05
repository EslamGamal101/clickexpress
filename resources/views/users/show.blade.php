@extends('layouts.admin')

@section('page-title', 'تفاصيل حساب: ' . optional($user->profile)->first_name . ' ' . optional($user->profile)->last_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h3 class="text-dark-blue fw-light">
            <i class="fas fa-user-circle me-2"></i> ملف المستخدم: <span class="fw-bold">{{ optional($user->profile)->first_name }} {{ optional($user->profile)->last_name }}</span>
        </h3>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-right me-2"></i> العودة لقائمة المستخدمين
        </a>
    </div>

    <div class="row">

        <div class="col-lg-8 order-lg-1">

            {{-- بطاقة البيانات الشخصية --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark-blue">
                        <i class="fas fa-id-card me-2"></i> البيانات الشخصية والملخص
                    </h6>
                    <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }} px-3 py-2">
                        {{ $user->is_active ? 'نشط' : 'محظور' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-3">
                            {{-- يجب التأكد من وجود صورة افتراضية في حالة عدم توفر صورة الملف الشخصي --}}
                            @php
                            $profileImageUrl = optional($user->profile)->profile_image
                            ? asset('storage/' . $user->profile->profile_image)
                            : asset('images/default-profile.png');
                            @endphp
                            <img src="{{ $profileImageUrl }}"
                                alt="صورة الملف الشخصي"
                                class="img-fluid rounded-circle border border-2 p-1 mb-2 shadow-sm"
                                style="width: 120px; height: 120px; object-fit: cover;">
                            <h5 class="mt-2 text-primary fw-bold">{{ $user->translated_type }}</h5>
                        </div>
                        <div class="col-md-9">
                            <dl class="row mb-0 small">
                                <div class="d-flex justify-content-between border-bottom py-2">
                                    <dt class="text-muted mb-0">الاسم الكامل:</dt>
                                    <dd class="fw-bold mb-0">{{ optional($user->profile)->first_name }} {{ optional($user->profile)->last_name }}</dd>
                                </div>

                                <div class="d-flex justify-content-between border-bottom py-2">
                                    <dt class="text-muted mb-0">رقم الهاتف:</dt>
                                    <dd class="mb-0" dir="ltr">{{ $user->phone }} </dd>
                                </div>

                                <div class="d-flex justify-content-between border-bottom py-2">
                                    <dt class="text-muted mb-0">الرقم الوطني:</dt>
                                    <dd class="mb-0">
                                        {{ optional($user->profile)->national_id ?? 'غير متوفر' }}
                                        @if($user->is_verified_id)
                                        <span class="badge bg-success ms-2"><i class="fas fa-check-circle"></i> موثق</span>
                                        @else
                                        <span class="badge bg-secondary ms-2"><i class="fas fa-times-circle"></i> غير موثق</span>
                                        @endif
                                    </dd>
                                </div>

                                <div class="d-flex justify-content-between border-bottom py-2">
                                    <dt class="text-muted mb-0">تاريخ التسجيل:</dt>
                                    <dd class="mb-0">{{ $user->created_at->format('Y-m-d H:i A') }}</dd>
                                </div>

                                <div class="d-flex justify-content-between border-bottom py-2">
                                    <dt class="text-muted mb-0">آخر نشاط:</dt>
                                    <dd class="mb-0">{{ $user->updated_at->diffForHumans() }}</dd>
                                </div>

                                <div class="d-flex justify-content-between py-2">
                                    <dt class="text-muted mb-0">الموقع:</dt>
                                    <dd class="mb-0">{{ optional($user->profile)->city ?? '' }} - {{ optional($user->profile)->area ?? 'لم يتم التحديد' }}</dd>
                                </div>
                            </dl>

                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light d-flex justify-content-between small">
                    <span class="text-muted">عدد الطلبات المنفذة:
                        <span class="fw-bold text-success">{{ $user->completed_orders_count ?? 0 }}</span>
                    </span>
                    <span class="text-muted">متوسط التقييم:
                        <span class="fw-bold text-warning">{{ number_format($user->average_rating ?? 0, 1) }} <i class="fas fa-star"></i></span>
                    </span>
                </div>
            </div>

            {{-- سجل الطلبات --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-truck me-2"></i>
                        سجل الطلبات ({{ $user->orders->count() }} طلب)
                    </h6>
                </div>
                <div class="card-body">
                    @if($user->orders->isEmpty())
                    <div class="alert alert-info text-center">
                        لا توجد طلبات مسجلة لهذا المستخدم حتى الآن.
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الحالة</th>
                                    <th>سعر الطلب</th>
                                    <th>رسوم التوصيل</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>التقييم</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->orders as $order)
                                @php
                                $statusMap = [
                                'completed' => ['success', 'fa-check-circle', 'مكتمل'],
                                'pending' => ['warning', 'fa-clock', 'قيد الانتظار'],
                                'in_progress' => ['info', 'fa-truck', 'قيد التنفيذ'],
                                'cancelled' => ['danger', 'fa-times-circle', 'ملغي'],
                                ];
                                [$class, $icon, $label] = $statusMap[$order->status] ?? ['secondary', 'fa-question-circle', 'غير معروف'];
                                @endphp
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>
                                        <span class="badge bg-{{ $class }}">
                                            <i class="fas {{ $icon }}"></i> {{ $label }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($order->price, 2) }} د.أ</td>
                                    <td>{{ number_format($order->delivery_fee, 2) }}د.أ</td>
                                    <td dir="ltr">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if(optional($order->ratingForDriver)->score)
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $order->ratingForDriver->score ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                            @else
                                            <span class="text-muted">لم يتم التقييم</span>
                                            @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>


            <div class="row">
                {{-- تقييماته --}}
                <div class="col-md-6">
                    <div class="card shadow mb-4 h-100">
                        <div class="card-header py-3 bg-light">
                            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-star me-2"></i> تقييماته على الطلبات</h6>
                        </div>
                        <div class="card-body">
                            @php $ratings = $user->ratingsGiven; @endphp
                            <p class="h6 mb-3">متوسط التقييم:
                                <span class="text-warning">{{ number_format($user->average_rating ?? 0, 1) }} <i class="fas fa-star"></i></span>
                                <small class="text-muted"> ({{ $ratings->count() }} تقييم)</small>
                            </p>
                            @if($ratings->isEmpty())
                            <div class="alert alert-warning text-center small">لم يقم المستخدم بتقييم أي خدمة بعد.</div>
                            @else
                            <ul class="list-group list-group-flush small">
                                {{-- يجب تعريف latestRatingsGiven في الموديل أو استخدام orderBy --}}
                                @foreach($ratings->take(3) as $rating)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <span class="text-warning fw-bold">{{ $rating->score }} <i class="fas fa-star small"></i></span>
                                        <span class="small text-dark">{{ Str::limit($rating->comment, 30) ?? '(لا يوجد تعليق)' }}</span>
                                    </div>
                                    <span class="badge bg-light text-muted">{{ $rating->created_at->diffForHumans() }}</span>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- الاشعارات (بيانات تجريبية) --}}
                <div class="col-md-6">
                    <div class="card shadow mb-4 h-100">
                        <div class="card-header py-3 bg-light">
                            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-bell me-2"></i> سجل الإشعارات (تجريبي)</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div><span class="fw-bold">تهنئة</span>: لقد وصل طلبك بنجاح.</div>
                                    <span class="badge bg-info">منذ 3 ساعات</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div><span class="fw-bold">تحديث</span>: تم فك حظرك الإداري.</div>
                                    <span class="badge bg-info">منذ يوم</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div><span class="fw-bold">عرض خاص</span>: خصم 20% على خدمات التوصيل.</div>
                                    <span class="badge bg-info">منذ أسبوع</span>
                                </li>
                                <li class="list-group-item text-center">
                                    <a href="#" class="btn btn-sm btn-outline-secondary mt-2">عرض الكل</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <div class="col-lg-4 order-lg-2">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-dark text-white">
                    <h6 class="m-0 fw-bold"><i class="fas fa-tools me-2"></i> أدوات الإدارة</h6>
                </div>
                <div class="card-body">

                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-block mb-2 w-100"><i class="fas fa-edit me-2"></i> تعديل بيانات المستخدم</a>

                    @if($user->is_active)
                    <form action="{{ route('admin.users.ban', $user->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حظر المستخدم؟')" class="mb-2">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-block w-100"><i class="fas fa-user-slash me-2"></i> حظر المستخدم</button>
                    </form>
                    @else
                    <form action="{{ route('admin.users.unban', $user->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من فك الحظر عن المستخدم؟')" class="mb-2">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-success btn-block w-100"><i class="fas fa-user-check me-2"></i> فك الحظر</button>
                    </form>
                    @endif

                    <hr class="my-3">

                  

                    @if($user->type == 'management_producers')
                    {{-- ملاحظة: يجب التأكد من عمل المودال الخاص بهذه الوظيفة --}}
                    <button type="button" class="btn btn-info btn-block mb-2 w-100" data-bs-toggle="modal" data-bs-target="#resetOrdersModal"><i class="fas fa-redo-alt me-2"></i> تعيين الطلبات لـ صفر</button>
                    @endif

                    <a href="{{ route('admin.users.download_pdf', $user->id) }}" class="btn btn-outline-secondary btn-block mb-2 w-100"><i class="fas fa-file-pdf me-2"></i> تنزيل سجل PDF</a>

                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('تحذير! هل أنت متأكد من حذف الحساب نهائياً؟')" class="mt-3">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-block w-100"><i class="fas fa-trash me-2"></i> حذف الحساب</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection