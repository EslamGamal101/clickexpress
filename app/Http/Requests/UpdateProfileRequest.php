<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        // السماح للمستخدمين المصدق عليهم
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();
        $profile = $user->profile;

        return [
            'first_name'    => 'nullable|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:255',
            'area'          => 'nullable|string|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => [
                'nullable',
                'digits:9',
                'regex:/^[1-9][0-9]{8}$/',
                 Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'national_id'   => [
                'nullable',
                'digits:10',
                
                Rule::unique('profiles', 'national_id')->ignore($profile->id),
            ],
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'vehicle_type'  => 'nullable|string|max:255',
            'vehicle_plate' => 'nullable|string|max:255',
            'work_place'    => 'nullable|string|max:255',
            'car_image'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'license_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'vendor_name'  => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.unique'       => 'رقم الهاتف مستخدم من قبل',
            'national_id.unique' => 'رقم الهوية مستخدم من قبل',
            'profile_image.image' => 'يجب أن يكون الملف صورة',
            'car_image.image'    => 'يجب أن يكون الملف صورة',
            'license_image.image' => 'يجب أن يكون الملف صورة',
        ];
    }
}
