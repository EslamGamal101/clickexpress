<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequestWeb extends FormRequest
{
    public function authorize(): bool
    {
        // السماح بالوصول للـ Request (ممكن تضيف شرط لو عايز تحكم)
        return true;
    }

    public function rules(): array
    {
        // المستخدم اللي جاي من الروت (Route Model Binding)
        $user = $this->route('user');
        $profile = $user?->profile;   // null-safe operator (لو مفيش بروفايل)

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
                Rule::unique('users', 'phone')->ignore($user?->id),
            ],
            'national_id'   => [
                'nullable',
                'digits:10',
            ],
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'vehicle_type'  => 'nullable|string|max:255',
            'vehicle_plate' => 'nullable|string|max:255',
            'work_place'    => 'nullable|string|max:255',
            'car_image'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'license_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'vendor_name'   => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.unique'        => 'رقم الهاتف مستخدم من قبل',
            'phone.digits'        => 'رقم الهاتف يجب أن يكون 9 أرقام',
            'phone.regex'         => 'صيغة رقم الهاتف غير صحيحة',
            'national_id.unique'  => 'رقم الهوية مستخدم من قبل',
            'national_id.digits'  => 'رقم الهوية يجب أن يكون 10 أرقام',
            'profile_image.image' => 'يجب أن يكون الملف صورة',
            'car_image.image'     => 'يجب أن يكون الملف صورة',
            'license_image.image' => 'يجب أن يكون الملف صورة',
        ];
    }
}
