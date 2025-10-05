<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $address = $this->address;

        return [
            'id'            => $this->id,
            'pickup_city'   => $address?->pickup_city,
            'pickup_area'   => $address?->pickup_area,
            'delivery_city' => $address?->delivery_city,
            'delivery_area' => $address?->delivery_area,
            'delivery_fee'  => $this->delivery_fee,
            'price'         => $this->price,
            'status'        => $this->status,
            'created_at'    => $this->created_at->format('Y-m-d H:i'),

            // بيانات صاحب الطلب
            'customer' => [
                'name'  => $this->user?->profile
                    ? $this->user->profile->first_name . ' ' . $this->user->profile->last_name
                    : $this->user?->name,
                'phone' => $this->user?->phone,
            ],
            'driver' => $this->when($this->driver_id, [
                'name'  => $this->driver?->profile
                    ? $this->driver->profile->first_name . ' ' . $this->driver->profile->last_name
                    : $this->driver?->name,
                'phone' => $this->driver?->phone,
            ]),
        ];
    }
}
