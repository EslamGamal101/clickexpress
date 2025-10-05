<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\DriverMembership;
use App\Models\DriverSubscription;
use App\Models\Package;
use App\Models\User; // نفترض أن السائقين هم users من نوع 'driver'
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminDriverController extends Controller
{
    private const DRIVER_TYPE = 'driver';
    private const LIST_RELATIONS = ['profile'];


    // ملف: app/Http/Controllers/Admin/AdminDriverController.php

    public function index(Request $request)
    {
        $driversQuery = User::where('type', self::DRIVER_TYPE)
            ->with(self::LIST_RELATIONS)
            ->withCount('orders')
            ->latest();

        if ($search = $request->query('search')) {
            $driversQuery->where(function ($query) use ($search) {
                $query->where('phone', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('vehicle_plate', 'like', "%{$search}%");
                    });
            });
        }
        if ($status = $request->query('status')) {
            switch ($status) {
                case 'active':
                    $driversQuery->where('is_active', true);
                    break;
                case 'banned':
                    $driversQuery->where('is_active', false);
                    break;
                case 'pending':
                    $driversQuery->where('is_active', true);
                    break;
            }
        }

        if ($location = $request->query('location')) {
            $driversQuery->whereHas('profile', function ($q) use ($location) {
                $q->where('city', 'like', "%{$location}%")
                    ->orWhere('area', 'like', "%{$location}%");
            });
        }

        if ($request->filled('registered_from')) {
            $driversQuery->whereDate('created_at', '>=', $request->input('registered_from'));
        }
        if ($request->filled('registered_to')) {
            $driversQuery->whereDate('created_at', '<=', $request->input('registered_to'));
        }

        if ($ordersCount = $request->query('orders_count')) {
            switch ($ordersCount) {
                case 'low': 
                    $driversQuery->having('orders_count', '<', 100);
                    break;
                case 'high': 
                    $driversQuery->having('orders_count', '>=', 100);
                    break;
                case 'very_high':
                    $driversQuery->having('orders_count', '>=', 1000);
                    break;
            }
        }



        // الفلترة حسب الاشتراك (Subscription)
        if ($subscription = $request->query('subscription')) {
            // يجب تنفيذ منطق البحث في جدول الاشتراكات هنا
            $driversQuery->whereHas('subscription', function ($q) use ($subscription) {
                if ($subscription === 'active') {
                    $q->where('ends_at', '>', now());
                } else { // 'expired'
                    $q->where('ends_at', '<=', now());
                }
            });
        }

        
        $totalDrivers = User::where('type', self::DRIVER_TYPE)->count();
        $activeDrivers = User::where('type', self::DRIVER_TYPE)->where('is_active', true)->count();
        $bannedDrivers = User::where('type', self::DRIVER_TYPE)->where('is_active', false)->count();
        $pendingDrivers = User::where('type', self::DRIVER_TYPE)->where('is_active', false)->count();


        // 5. جلب النتائج وإنشاء رسالة عدم وجود سائق
        $drivers = $driversQuery->paginate(20)->withQueryString();

        if ($drivers->isEmpty() && $request->hasAny(['search', 'status', 'location', 'orders_count', 'registered_from', 'registered_to'])) {
            $noResultsMessage = 'لم يتم العثور على سائق يطابق معايير البحث المحددة.';
        } else {
            $noResultsMessage = null;
        }

        // 6. إرجاع الـ View مع البيانات المطلوبة
        return view('driver.index', compact(
            'drivers',
            'noResultsMessage',
            'totalDrivers',
            'activeDrivers',
            'bannedDrivers',
            'pendingDrivers'
        ));
    }

    /**
     * عرض صفحة إضافة سائق جديد.
     */
    public function create()
    {
        $user = new User(['type' => self::DRIVER_TYPE]);
        return view('driver.form', ['user' => $user]);
    }

    /**
     * تخزين سائق جديد.
     */
    public function store(Request $request)
    {
        // استخدام قواعد التحقق الخاصة بالسائق (مشابهة لـ User لكن مع قيد type)
        $validatedData = $request->validate([
            'phone' => 'required|unique:users,phone|max:20',
            'email' => 'nullable|email|max:255',
            'password' => 'required|min:6',
            'is_active' => 'required|boolean',

            // بيانات الملف الشخصي المطلوبة للسائق
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'national_id' => 'required|string|max:50', // مفترض أن الرقم الوطني مطلوب للسائق
            'city' => 'required|string|max:100',
            'area' => 'required|string|max:100',
            'vendor_name' => 'nullable|string|max:255',
            'vehicle_type' => 'required|string|max:100', // مطلوب
            'vehicle_plate' => 'required|string|max:50', // مطلوب

            // التحقق من صور السائق
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'license_image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // مطلوب
            'car_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',     // مطلوب
            'id_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',      // مطلوب
        ]);

        $user = User::create([
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'password' => bcrypt($request->password),
            'type' => self::DRIVER_TYPE, // تعيين النوع كسائق
            'is_active' => $request->is_active,
            'is_verified_id' => $request->has('is_verified_id') ? 1 : 0,
        ]);

        $profile = $user->profile()->create($request->only([
            'first_name',
            'last_name',
            'national_id',
            'city',
            'area',
            'vendor_name',
            'vehicle_type',
            'vehicle_plate'
        ]));

        // معالجة رفع الصور
        $this->handleImageUploads($request, $profile);

        return redirect()->route('admin.drivers.index')->with('success', 'تم إضافة السائق بنجاح.');
    }


    public function show(User $driver)
    {

        if ($driver->type !== self::DRIVER_TYPE) {
            abort(404, 'المورد المطلوب ليس سائقاً.');
        }

        $driver->load([
            'profile',
            'subscription' => function ($query) {
                $query->latest();
            },
            'orders' => function ($query) {
                $query->latest()
                    ->limit(20);
            },
            'notifications' => function ($query) {
                $query->latest()->limit(5);
            },
            'activeMembership',
            'activeSubscription.package'
        ])
            // 3. تحميل الإحصائيات كأعمدة وهمية (Append Attributes)
            ->loadCount([
                'orders as completed_orders_count' => function ($query) {
                    // حساب عدد الطلبات المنجزة
                    $query->where('status', 'completed');
                },
            ]);
        $driver->average_rating = $driver->orders->where('status', 'completed')->avg(function ($order) {
            return optional($order->ratingForDriver)->score;
        });
        $packages = Package::get();
        // 5. إرجاع الـ View
        return view('driver.show', compact('driver', 'packages'));
    }

    /**
     * عرض نموذج تعديل بيانات سائق.
     */
    public function edit(User $driver)
    {
        if ($driver->type !== self::DRIVER_TYPE) {
            abort(404);
        }
        $driver = User::with('profile')->findOrFail($driver->id);
        // dd($driver);
        return view('users.form', ['user' => $driver]);
    }

    /**
     * تحديث بيانات سائق.
     */
    public function update(Request $request, User $driver)
    {
        if ($driver->type !== self::DRIVER_TYPE) {
            abort(404);
        }

        $validatedData = $request->validate([
            'phone' => 'required|max:20|unique:users,phone,' . $driver->id,
            'email' => 'nullable|email|max:255',
            'password' => 'nullable|min:6',
            'is_active' => 'required|boolean',

            // ... بقية قواعد التحقق من الملف الشخصي والصور
        ]);

        // 1. تحديث بيانات المستخدم
        $driver->update([
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'] ?? null,
            'is_active' => $validatedData['is_active'],
            'is_verified_id' => $request->has('is_verified_id') ? 1 : 0,
        ]);

        if ($request->filled('password')) {
            $driver->update(['password' => bcrypt($request->password)]);
        }

        // 2. تحديث الملف الشخصي
        $profile = $driver->profile()->updateOrCreate(['user_id' => $driver->id], $request->only([
            'first_name',
            'last_name',
            'national_id',
            'city',
            'area',
            'vendor_name',
            'vehicle_type',
            'vehicle_plate'
        ]));

        // معالجة رفع الصور
        $this->handleImageUploads($request, $profile);

        return redirect()->route('admin.drivers.show', $driver->id)->with('success', 'تم تحديث بيانات السائق بنجاح.');
    }

    
    public function toggleBan(User $driver)
    {
        if ($driver->type !== self::DRIVER_TYPE) {
            return redirect()->back()->with('error', 'الإجراء غير صالح لهذا المستخدم.');
        }

        $isBanning = $driver->is_active; // إذا كان نشطًا، سنقوم بحظره

        $driver->update(['is_active' => !$isBanning]);

        $message = $isBanning ? 'تم حظر السائق مؤقتاً.' : 'تم تفعيل حساب السائق.';
        return redirect()->back()->with('success', $message);
    }

    /**
     * حذف حساب السائق.
     */
    public function destroy(User $driver)
    {
        if ($driver->type !== self::DRIVER_TYPE) {
            return redirect()->back()->with('error', 'الإجراء غير صالح لهذا المستخدم.');
        }

        if ($driver->profile) {
            $driver->profile->delete();
        }

        $driver->delete();

        return redirect()->route('admin.drivers.index')->with('success', 'تم حذف حساب السائق بنجاح.');
    }

    /**
     * تجديد اشتراك السائق (Subscription).
     */
    public function renewSubscription(Request $request, User $driver)
    {
        if ($driver->type !== self::DRIVER_TYPE) {
            return redirect()->back()->with('error', 'الإجراء غير صالح لهذا المستخدم.');
        }

        return redirect()->back()->with('success', 'تم تجديد اشتراك السائق بنجاح.');
    }


    public function sendNotification(Request $request, User $driver)
    {

        return redirect()->back()->with('success', 'تم إرسال الإشعار إلى السائق بنجاح.');
    }

    private function handleImageUploads(Request $request, $profile)
    {
        $imageFields = [
            'profile_image' => 'profiles',
            'license_image' => 'licenses',
            'car_image' => 'cars',
            'id_image' => 'ids'
        ];

        foreach ($imageFields as $field => $directory) {
            if ($request->hasFile($field)) {

                // حذف الصورة القديمة
                if ($profile->$field && Storage::disk('public')->exists($profile->$field)) {
                    Storage::disk('public')->delete($profile->$field);
                }

                // رفع الصورة الجديدة
                $path = $request->file($field)->store($directory, 'public');
                $profile->update([$field => $path]);
            }
        }
    }
    public function activateMonthly(Request $request)
    {
       
        $request->validate([
            'driver_id' => 'required|exists:users,id',
            'price' => 'required|numeric|min:0.01',
            'duration' => 'required|integer|min:1',
        ]);

        $driver = User::findOrFail($request->driver_id);

        DriverMembership::where('driver_id', $driver->id)
            ->update(['is_active' => false]);

        $expiresAt = Carbon::now()->addMonths($request->duration);

        DriverMembership::create([
            'driver_id' => $driver->id,
            'price' => $request->price,
            'started_at' => Carbon::now(),
            'expires_at' => $expiresAt,
            'is_active' => true,
        ]);

        return redirect()->route('admin.drivers.show', $driver->id)
            ->with('success', 'تم تفعيل الاشتراك الشهري بنجاح حتى تاريخ ' . $expiresAt->format('Y-m-d'));
    }


    public function activateRidesPackage(Request $request)
    {
       
        $request->validate([
            'driver_id' => 'required|exists:users,id',
            'package_id' => 'required|exists:packages,id',
            'expiry_days' => 'required|integer|min:1',
        ]);

        $driver = User::findOrFail($request->driver_id);
        $package = Package::findOrFail($request->package_id);

        $expiresAt = Carbon::now()->addDays($request->expiry_days);

        DriverSubscription::create([
            'driver_id' => $driver->id,
            'package_id' => $package->id,
            'activated_at' => Carbon::now(),
            'expires_at' => $expiresAt,
            'remaining_rides' => $package->rides_count,
            'status' => 'active',
        ]);

        return redirect()->route('admin.drivers.show', $driver->id)
            ->with('success', 'تم تفعيل باقة الرحلات (' . $package->name . ') بنجاح.');
    }

    public function terminateSubscription(User $driver, $type)
    {
        if ($type === 'monthly') {
            $driver->activeMembership()?->update([
                'is_active' => false,
                'expires_at' => now(),
            ]);
        } elseif ($type === 'package') {
            $driver->activeSubscription()?->update([
                'status' => 'expired',
                'expires_at' => now(),
            ]);
        }

        return redirect()->route('admin.drivers.show', $driver->id)
            ->with('success', 'تم إنهاء ' . ($type === 'monthly' ? 'الاشتراك الشهري' : 'باقة الرحلات') . ' بنجاح.');
    }

    public function pending()
    {
        $pendingDrivers = User::where('type', 'driver')
            ->where('is_active', false)
            ->latest()
            ->with('profile') // Eager load the profile relation
            ->paginate(20);

        return view('driver.pending', compact('pendingDrivers'));
    }

    /**
     * Approve the driver registration and activate the account.
     *
     * @param User $driver
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(User $driver)
    {
        if ($driver->is_active) {
            return redirect()->back()->with('warning', 'السائق مُعتمد بالفعل.');
        }


        $driver->is_active = true;
        $driver->save();

        return redirect()->route('admin.drivers.show', $driver->id)
            ->with('success', 'تمت الموافقة على السائق بنجاح وتفعيل حسابه.');
    }

    /**
     * Reject the driver registration.
     *
     * @param Request $request
     * @param User $driver
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, User $driver)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $driver->is_active = false;
        $driver->save();

        return redirect()->route('admin.drivers.pending')
            ->with('error', 'تم رفض تسجيل السائق بنجاح.');
    }
}
