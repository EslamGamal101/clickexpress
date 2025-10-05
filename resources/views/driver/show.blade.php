@extends('layouts.admin')

@section('page-title', 'تفاصيل حساب السائق: ' . optional($driver->profile)->first_name . ' ' . optional($driver->profile)->last_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h3 class="text-dark-blue fw-light">
            <i class="fas fa-motorcycle me-2"></i> ملف السائق: <span class="fw-bold">{{ optional($driver->profile)->first_name }} {{ optional($driver->profile)->last_name }}</span>
        </h3>
        <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-right me-2"></i> العودة لقائمة السائقين
        </a>
    </div>

    <div class="row">

        <div class="col-lg-8 order-lg-1">

            {{-- بطاقة البيانات الشخصية والملخص --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark-blue">
                        <i class="fas fa-id-card me-2"></i> البيانات الأساسية للسائق
                    </h6>
                    <span class="badge bg-{{ $driver->is_active ? 'success' : 'danger' }} px-3 py-2">
                        {{ $driver->is_active ? 'نشط' : 'محظور' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-3">
                            <img src="{{ asset('storage/' . optional($driver->profile)->profile_image) }}"
                                alt="صورة الملف الشخصي"
                                class="img-fluid rounded-circle border border-2 p-1 mb-2 shadow-sm"
                                style="width: 120px; height: 120px; object-fit: cover;">
                            <h5 class="mt-2 text-primary fw-bold">سائق</h5>
                        </div>
                        <div class="col-md-9">
                            <dl class="row mb-0 small">
                                <div class="d-flex justify-content-between border-bottom py-2">
                                    <dt class="text-muted mb-0">الاسم الكامل:</dt>
                                    <dd class="fw-bold mb-0">{{ optional($driver->profile)->first_name }} {{ optional($driver->profile)->last_name }}</dd>
                                </div>

                                <div class="d-flex justify-content-between border-bottom py-2">
                                    <dt class="text-muted mb-0">رقم الهاتف:</dt>
                                    <dd class="mb-0" dir="ltr">{{ $driver->phone }} </dd>
                                </div>

                                <div class="d-flex justify-content-between border-bottom py-2">
                                    <dt class="text-muted mb-0">تاريخ التسجيل:</dt>
                                    <dd class="mb-0">{{ $driver->created_at->format('Y-m-d H:i A') }}</dd>
                                </div>

                                <div class="d-flex justify-content-between py-2">
                                    <dt class="text-muted mb-0">الموقع:</dt>
                                    <dd class="mb-0">{{ optional($driver->profile)->city ?? '' }} - {{ optional($driver->profile)->area ?? 'لم يتم التحديد' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light d-flex justify-content-between small">
                    <span class="text-muted">عدد الطلبات المنفذة:
                        <span class="fw-bold text-success">{{ $driver->completed_orders_count ?? 0 }}</span>
                    </span>
                    <span class="text-muted">متوسط التقييم:
                        <span class="fw-bold text-warning">{{ number_format($driver->average_rating ?? 0, 1) }} <i class="fas fa-star"></i></span>
                    </span>
                </div>
            </div>

            {{-- بطاقة تفاصيل المركبة والتوثيق --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 fw-bold text-dark-blue">
                        <i class="fas fa-car-side me-2"></i> تفاصيل المركبة والتوثيق
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        {{-- تفاصيل المركبة (نفترض وجودها في Profile أو Vehicle Model) --}}
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <dt class="text-muted mb-0">رقم لوحة المركبة (ترميز):</dt>
                            <dd class="fw-bold mb-0 badge bg-primary">{{ optional($driver->profile)->vehicle_plate ?? 'غير متوفر' }}</dd>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <dt class="text-muted mb-0">نوع المركبة:</dt>
                            <dd class="mb-0">{{ optional($driver->profile)->vehicle_type ?? 'سكوتر/دراجة' }}</dd>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <dt class="text-muted mb-0">حالة السائق :</dt>
                            <dd class="mb-0">
                                @if($driver->is_active)
                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> نشط</span>
                                @else
                                <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>في انتظار التوثيق </span>
                                @endif
                            </dd>
                        </div>


                        {{-- حالة توثيق الهوية --}}
                        <div class="card mt-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-images me-2"></i> صور توثيق السائق</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered table-striped mb-0 text-center align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>المستند</th>
                                            <th>الصورة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (optional($driver->profile)->national_id_image)
                                        <tr>
                                            <td class="fw-bold">الهوية الوطنية / الإقامة</td>
                                            <td>
                                                <a href="{{ asset('storage/' . $driver->profile->national_id_image) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $driver->profile->national_id_image) }}"
                                                        alt="صورة الهوية" class="img-thumbnail" style="max-height: 120px;">
                                                </a>
                                            </td>
                                        </tr>
                                        @endif

                                        @if (optional($driver->profile)->license_image)
                                        <tr>
                                            <td class="fw-bold">رخصة القيادة</td>
                                            <td>
                                                <a href="{{ asset('storage/' . $driver->profile->license_image) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $driver->profile->license_image) }}"
                                                        alt="صورة الرخصة" class="img-thumbnail" style="max-height: 120px;">
                                                </a>
                                            </td>
                                        </tr>
                                        @endif

                                        @if (optional($driver->profile)->id_card_image)
                                        <tr>
                                            <td class="fw-bold">البطاقة</td>
                                            <td>
                                                <a href="{{ asset('storage/' . $driver->profile->id_card_image) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $driver->profile->id_card_image) }}"
                                                        alt="صورة البطاقة" class="img-thumbnail" style="max-height: 120px;">
                                                </a>
                                            </td>
                                        </tr>
                                        @endif

                                        @if (optional($driver->profile)->car_image)
                                        <tr>
                                            <td class="fw-bold">السيارة</td>
                                            <td>
                                                <a href="{{ asset('storage/' . $driver->profile->car_image) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $driver->profile->car_image) }}"
                                                        alt="صورة السيارة" class="img-thumbnail" style="max-height: 120px;">
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </dl>
                </div>
            </div>

            {{-- سجل الطلبات (Orders Log) --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light">
                    {{-- ملاحظة: تم تعديل العلاقة لافتراض أن orders موجودة ومحملة --}}
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-box-open me-2"></i> سجل الطلبات المنجزة ({{ $driver->orders->count() ?? 0 }} طلب)</h6>
                </div>
                <div class="card-body">
                    @if(optional($driver->orders)->isEmpty())
                    <div class="alert alert-info text-center">لم يقم هذا السائق بتنفيذ أي طلبات مسجلة بعد.</div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th># الطلب</th>
                                    <th>الحالة</th>
                                    <th>الإجمالي</th>
                                    <th>تاريخ الإنهاء</th>
                                    <th>التقييم المستلم</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($driver->orders->take(5) as $order)
                                @php
                                $statusClass = [
                                'completed' => 'success',
                                'pending' => 'warning',
                                'in_progress' => 'info',
                                'cancelled' => 'danger',
                                ][$order->status] ?? 'secondary';
                                @endphp
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td><span class="badge bg-{{ $statusClass }}">{{ $order->status }}</span></td>
                                    <td>{{ number_format($order->total_amount, 2) }}</td>
                                    <td dir="ltr">{{ $order->updated_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if(optional($order)->ratingForDriver)
                                        <span class="text-warning"><i class="fas fa-star"></i> {{ $order->ratingForDriver->score }}</span>
                                        @else
                                        <span class="text-muted">-</span>
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
        </div>

        {{-- العمود الجانبي للأدوات (تم تعديل قسم الاشتراكات هنا) --}}
        <div class="col-lg-4 order-lg-2">
            @php
            $activeMembership = $driver->activeMembership ?? null;
            $isMonthlyActive = $activeMembership && $activeMembership->is_active && $activeMembership->expires_at->isFuture();

            $activePackage = $driver->activeSubscription ?? null;

            $isPackageActive = $activePackage && $activePackage->status;
            @endphp

            {{-- بطاقة حالة الاشتراك الشهري (مستقلة) --}}
            <div class="card shadow mb-4 border-{{ $isMonthlyActive ? 'success' : 'primary' }}">
                <div class="card-header py-3 bg-{{ $isMonthlyActive ? 'success' : 'primary' }} text-white">
                    <h6 class="m-0 fw-bold"><i class="fas fa-calendar-check me-2"></i> الاشتراك الشهري</h6>
                </div>
                <div class="card-body">
                    @if($isMonthlyActive)
                    <div class="text-center mb-3">
                        <span class="badge bg-success h5 px-3 py-2"><i class="fas fa-check-circle me-1"></i> اشتراك نشط</span>
                    </div>
                    <dl class="row mb-0 small border rounded p-3 bg-light">
                        <div class="col-6 text-muted mb-1">تاريخ الانتهاء:</div>
                        <div class="col-6 fw-bold mb-1 text-end">{{ $activeMembership->expires_at->format('Y-m-d H:i A') }}</div>

                        <div class="col-6 text-muted mb-1">التكلفة الشهرية:</div>
                        <div class="col-6 fw-bold mb-1 text-end">{{ number_format($activeMembership->price, 2) }} ر.س</div>
                    </dl>
                    {{-- استخدام route 'terminate_subscription' الأصلي لإلغاء الاشتراك الشهري --}}
                    <form action="{{ route('admin.drivers.terminate_subscription', ['driver' => $driver->id, 'type' => 'monthly']) }}" method="POST" onsubmit="return confirm('تأكيد إنهاء الاشتراك الشهري الحالي؟')" class="mt-3">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100"><i class="fas fa-power-off me-2"></i> إنهاء الاشتراك</button>
                    </form>
                    @else
                    <div class="alert alert-warning text-center small py-2">
                        <i class="fas fa-exclamation-triangle me-1"></i> لا يوجد اشتراك شهري فعال.
                    </div>
                    <button type="button" class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#activateMonthlyModal">
                        <i class="fas fa-calendar-alt me-1"></i> تفعيل اشتراك شهري جديد
                    </button>
                    @endif
                </div>
            </div>

            {{-- بطاقة باقة الرحلات (مستقلة) --}}
            <div class="card shadow mb-4 border-{{ $isPackageActive ? 'info' : 'secondary' }}">
                <div class="card-header py-3 bg-{{ $isPackageActive ? 'info text-dark' : 'secondary text-white' }}">
                    <h6 class="m-0 fw-bold"><i class="fas fa-ticket-alt me-2"></i> باقة الرحلات</h6>
                </div>
                <div class="card-body">
                    @if($isPackageActive)
                    <div class="text-center mb-3">
                        <span class="badge bg-info text-dark h5 px-3 py-2"><i class="fas fa-check-circle me-1"></i> باقة فعالة</span>
                    </div>
                    <dl class="row mb-0 small border rounded p-3 bg-light">
                        <div class="col-6 text-muted mb-1">اسم الباقة:</div>
                        <div class="col-6 fw-bold mb-1 text-end">{{ optional($activePackage->package)->name ?? 'باقة محددة' }}</div>

                        <div class="col-6 text-muted mb-1">الرحلات المتبقية:</div>
                        <div class="col-6 fw-bold mb-1 text-end">{{ number_format($activePackage->remaining_rides) }}</div>

                        <div class="col-6 text-muted mb-1">تاريخ الانتهاء (الوقت):</div>
                        <div class="col-6 fw-bold mb-1 text-end">{{ $activePackage->expires_at ? $activePackage->expires_at->format('Y-m-d') : 'غير محدد' }}</div>
                    </dl>
                    {{-- استخدام route 'terminate_subscription' الأصلي لإلغاء باقة الرحلات، مع تمرير نوع الاشتراك --}}
                    <form action="{{ route('admin.drivers.terminate_subscription', ['driver' => $driver->id, 'type' => 'package']) }}" method="POST" onsubmit="return confirm('تأكيد إنهاء باقة الرحلات الحالية؟')" class="mt-3">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-warning w-100"><i class="fas fa-power-off me-2"></i> إنهاء الباقة</button>
                    </form>
                    @else
                    <div class="alert alert-secondary text-center small py-2">
                        <i class="fas fa-times-circle me-1"></i> لا توجد باقة رحلات فعالة.
                    </div>
                    <button type="button" class="btn btn-sm btn-info text-dark w-100" data-bs-toggle="modal" data-bs-target="#activateRidesModal">
                        <i class="fas fa-ticket-alt me-1"></i> تفعيل باقة رحلات جديدة
                    </button>
                    @endif
                </div>
            </div>


            {{-- بطاقة أدوات الإدارة --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-dark text-white">
                    <h6 class="m-0 fw-bold"><i class="fas fa-tools me-2"></i> أدوات الإدارة</h6>
                </div>
                <div class="card-body">

                    <a href="{{ route('admin.drivers.edit', $driver->id) }}" class="btn btn-warning btn-block mb-2 w-100"><i class="fas fa-edit me-2"></i> تعديل بيانات السائق</a>

                    {{-- تبديل حالة التوثيق --}}
                    @if(!($driver->is_verified_id ?? false))
                    <form action="{{ route('admin.drivers.verify', $driver->id) }}" method="POST" onsubmit="return confirm('تأكيد توثيق بيانات السائق؟')" class="mb-2">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-info btn-block w-100"><i class="fas fa-check-double me-2"></i> توثيق حساب السائق</button>
                    </form>
                    @endif

                    {{-- حظر / فك حظر --}}
                    @if($driver->is_active)
                    <form action="{{ route('admin.drivers.toggle_ban', $driver->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حظر السائق؟ سيمنعه هذا من استلام الطلبات.')" class="mb-2">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-block w-100"><i class="fas fa-user-slash me-2"></i> حظر السائق</button>
                    </form>
                    @else
                    <form action="{{ route('admin.drivers.toggle_ban', $driver->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من فك الحظر عن السائق؟')" class="mb-2">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-success btn-block w-100"><i class="fas fa-user-check me-2"></i> فك الحظر</button>
                    </form>
                    @endif

                    <hr class="my-3">

                    <button type="button" class="btn btn-primary btn-block mb-2 w-100" data-bs-toggle="modal" data-bs-target="#sendNotificationModal"><i class="fas fa-paper-plane me-2"></i> إرسال إشعار يدوي</button>

                    <a href="{{ route('admin.drivers.download_pdf', $driver->id) }}" class="btn btn-outline-secondary btn-block mb-2 w-100"><i class="fas fa-file-pdf me-2"></i> تنزيل سجل PDF</a>

                    <form action="{{ route('admin.drivers.destroy', $driver->id) }}" method="POST" onsubmit="return confirm('تحذير! هل أنت متأكد من حذف الحساب نهائياً؟')" class="mt-3">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-block w-100"><i class="fas fa-trash me-2"></i> حذف الحساب</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ---------------------------------------------------------------- --}}
{{-- المودالات: نماذج تفعيل الاشتراك (تبقى كما هي) --}}
{{-- ---------------------------------------------------------------- --}}

{{-- 1. نافذة تفعيل الاشتراك الشهري (DriverMembership) --}}
<div class="modal fade" id="activateMonthlyModal" tabindex="-1" aria-labelledby="activateMonthlyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="activateMonthlyModalLabel"><i class="fas fa-calendar-plus me-2"></i> تفعيل الاشتراك الشهري</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.drivers.activate_monthly', $driver->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small"> سيتم إنشاء سجل في جدول `DriverMembership` وتعيين تاريخ انتهاء الصلاحية بناءً على المدة.</p>
                    <div class="mb-3">
                        <input type="number" step="0.01" name="driver_id" id="monthly_price" class="form-control" value="{{ $driver->id }}" hidden>
                    </div>
                    <div class="mb-3">
                        <label for="monthly_price" class="form-label">سعر الاشتراك (شهرياً)</label>
                        <input type="number" step="0.01" name="price" id="monthly_price" class="form-control" required placeholder="مثال: 150.00">
                    </div>

                    <div class="mb-3">
                        <label for="monthly_duration" class="form-label">المدة بالأشهر</label>
                        <input type="number" name="duration" id="monthly_duration" class="form-control" value="1" min="1" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check me-1"></i> تفعيل الاشتراك</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 2. نافذة تفعيل باقة الرحلات (DriverSubscription) --}}
<div class="modal fade" id="activateRidesModal" tabindex="-1" aria-labelledby="activateRidesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-dark">
                <h5 class="modal-title" id="activateRidesModalLabel"><i class="fas fa-box me-2"></i> تفعيل باقة الرحلات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.drivers.activate_rides_package', $driver->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">سيتم اختيار باقة من الباقات المُعرّفة مُسبقاً وتفعيلها للسائق.</p>
                    <div class="mb-3">
                        <input type="number" step="0.01" name="driver_id" id="monthly_price" class="form-control" value="{{ $driver->id }}" hidden>
                    </div>
                    <div class="mb-3">
                        <label for="package_id" class="form-label">اختر الباقة</label>
                        <select name="package_id" id="package_id" class="form-select" required>
                            <option value="" disabled selected>-- اختر باقة الرحلات --</option>
                            {{-- افتراض وجود متغير $packages ممرر من المتحكم --}}
                            @forelse ($packages ?? [] as $package)
                            <option value="{{ $package->id }}">
                                {{ $package->name }} ({{ $package->ride_limit }} رحلة / {{ number_format($package->price, 2) }} ر.س)
                            </option>
                            @empty
                            <option value="" disabled>لا توجد باقات رحلات متاحة حالياً.</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="expiry_days" class="form-label">مدة صلاحية الباقة (بالأيام)</label>
                        <input type="number" name="expiry_days" id="expiry_days" class="form-control" value="90" min="1" required placeholder="كم يوم تبقى الباقة سارية حتى لو لم تُستخدم">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-info text-dark"><i class="fas fa-check me-1"></i> تفعيل الباقة</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 3. نافذة إرسال إشعار يدوي (Notification Modal - افتراضية) --}}
<div class="modal fade" id="sendNotificationModal" tabindex="-1" aria-labelledby="sendNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="sendNotificationModalLabel"><i class="fas fa-paper-plane me-2"></i> إرسال إشعار للسائق</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.drivers.send_notification', $driver->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">أدخل محتوى الإشعار الذي سيتم إرساله مباشرة إلى تطبيق السائق.</p>
                    <div class="mb-3">
                        <label for="notification_title" class="form-label">العنوان</label>
                        <input type="text" name="title" id="notification_title" class="form-control" required placeholder="مثال: تنبيه بانتهاء الباقة">
                    </div>
                    <div class="mb-3">
                        <label for="notification_body" class="form-label">المحتوى/الرسالة</label>
                        <textarea name="body" id="notification_body" class="form-control" rows="3" required placeholder="اكتب نص الإشعار هنا..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> إرسال</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection