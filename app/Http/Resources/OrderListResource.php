<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $status = strtolower(trim((string) $this->status));

        return [
            'id'             => $this->id,
            'pickup_city'    => $this->pickup_city,
            'pickup_area'    => $this->pickup_area,
            'pickup_phone'   => $this->pickup_phone,
            'pickup_name'    => $this->pickup_name,
            'delivery_city'  => $this->delivery_city,
            'delivery_area'  => $this->delivery_area,
            'delivery_phone' => $this->delivery_phone,
            'delivery_name'  => $this->delivery_name,

            'order_type'     => $this->order_type,
            'delivery_type'  => $this->delivery_type,
            'package_type'   => $this->package_type,
            'package_other'  => $this->package_other,
            'vehicle_type'   => $this->vehicle_type,

            'price'          => $this->price,
            'delivery_fee'   => $this->delivery_fee,
            'notes'          => $this->notes,
            'status'         => $status,
            'created_at'     => $this->created_at?->format('Y-m-d H:i'),

            // ✅ الصلاحيات
            'can_edit'   => $status === 'pending',
            'can_delete' => $status === 'pending',
            'can_rate'   => $status === 'delivered' && !$this->rate,

            // ✅ بيانات السائق (لو عندك جدول drivers أو عمود للسائق)
            'driver' => $this->when(
                in_array($status, ['accepted', 'in_vehicle', 'delivered']),
                fn() => [
                    'name'   => $this->driver?->first_name . ' ' . $this->driver?->last_name,
                    'phone'  => $this->user?->phone, // رقم التليفون موجود في جدول users
                    'avatar' => $this->driver?->profile_image,
                    'vehicle_type' => $this->driver?->vehicle_type,
                    'vehicle_plate' => $this->driver?->vehicle_plate,   
                ]
            ),
        ];
    }
}
