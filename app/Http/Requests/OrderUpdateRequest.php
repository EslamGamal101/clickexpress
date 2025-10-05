<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ✅ هنا بنخلي التحقق يتم جوه الكنترولر نفسه
        return true;
    }

    public function rules(): array
    {
        return [
            
            'pickup_city'       => 'sometimes|required|string|max:255',
            'pickup_area'       => 'sometimes|required|string|max:255',
            'pickup_phone'      => 'sometimes|required|string|max:20',
            'pickup_name'       => 'nullable|string|max:255',
            'pickup_latitude'   => 'nullable|numeric|between:-90,90',
            'pickup_longitude'  => 'nullable|numeric|between:-180,180',

            // عنوان التسليم
            'delivery_city'     => 'sometimes|required|string|max:255',
            'delivery_area'     => 'sometimes|required|string|max:255',
            'delivery_phone'    => 'sometimes|required|string|max:20',
            'delivery_name'     => 'nullable|string|max:255',
            'delivery_latitude' => 'nullable|numeric|between:-90,90',
            'delivery_longitude' => 'nullable|numeric|between:-180,180',

            // الطلب
            'order_type'        => 'sometimes|required|in:package,cargo',
            'delivery_type'     => 'sometimes|required|in:instant,same_day,scheduled',
            'delivery_date'     => 'required_if:delivery_type,scheduled|date|after_or_equal:today',

            // الطرد
            'package_type'      => 'sometimes|required|string|in:clothes,food,valuables,gifts,other',
            'package_other'     => 'nullable|string|max:255',

            // السعر و الملاحظات
            'price'             => 'nullable|numeric|min:0',
            'delivery_fee'      => 'sometimes|required|numeric|min:0',
            'notes'             => 'nullable|string',

            // إعدادات أخرى
            'assign_last_driver' => 'boolean',
            'vehicle_type'      => 'sometimes|required|string|max:255',
        ];
    }


    public function messages(): array
    {
        return [
            'pickup_city.required'    => 'المدينة المطلوبة للاستلام مطلوبة',
            'delivery_city.required'  => 'المدينة المطلوبة للتسليم مطلوبة',
            'order_type.in'           => 'نوع الطلب يجب أن يكون إما طرد أو حمولة',
            'package_type.in'         => 'نوع الطرد غير صحيح',
            'delivery_fee.required'   => 'أجور التوصيل مطلوبة',
            'vehicle_type.required'   => 'يجب اختيار وسيلة النقل',
        ];
    }
}
