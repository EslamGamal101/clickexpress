<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Throwable;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $status = $request->query('status');

        // الحالات المسموح بها
        $allowedStatuses = ['pending', 'accepted', 'picked_up', 'delivered', 'cancelled'];

        $query = $user->orders();

        if ($status && !in_array($status, $allowedStatuses)) {
            return ApiResponse::SendRespond(
                400,
                'الحالة غير صحيحة',
                ['allowed_statuses' => $allowedStatuses]
            );
        }
        // فلترة حسب الحالة إذا كانت موجودة وصحيحة
        if ($status && in_array($status, $allowedStatuses)) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->get();

        return ApiResponse::SendRespond(
            200,
            'تم جلب الطلبات بنجاح',
            OrderResource::collection($orders)
        );
    }


    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return ApiResponse::SendRespond(403, 'غير مصرح لك برؤية هذا الطلب', []);
        }
        return ApiResponse::SendRespond(200, 'تفاصيل الطلب', new OrderResource($order));
    }

    public function store(OrderStoreRequest $request)
    {
        
        try {
            return DB::transaction(function () use ($request) {
                $user = $request->user();
                $validated = $request->validated();
                // تحقق من القيم المطلوبة
                if ($validated['order_type'] === 'cargo' && empty($validated['notes'])) {
                    return ApiResponse::SendRespond(422, 'الملاحظات مطلوبة عند اختيار حمولة', []);
                }
                if (isset($validated['package_type']) && $validated['package_type'] === 'other' && empty($validated['package_other'])) {
                    return ApiResponse::SendRespond(422, 'يجب تحديد نوع الطرد في حالة اختيار أخرى', []);
                }
                if ($validated['delivery_type'] === 'scheduled' && empty($validated['delivery_date'])) {
                    $validated['delivery_date'] = now()->toDateString();
                }
                $total = $request->price;
                if ($user->type === 'customer' && $total > 20) {
                    if (empty($user->profile->id_image)) {
                        return ApiResponse::SendRespond(422, 'يجب رفع صورة الهوية قبل نشر طلبات تزيد عن 20 دينار', []);
                    } elseif (!$user->is_verified_id) {
                        return ApiResponse::SendRespond(422, 'يجب توثيق الهوية قبل نشر طلبات تزيد عن 20 دينار', []);
                    }
                }

                // لو Vendor أو Management Producers
                if (in_array($user->type, ['vendor', 'management_producers']) && $total > 50) {
                    if (empty($user->profile->commercial_record) && empty($user->profile->id_image)) {
                        return ApiResponse::SendRespond(422, 'يجب رفع السجل التجاري أو الهوية لاعتماد الطلبات التي تزيد عن 50 دينار', []);
                    } elseif (!$user->is_verified_id) {
                        return ApiResponse::SendRespond(422, 'يجب توثيق الهوية قبل نشر طلبات تزيد عن 50 دينار', []);
                    }
                }
                // تجهيز بيانات الطلب
                $orderData = $validated;
                $orderData['serial_number'] = $validated['serial_number'] ?? 'SN-' . strtoupper(uniqid());
                $orderData['tracking_code'] = $validated['tracking_code'] ?? 'TR-' . strtoupper(uniqid());

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

                // إنشاء الطلب
                $order = $user->orders()->create($orderData);

                // إنشاء العنوان
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
                    'delivery_longitude' => $validated['delivery_longitude'] ?? null,
                ];

                $order->address()->create($addressData);
                $order->refresh();

                return ApiResponse::SendRespond(201, 'تم إنشاء الطلب بنجاح', new OrderResource($order->load('address')));
            });
        } catch (Throwable $e) {
            // لو حصل أي خطأ
            return ApiResponse::SendRespond(500, 'حصل خطأ أثناء إنشاء الطلب', [
                'error' => $e->getMessage()
            ]);
        }
    }




    public function update(OrderUpdateRequest $request, Order $order)
    {
        try {
            return DB::transaction(function () use ($request, $order) {
                $user = $request->user();

                // تحقق من ملكية الطلب
                if ($order->user_id !== $user->id) {
                    return ApiResponse::SendRespond(403, 'غير مصرح لك بتعديل هذا الطلب', []);
                }

                // تحقق من الحالة
                if ($order->status !== 'pending') {
                    return ApiResponse::SendRespond(422, 'لا يمكن تعديل الطلب بعد قبوله', []);
                }

                $validated = $request->validated();

                // تحقق من شروط الطلب
                if (($validated['order_type'] ?? $order->order_type) === 'cargo' &&
                    empty($validated['notes']) &&
                    empty($order->notes)
                ) {
                    return ApiResponse::SendRespond(422, 'الملاحظات مطلوبة عند اختيار حمولة', []);
                }

                if (($validated['package_type'] ?? $order->package_type) === 'other' &&
                    empty($validated['package_other']) &&
                    empty($order->package_other)
                ) {
                    return ApiResponse::SendRespond(422, 'يجب تحديد نوع الطرد في حالة اختيار أخرى', []);
                }

                // ✅ تحديث بيانات الطلب الأساسية
                $order->update([
                    'order_type'       => $validated['order_type']       ?? $order->order_type,
                    'delivery_type'    => $validated['delivery_type']    ?? $order->delivery_type,
                    'delivery_date'    => $validated['delivery_date']    ?? $order->delivery_date,
                    'package_type'     => $validated['package_type']     ?? $order->package_type,
                    'package_other'    => $validated['package_other']    ?? $order->package_other,
                    'price'            => $validated['price']            ?? $order->price,
                    'delivery_fee'     => $validated['delivery_fee']     ?? $order->delivery_fee,
                    'notes'            => $validated['notes']            ?? $order->notes,
                    'assign_last_driver' => $validated['assign_last_driver'] ?? $order->assign_last_driver,
                    'vehicle_type'     => $validated['vehicle_type']     ?? $order->vehicle_type,
                ]);

                // ✅ تحديث العنوان
                if ($order->address) {
                    $order->address()->update([
                        'pickup_city'       => $validated['pickup_city']       ?? $order->address->pickup_city,
                        'pickup_area'       => $validated['pickup_area']       ?? $order->address->pickup_area,
                        'pickup_phone'      => $validated['pickup_phone']      ?? $order->address->pickup_phone,
                        'pickup_name'       => $validated['pickup_name']       ?? $order->address->pickup_name,
                        'pickup_latitude'   => $validated['pickup_latitude']   ?? $order->address->pickup_latitude,
                        'pickup_longitude'  => $validated['pickup_longitude']  ?? $order->address->pickup_longitude,
                        'delivery_city'     => $validated['delivery_city']     ?? $order->address->delivery_city,
                        'delivery_area'     => $validated['delivery_area']     ?? $order->address->delivery_area,
                        'delivery_phone'    => $validated['delivery_phone']    ?? $order->address->delivery_phone,
                        'delivery_name'     => $validated['delivery_name']     ?? $order->address->delivery_name,
                        'delivery_latitude' => $validated['delivery_latitude'] ?? $order->address->delivery_latitude,
                        'delivery_longitude' => $validated['delivery_longitude'] ?? $order->address->delivery_longitude,
                    ]);
                }

                $order->refresh();

                return ApiResponse::SendRespond(200, 'تم تحديث الطلب بنجاح', new OrderResource($order));
            });
        } catch (Throwable $e) {
            return ApiResponse::SendRespond(500, 'حصل خطأ أثناء تحديث الطلب', [
                'error' => $e->getMessage(),
            ]);
        }
    }




    public function destroy(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return ApiResponse::SendRespond(403, 'غير مصرح لك بحذف هذا الطلب', []);
        }

        if ($order->status !== 'pending') {
            return ApiResponse::SendRespond(422, 'لا يمكن حذف الطلب بعد قبوله', []);
        }

        $order->delete();
        return ApiResponse::SendRespond(200, 'تم حذف الطلب بنجاح', []);
    }

    public function status(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return ApiResponse::SendRespond(404, 'الطلب غير موجود', []);
        }

        if ($order->user_id !== $request->user()->id) {
            return ApiResponse::SendRespond(403, 'غير مصرح لك بمشاهدة حالة هذا الطلب', []);
        }

        // البيانات الأساسية
        $data = [
            'order_id' => $order->id,
            'status' => $order->status
        ];

        // إضافة بيانات السائق لو الحالة مش pending
        if ($order->status !== 'pending' && $order->driver) {
            $data['driver'] = [
                'id' => $order->driver->id,
                'name' => $order->driver->profile->first_name . ' ' . $order->driver->profile->last_name,
                'phone' => $order->driver->phone
            ];
        }

        return ApiResponse::SendRespond(
            200,
            'تم استرجاع حالة الطلب بنجاح',
            $data
        );
    }

    public function increaseFee(Request $request, $id)
    {
        // التحقق من وجود الطلب
        $order = Order::find($id);
        if (!$order) {
            return ApiResponse::SendRespond(404, 'الطلب غير موجود', []);
        }

        // التأكد أن الطلب يخص المستخدم الحالي
        if ($order->user_id !== $request->user()->id) {
            return ApiResponse::SendRespond(403, 'غير مصرح لك بتعديل هذا الطلب', []);
        }

        // التحقق من المدخلات
        $request->validate(
            [
                'new_price' => 'required|numeric|min:1'
            ],
            [
                'new_price.required' => 'يرجى إدخال السعر الجديد',
                'new_price.numeric' => 'يجب أن يكون السعر الجديد رقماً',
                'new_price.min' => 'يجب أن يكون السعر الجديد على الأقل 1'
            ]
        );


        // زيادة السعر (price)
        $order->price = $request->new_price;
        $order->save();

        return ApiResponse::SendRespond(200, 'تم زيادة السعر بنجاح', [
            'order_id' => $order->id,
            'new_price' => $order->price
        ]);
    }
    public function reassignDriver(Request $request, $order_id)
    {
        $order = Order::find($order_id);
        if ($order->user_id !== $request->user()->id) {
            return ApiResponse::SendRespond(403, 'غير مصرح لك بالتعديل على هذا الطلب', []);
        }
        if (!$order) {
            return ApiResponse::SendRespond(404, 'الطلب غير موجود', []);
        }
        // تأكد أن الطلب لم يتم تسليمه أو إلغاؤه
        if (in_array($order->status, ['delivered', 'cancelled'])) {
            return ApiResponse::SendRespond(403, 'لا يمكن إعادة تعيين السائق لهذا الطلب', []);
        }

        // إعادة الطلب إلى حالة جديدة
        $order->status = 'pending';
        $order->driver_id = null; // إزالة السائق الحالي
        $order->save();

        return ApiResponse::SendRespond(200, 'تم إرجاع الطلب إلى حالة جديدة لإعادة تعيين سائق', [
            'order_id' => $order->id,
            'status' => $order->status,
            'driver_id' => $order->driver_id
        ]);
    }
}
