<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $base = [
            'id'        => $this->id,
            'type'      => $this->type,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];

        $profile = $this->whenLoaded('profile', function () {
            return [
                'first_name' => $this->profile->first_name ?? null,
                'last_name'  => $this->profile->last_name ?? null,
                'profile_image' => $this->profile->profile_image ?? null,
                'vendor_name'   => $this->profile->vendor_name ?? null,
                'city'          => $this->profile->city ?? null,
                'area'          => $this->profile->area ?? null,
                'vehicle_type'  => $this->profile->vehicle_type ?? null,
                'vehicle_plate' => $this->profile->vehicle_plate ?? null,
            ];
        });

        // 🔹 رجّع حسب نوع المستخدم
        switch ($this->type) {
            case 'customer':
                $base['profile'] = [
                    'first_name' => $this->profile->first_name ?? null,
                    'last_name'  => $this->profile->last_name ?? null,
                    'city'       => $this->profile->city ?? null,
                    'area'       => $this->profile->area ?? null,
                    'profile_image' => $this->profile->profile_image ?? null,
                ];
                break;

            case 'vendor':
                $base['profile'] = [
                    'first_name'  => $this->profile->first_name ?? null,
                    'last_name'   => $this->profile->last_name ?? null,
                    'vendor_name' => $this->profile->vendor_name ?? null,
                    'city'        => $this->profile->city ?? null,
                    'area'        => $this->profile->area ?? null,
                ];
                break;
            case 'management_producers':
                $base['profile'] = [
                    'first_name'  => $this->profile->first_name ?? null,
                    'last_name'   => $this->profile->last_name ?? null,
                    'city'        => $this->profile->city ?? null,
                    'area'        => $this->profile->area ?? null,
                ];
                break;

            case 'driver':
                $base['profile'] = [
                    'first_name'    => $this->profile->first_name ?? null,
                    'last_name'     => $this->profile->last_name ?? null,
                    'vehicle_type'  => $this->profile->vehicle_type ?? null,
                    'vehicle_plate' => $this->profile->vehicle_plate ?? null,
                    'city'          => $this->profile->city ?? null,
                    'area'          => $this->profile->area ?? null,

                ];
                break;

            default:
                $base['profile'] = $profile;
        }

        return $base;
    }
}
