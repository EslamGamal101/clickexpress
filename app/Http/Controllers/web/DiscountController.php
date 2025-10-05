<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class DiscountController extends Controller
{
    /**
     * ✅ عرض كل الخصومات (مع فلترة وبحث)
     */
    public function index(Request $request)
    {
        $query = Discount::with(['admin.profile', 'driver.profile', 'package']);

        // 🔍 البحث باسم أو بريد الأدمن
        if ($request->filled('admin_query')) {
            $query->whereHas('admin.profile', function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->admin_query}%")
                    ->orWhere('last_name', 'like', "%{$request->admin_query}%");
            })->orWhereHas('admin', function ($q) use ($request) {
                $q->where('email', 'like', "%{$request->admin_query}%");
            });
        }

        // 🔍 البحث باسم السائق
        if ($request->filled('driver_query')) {
            $query->whereHas('driver.profile', function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->driver_query}%")
                    ->orWhere('last_name', 'like', "%{$request->driver_query}%");
            });
        }

        // 🎉 فلترة حسب المناسبة
        if ($request->filled('occasion')) {
            $query->where('occasion', $request->occasion);
        }

        // 📅 فلترة حسب تاريخ الإنشاء
        if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }

        $discounts = $query->latest()->paginate(10);

        return view('discounts.index', compact('discounts'));
    }

    /**
     * ✅ عرض صفحة إنشاء خصم جديد
     */
    public function create()
    {
        $drivers = User::where('type', 'driver')->get();
        $packages = Package::where('is_active', true)->get();

        return view('discounts.create', compact('drivers', 'packages'));
    }

    /**
     * ✅ حفظ الخصم الجديد في قاعدة البيانات
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,amount',
            'value'         => 'required|numeric|min:0',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'target_type'   => 'required|in:all_drivers,specific_driver,specific_package',
            'driver_id'     => 'nullable|exists:users,id',
            'package_id'    => 'nullable|exists:packages,id',
        ]);

        $discount = Discount::create([
            'admin_id'      => Auth::id(),
            'title'         => $request->title,
            'discount_type' => $request->discount_type,
            'value'         => $request->value,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'occasion'      => $request->occasion,
            'target_type'   => $request->target_type,
            'driver_id'     => $request->driver_id,
            'package_id'    => $request->package_id,
        ]);

        // 🔔 إرسال إشعار للسائقين أو المستخدمين
        $this->sendDiscountNotification($discount);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'تم إنشاء الخصم بنجاح ✅');
    }

    /**
     * ✅ تعديل الخصم
     */
    public function edit($id)
    {
        $discount = Discount::findOrFail($id);
        $drivers = User::where('type', 'driver')->get();
        $packages = Package::where('is_active', true)->get();

        return view('admin.discounts.edit', compact('discount', 'drivers', 'packages'));
    }

    /**
     * ✅ تحديث بيانات الخصم
     */
    public function update(Request $request, $id)
    {
        $discount = Discount::findOrFail($id);

        $request->validate([
            'title'         => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,amount',
            'value'         => 'required|numeric|min:0',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'target_type'   => 'required|in:all_drivers,specific_driver,specific_package',
            'driver_id'     => 'nullable|exists:users,id',
            'package_id'    => 'nullable|exists:packages,id',
        ]);

        $discount->update([
            'title'         => $request->title,
            'discount_type' => $request->discount_type,
            'value'         => $request->value,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'occasion'      => $request->occasion,
            'target_type'   => $request->target_type,
            'driver_id'     => $request->driver_id,
            'package_id'    => $request->package_id,
        ]);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'تم تعديل الخصم بنجاح ✏️');
    }

    /**
     * 🗑️ حذف الخصم
     */
    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();

        return redirect()->route('admin.discounts.index')
            ->with('success', 'تم حذف الخصم بنجاح 🗑️');
    }

    /**
     * 🔔 إرسال إشعارات عند إنشاء الخصم
     */
    protected function sendDiscountNotification(Discount $discount)
    {
        try {
            switch ($discount->target_type) {
                case 'all_drivers':
                    $drivers = User::where('type', 'driver')->get();
                    foreach ($drivers as $driver) {
                        $driver->notifications()->create([
                            'title' => 'عرض جديد 🎉',
                            'message' => "خصم جديد متاح: {$discount->title}",
                            'type' => 'discount',
                        ]);
                    }
                    break;

                case 'specific_driver':
                    if ($discount->driver) {
                        $discount->driver->notifications()->create([
                            'title' => 'خصم خاص بك 🎁',
                            'message' => "تم إضافة خصم خاص لك: {$discount->title}",
                            'type' => 'discount',
                        ]);
                    }
                    break;

                case 'specific_package':
                    $drivers = User::where('type', 'driver')->get();
                    foreach ($drivers as $driver) {
                        $driver->notifications()->create([
                            'title' => 'عرض جديد على الباقات 📦',
                            'message' => "تم عمل خصم على باقة {$discount->package->name}",
                            'type' => 'discount',
                        ]);
                    }
                    break;
            }
        } catch (\Throwable $e) {
            Log::error('فشل إرسال الإشعار:', ['error' => $e->getMessage()]);
        }
    }
}
