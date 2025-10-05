<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $address = $this->address;

        $data = [
            'id'             => $this->id,
            'pickup_city'    => $address?->pickup_city,
            'pickup_area'    => $address?->pickup_area,
            'delivery_city'  => $address?->delivery_city,
            'delivery_area'  => $address?->delivery_area,
            'price'          => $this->price,
            'status'         => $this->status ?? 'pending',
            'tracking_code'  => $this->tracking_code,
            'created_at'     => $this->created_at?->format('Y-m-d H:i:s'),

            // صلاحيات للمستخدم
            'can_edit'   => trim(strtolower($this->status)) === 'pending',
            'can_delete' => trim(strtolower($this->status)) === 'pending',
            'can_rate'   => trim(strtolower($this->status)) === 'delivered',
        ];

        // إضافة بيانات السائق إذا الحالة مقبولة أو بعدها
        $statusesWithDriver = ['accepted', 'picked_up', 'delivered'];
        if (in_array(trim(strtolower($this->status)), $statusesWithDriver) && $this->driver) {
            $data['driver'] = [
                'id'    => $this->driver->id,
                'name'  => trim(($this->driver->profile?->first_name ?? '') . ' ' . ($this->driver->profile?->last_name ?? '')),
                'phone' => $this->driver->phone,
                'vehicle_type' => $this->driver->profile?->vehicle_type,
                'vehicle_plate' => $this->driver->profile?->vehicle_plate,
            ];
        }

        return $data;
    }
}
