<?php

namespace App\Http\Controllers\web;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequestWeb;
use App\Models\Order;
use App\Models\Profile;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    /**
     * 📌 عرض كل المستخدمين مع البحث والفلترة
     */
    public function index(Request $request)
    {
        $query = User::with(['profile', 'orders'])
            ->whereIn('type', ['customer', 'vendor', 'management_producers']); 
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('profile', function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%");
                })
                    ->orWhere('phone', 'like', "%$search%");
            });
        }
        if ($request->filled('registered_from') && $request->filled('registered_to')) {
            $query->whereBetween('created_at', [$request->registered_from, $request->registered_to]);
        }
        if ($request->filled('city')) {
            $query->whereHas('profile', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }
        if ($request->filled('orders_count')) {
            $query->withCount('orders')
                ->having('orders_count', '>=', $request->orders_count);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        $users = $query->orderBy('id', 'desc')->paginate(15);
        $totalUsers  = User::whereIn('type', ['customer', 'vendor', 'management_producers'])->count();
        $activeUsers = User::whereIn('type', ['customer', 'vendor', 'management_producers'])
            ->where('is_active', 1)
            ->count();
        $bannedUsers = User::whereIn('type', ['customer', 'vendor', 'management_producers'])
            ->where('is_active', 0)
            ->count();

        return view('users.index', compact('users', 'totalUsers', 'activeUsers', 'bannedUsers'));
    }
    public function create()
    {
        // لا نمرر كائن مستخدم موجود (أو نمرر كائن User جديد فارغ إذا لزم الأمر)
        return view('users.form'); // سيعمل الـ Blade كنموذج إضافة
    }

    // لمعالجة بيانات الإضافة وحفظها
    public function store(RegisterRequest $request)
    {
     
        $user = User::create([
            'phone' => $request['phone'],
            'email' => $request['email'] ?? null,
            // يجب تشفير كلمة المرور قبل الحفظ
            'password' => bcrypt($request->password),
            'type' => $request['type'],
            // يتم استقبال is_active و is_verified_id كقيم بوليانية من النموذج
            'is_active' => $request->is_active,
            'is_verified_id' => $request->has('is_verified_id') ? 1 : 0,
        ]);


        // 3. 📄 إنشاء الملف الشخصي (Profile)
        $profileData = $request->only([
            'first_name',
            'last_name',
            'national_id',
            'city',
            'area',
            'vendor_name',
            'vehicle_type',
            'vehicle_plate'
        ]);

        // نستخدم create لإنشاء سجل ملف شخصي جديد مرتبط بالمستخدم الجديد
        $profile = $user->profile()->create($profileData);


        // 4. 🖼️ معالجة ورفع الصور وحفظ المسارات
        $imageFields = [
            'profile_image' => 'profiles',
            'license_image' => 'licenses',
            'car_image' => 'cars',
            'id_image' => 'ids'
        ];

        $profileUpdates = [];

        foreach ($imageFields as $field => $directory) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store($directory, 'public');
                // حفظ المسار لتحديث سجل Profile
                $profileUpdates[$field] = $path;
            }
        }

        // تحديث سجل Profile بمسارات الصور دفعة واحدة (إذا كان هناك صور مرفوعة)
        if (!empty($profileUpdates)) {
            $profile->update($profileUpdates);
        }


        // 5. ✅ إعادة التوجيه
        // يفضل إعادة التوجيه إلى صفحة تفاصيل المستخدم (show) بعد الإضافة
        return redirect()->route('admin.users.show', $user->id)->with('success', 'تم إضافة المستخدم بنجاح وتوثيقه مبدئياً.');
    }

    public function show($id)
    {
        $user = User::with([
            'profile',
            'orders.rating',  
        ])->findOrFail($id);
        return view('users.show', compact('user'));
    }


    public function edit($id)
    {
        $user = User::with('profile')->findOrFail($id);
        return view('users.form', compact('user'));
    }
    /**
     * 📌 تعديل بيانات المستخدم
     */
    public function update(UpdateProfileRequestWeb $request, User $user)
    {
       
        // ✅ تحديث بيانات المستخدم
        $userData = [
            'phone'         => $request->input('phone'),
            'email'         => $request->input('email') ?? null,
            'type'          => $request->input('type'),
            'is_active'     => $request->input('is_active'),
            'is_verified_id' => $request->has('is_verified_id') ? 1 : 0,
        ];

        // كلمة المرور (لو اتغيرت)
        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->input('password'));
        }

        $user->update($userData);

        // ✅ تحديث أو إنشاء البروفايل
        $profileData = $request->only([
            'first_name',
            'last_name',
            'national_id',
            'city',
            'area',
            'vendor_name',
            'vehicle_type',
            'vehicle_plate'
        ]);

        $profile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        // ✅ معالجة الصور
        $imageFields = [
            'profile_image' => 'profiles',
            'license_image' => 'licenses',
            'car_image'     => 'cars',
            'id_image'      => 'ids',
        ];

        foreach ($imageFields as $field => $directory) {
            if ($request->hasFile($field)) {
                // حذف القديم
                if ($profile->$field && Storage::disk('public')->exists($profile->$field)) {
                    Storage::disk('public')->delete($profile->$field);
                }

                // رفع الجديد
                $path = $request->file($field)->store($directory, 'public');

                // تحديث العمود
                $profile->update([$field => $path]);
            }
        }

        // ✅ رجوع للصفحة مع رسالة نجاح
        return redirect()
            ->route('admin.users.show', $user->id)
            ->with('success', 'تم تحديث بيانات المستخدم بنجاح.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'تم تحديث حالة المستخدم');
    }
    public function resetOrders($id)
    {
        $user = User::findOrFail($id);

        Order::where('user_id', $user->id)->delete();

        return back()->with('success', 'تم تصفير الطلبات للمستخدم');
    }

    /**
     * 📌 حذف الحساب
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return to_route('admin.users.index')->with('success', 'تم حذف الحساب بنجاح');
    }

    /**
     * 📌 تنزيل PDF ببيانات وسجل المستخدم
     */
    public function exportPdf($id)
    {
        $user = User::with(['profile', 'orders'])->findOrFail($id);

        $pdf = Pdf::loadView('users.pdf', compact('user'));
        return $pdf->download("user_{$user->id}.pdf");
    }
}
