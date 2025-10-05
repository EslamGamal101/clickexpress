<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrderController extends Controller
{
    /**
     * عرض جميع الطلبات مع البحث والفلترة.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'driver', 'address', 'rating']);
        if ($request->filled('user_search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->user_search}%")
                    ->orWhere('phone', 'like', "%{$request->user_search}%");
            });
        }
        if ($request->filled('driver_search')) {
            $query->whereHas('driver', function ($q) use ($request) {
                // نفترض أن نموذج User يحتوي على عمود 'name' و 'phone'
                $q->where('name', 'like', "%{$request->driver_search}%")
                    ->orWhere('phone', 'like', "%{$request->driver_search}%");
            });
        }
        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }
        if ($request->filled('city')) {
            $query->whereHas('address', function ($q) use ($request) {
                $q->where('pickup_city', $request->city)
                    ->orWhere('delivery_city', $request->city);
            });
        }
        $orders = $query->latest()->paginate(10);

       
        if ($orders->isEmpty() && $request->hasAny(['user_search', 'driver_search', 'created_from', 'created_to', 'city'])) {
            return view('admin.orders.index', [    
                'orders' => $orders,
                'noResultsMessage' => '❌ لم يتم العثور على أي طلبات تطابق معايير البحث والفلترة.',
            ]);
        }

        return view('orders.index', compact('orders'));
    }


    /**
     * عرض تفاصيل الطلب.
     */
    

    /**
     * إنشاء طلب جديد (عرض الفورم).
     */
    public function create()
    {
        $users = User::where('type','!=', 'driver')->get();
        $drivers = User::where('type', 'driver')->get();

        return view('orders.form', compact('users', 'drivers'));
    }

    /**
     * تخزين الطلب الجديد.
     */

    public function store(OrderStoreRequest $request)
    {
      
        try {
            return DB::transaction(function () use ($request) {

                // ✅ 1. التحقق من القيم بعد الفالديشن
                $validated = $request->validated();

                // ✅ 2. جلب المستخدم اللي تم اختياره في الفورم
                $user = User::findOrFail($validated['user_id']);
                
                // ✅ 3. تجهيز بيانات الطلب
                $orderData = $validated;
                $orderData['serial_number'] = 'SN-' . strtoupper(uniqid());
                $orderData['tracking_code'] = 'TR-' . strtoupper(uniqid());
              
                // ✅ إزالة بيانات العناوين قبل إنشاء الطلب
                unset(
                    $orderData['pickup_city'],
                    $orderData['pickup_area'],
                    $orderData['pickup_phone'],
                    $orderData['pickup_name'],
                    $orderData['pickup_latitude'],
                    $orderData['pickup_longitude'],
                    $orderData['delivery_city'],
                    $orderData['delivery_area'],
                    $orderData['delivery_phone'],
                    $orderData['delivery_name'],
                    $orderData['delivery_latitude'],
                    $orderData['delivery_longitude']
                );
                
                // ✅ 4. إنشاء الطلب للمستخدم المحدد
                $order = $user->orders()->create($orderData);
               
                // ✅ 5. إنشاء العنوان المرتبط بالطلب
                $addressData = [
                    'order_id'          => $order->id,
                    'pickup_city'       => $validated['pickup_city'],
                    'pickup_area'       => $validated['pickup_area'],
                    'pickup_phone'      => $validated['pickup_phone'],
                    'pickup_name'       => $validated['pickup_name'] ?? null,
                    'pickup_latitude'   => $validated['pickup_latitude'] ?? null,
                    'pickup_longitude'  => $validated['pickup_longitude'] ?? null,
                    'delivery_city'     => $validated['delivery_city'],
                    'delivery_area'     => $validated['delivery_area'],
                    'delivery_phone'    => $validated['delivery_phone'],
                    'delivery_name'     => $validated['delivery_name'] ?? null,
                    'delivery_latitude' => $validated['delivery_latitude'] ?? null,
                    'delivery_longitude' => $user->id,
                    'driver_id'        => $validated['driver_id'] ?? null,
                ];
               

                $order->address()->create($addressData);
               
                // ✅ 6. تحديث الطلب بعد الإنشاء (عشان العنوان يتربط)
                $order->refresh();
               
                // ✅ 7. توجيه للصفحة مع رسالة نجاح
                return redirect()
                    ->route('admin.orders.show', $order->id)
                    ->with('success', 'تم إنشاء الطلب بنجاح 🎉');
            });
        } catch (Throwable $e) {
            dd($e);
            dd($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }



    public function show($id)
    {
        $order = Order::with(['user', 'driver', 'address'])->findOrFail($id);

        return view('orders.show', compact('order'));
    }
    public function edit($id)
    {
        $order = Order::with('address')->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    /**
     * تحديث بيانات الطلب.
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // ✅ التحقق من البيانات
        $validated = $request->validate([
            'order_type' => 'nullable|string|max:255',
            'delivery_type' => 'nullable|string|max:255',
            'delivery_date' => 'nullable|date',
            'vehicle_type' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'delivery_fee' => 'nullable|numeric',
            'status' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',

            // عناوين الطلب
            'pickup_city' => 'nullable|string|max:255',
            'pickup_area' => 'nullable|string|max:255',
            'pickup_name' => 'nullable|string|max:255',
            'pickup_phone' => 'nullable|string|max:20',
            'delivery_city' => 'nullable|string|max:255',
            'delivery_area' => 'nullable|string|max:255',
            'delivery_name' => 'nullable|string|max:255',
            'delivery_phone' => 'nullable|string|max:20',
        ]);

        // ✅ تحديث بيانات الطلب
        $order->update($validated);

        // ✅ تحديث أو إنشاء العنوان
        if ($order->address) {
            $order->address->update($validated);
        } else {
            $order->address()->create($validated);
        }

        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'تم تحديث بيانات الطلب بنجاح.');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'تم حذف الطلب بنجاح 🗑️');
    }

    /**
     * إلغاء الطلب.
     */
    public function cancel($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'cancelled']);

        return redirect()->route('admin.orders.index')->with('success', 'تم إلغاء الطلب 🚫');
    }
}
