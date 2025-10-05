<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'type'        => $this->type,
            'is_active'   => (bool) $this->is_active,

            'profile' => $this->whenLoaded('profile', function () {
                return [
                    'first_name'  => $this->profile->first_name ?? null,
                    'last_name'   => $this->profile->last_name ?? null,
                    'vendor_name' => $this->profile->vendor_name ?? null,
                    'avatar'      => $this->profile->avatar ?? null,
                ];
            }),

            'driver_info' => $this->when($this->type === 'driver' && $this->relationLoaded('profile'), function () {
                return [
                    'vehicle_type'  => $this->profile->vehicle_type ?? null,
                    'vehicle_plate' => $this->profile->vehicle_plate ?? null,
                    'work_place'    => $this->profile->work_place ?? null,
                    'car_image'     => $this->profile->car_image ?? null,
                    'license_image' => $this->profile->license_image ?? null,
                ];
            }),
        ];
    }
}
