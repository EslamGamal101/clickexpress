<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\DriverCurrentOrderResource;
use App\Http\Resources\DriverOrderListResource;
use App\Http\Resources\DriverOrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class DriverOrderController extends Controller
{

    // public function availableOrders(Request $request)
    // {
    //     $user = $request->user();

    //     if ($user->type !== 'driver') {
    //         return ApiResponse::SendRespond(
    //             403,
    //             'فقط السائقين يمكنهم عرض الطلبات المتاحة',
    //             []
    //         );
    //     }

    //     $city = $request->query('city');

    //     $query = Order::with('address')
    //         ->where('status', 'pending')
    //         ->where('delivery_type', '!=', 'instant');

    //     if ($city) {
    //         $query->whereHas('address', function ($q) use ($city) {
    //             $q->where('pickup_city', $city);
    //         });
    //     }

    //     // Pagination
    //     $orders = $query->latest()->paginate(10); 
    //     $data = [
    //         'orders' => DriverOrderListResource::collection($orders),
    //         'meta' => [
    //             'current_page'   => $orders->currentPage(),
    //             'last_page'      => $orders->lastPage(),
    //             'per_page'       => $orders->perPage(),
    //             'total'          => $orders->total(),
    //             'next_page_url'  => $orders->nextPageUrl(),
    //             'prev_page_url'  => $orders->previousPageUrl(),
    //             'from'           => $orders->firstItem(),
    //             'to'             => $orders->lastItem(),
    //         ],
    //     ];

    //     return ApiResponse::SendRespond(
    //         200,
    //         'قائمة الطلبات المتاحة',
    //         $data
    //     );
    // }
    public function availableOrders(Request $request)
    {
        $type = $request->query('type'); // ممكن يكون customer, vendor, management_producers

        // الإحصائيات
        $stats = [
            'customer_orders'    => Order::whereHas('user', fn($q) => $q->where('type', 'customer'))->count(),
            'vendor_orders'      => Order::whereHas('user', fn($q) => $q->where('type', 'vendor'))->count(),
            'management_orders'  => Order::whereHas('user', fn($q) => $q->where('type', 'management_producers'))->count(),
        ];

        $ordersData = null;

        if ($type && in_array($type, ['customer', 'vendor', 'management_producers'])) {
            $orders = Order::with('user', 'address')
                ->whereHas('user', fn($q) => $q->where('type', $type))
                ->latest()
                ->paginate(10);

            $ordersData = [
                'orders' => DriverOrderListResource::collection($orders),
                'meta' => [
                    'current_page'   => $orders->currentPage(),
                    'last_page'      => $orders->lastPage(),
                    'per_page'       => $orders->perPage(),
                    'total'          => $orders->total(),
                    'next_page_url'  => $orders->nextPageUrl(),
                    'prev_page_url'  => $orders->previousPageUrl(),
                    'from'           => $orders->firstItem(),
                    'to'             => $orders->lastItem(),
                ],
            ];
        }

        return ApiResponse::SendRespond(200, 'لوحة الطلبات', [
            'stats' => $stats,
            'orders' => $ordersData,
        ]);
    }

    public function acceptOrder(Request $request, $orderId)
    {
        $user = $request->user();

        if (!$user->profile || !$user->profile->vehicle_type) {
            return ApiResponse::SendRespond(403, 'هذا المستخدم ليس سائقًا', []);
        }

        // جلب الطلب من جدول orders فقط، مع العلاقة address
        $order = Order::with('address')
            ->where('status', 'pending')
            ->where('delivery_type', '!=', 'schedula')
            ->find($orderId);

        if (!$order || !$order->address) {
            return ApiResponse::SendRespond(404, 'الطلب غير موجود أو تم قبوله من سائق آخر', []);
        }

        // حساب عدد الطلبات بنفس المنطقة بناءً على جدول order_addresses
        $sameAreaOrders = Order::where('driver_id', $user->id)
            ->whereHas('address', function ($query) use ($order) {
                $query->where('pickup_city', $order->address->pickup_city);
            })
            ->whereIn('status', ['accepted', 'in_vehicle'])
            ->count();

        if ($sameAreaOrders >= 3) {
            return ApiResponse::SendRespond(422, 'لا يمكنك قبول أكثر من 3 طلبات من نفس المنطقة', []);
        }

        // تحديث الطلب
        $order->update([
            'status'    => 'accepted',
            'driver_id' => $user->id,
        ]);

        return ApiResponse::SendRespond(
            200,
            'تم قبول الطلب بنجاح',
            new DriverOrderResource($order)
        );
    }

    public function complain(Request $request, Order $order)
    {
        $driver = $request->user();
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);
        if ($driver->type !== 'driver') {
            return ApiResponse::SendRespond(403, 'غير مصرح لك', []);
        }
        if ($order->status !== 'pending') {
            return ApiResponse::SendRespond(422, 'لا يمكن الاعتراض على هذا الطلب', []);
        }
        if ($order->complaint) {
            return ApiResponse::SendRespond(422, 'تم الاعتراض على هذا الطلب مسبقًا', []);
        }
        $complaint = $order->complaint()->create([
            'driver_id' => $driver->id,
            'reason'    => $request->input('reason'),
        ]);

        return ApiResponse::SendRespond(201, 'تم تسجيل الاعتراض على السعر', $complaint);
    }

    public function currentOrders(Request $request)
    {
        $driver = $request->user();

        $orders = Order::where('driver_id', $driver->id)
            ->whereIn('status', ['accepted', 'picked_up'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return ApiResponse::SendRespond(
            200,
            'الطلبات الحالية',
            DriverCurrentOrderResource::collection($orders)
                ->additional([
                    'meta' => [
                        'current_page' => $orders->currentPage(),
                        'last_page'    => $orders->lastPage(),
                        'per_page'     => $orders->perPage(),
                        'total'        => $orders->total(),
                    ]
                ])
        );
    }

    public function confirmPickup(Request $request, Order $order)
    {
        $driver = $request->user();
        if ($order->driver_id !== $driver->id) {
            return ApiResponse::SendRespond(403, 'غير مصرح', []);
        }
        if ($request->tracking_code !== $order->tracking_code) {
            return ApiResponse::SendRespond(422, 'الكود غير صحيح', []);
        }

        // تحديث الحالة
        $order->update(['status' => 'picked_up']);

        return ApiResponse::SendRespond(200, 'تم تأكيد استلام الطرد', new DriverOrderResource($order));
    }
    public function confirmDelivery(Request $request, Order $order)
    {
        $driver = $request->user();
        if ($order->driver_id !== $driver->id) {
            return ApiResponse::SendRespond(403, 'غير مصرح', []);
        }
        
        $order->update(['status' => 'delivered']);

        return ApiResponse::SendRespond(200, 'تم تأكيد تسليم الطرد', new DriverOrderResource($order));
    }
}
