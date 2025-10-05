<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userType = $this->user?->type; // نجيب نوع المستخدم

        switch ($userType) {
            case 'customer':
                return [
                    'first_name' => $this->first_name,
                    'last_name'  => $this->last_name,
                    'city'       => $this->city,
                    'area'       => $this->area,
                    'profile_image' => $this->profile_image,
                ];

            case 'vendor':
                return [
                    'first_name'    => $this->first_name,
                    'last_name'     => $this->last_name,
                    'vendor_name' => $this->vendor_name,
                    'city'        => $this->city,
                    'area'        => $this->area,
                    'profile_image' => $this->profile_image,
                ];

            case 'driver':
                return [
                    'first_name'    => $this->first_name,
                    'last_name'     => $this->last_name,
                    'city'       => $this->city,
                    'area'       => $this->area,
                    'vehicle_type'  => $this->vehicle_type,
                    'vehicle_plate' => $this->vehicle_plate,  
                    'profile_image' => $this->profile_image,
                ];
            case 'management_producers':
                return [
                    'first_name'    => $this->first_name,
                    'last_name'     => $this->last_name,
                    'profile_image' => $this->profile_image,
                    'city'       => $this->city,
                    'area'       => $this->area,
                ];

            case 'admin':
                return [
                    'first_name' => $this->first_name,
                    'last_name'  => $this->last_name,
                ];

            default:
                return []; 
        }
    }
}
