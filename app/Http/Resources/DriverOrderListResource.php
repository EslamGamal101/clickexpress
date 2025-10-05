<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverOrderListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $address = $this->address; // العلاقة Order -> OrderAddress

        return [
            'id'            => $this->id,
            'pickup_city'   => $address?->pickup_city,
            'pickup_area'   => $address?->pickup_area,
            'delivery_city' => $address?->delivery_city,
            'delivery_area' => $address?->delivery_area,
            'delivery_fee'  => $this->delivery_fee,
            'price'         => $this->price,
            'created_at'    => $this->created_at->format('Y-m-d H:i'),
            'can_complain'  => !$this->complaint_id,
            
        ];
    }
}
