<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ✅ خليها true عشان أي مستخدم مسجل يقدر يعمل طلب
    }

    public function rules(): array
    {
        $jordanCities = [
            'عمان',
            'الزرقاء',
            'إربد',
            'العقبة',
            'المفرق',
            'مادبا',
            'الكرك',
            'جرش',
            'عجلون',
            'الطفيلة',
            'معان',
            'البَلقاء',
            'الرصيفة',
            'السلط'
        ];
        return [
            // بيانات الاستلام
            'pickup_city'    => ['required', 'string', 'max:255', Rule::in($jordanCities)],
            'pickup_area'    => 'required|string|max:255',
            'pickup_phone'   => ['required', 'string'],
            'pickup_name'    => 'nullable|string|max:255',
            'pickup_latitude'  => 'nullable|numeric|between:-90,90',
            'pickup_longitude' => 'nullable|numeric|between:-180,180',
            // بيانات التسليم
            'delivery_city'  => ['required', 'string', 'max:255', Rule::in($jordanCities)],
            'delivery_area'  => 'required|string|max:255',
            'delivery_phone' => ['required', 'string'],
            'delivery_name'  => 'nullable|string|max:255',
            'delivery_latitude'  => 'nullable|numeric|between:-90,90',
            'delivery_longitude' => 'nullable|numeric|between:-180,180',

            'order_type'     => 'required|in:package,cargo',
            'delivery_type'  => 'required|in:instant,scheduled',
            'delivery_date' => 'required_if:delivery_type,scheduled|date|after_or_equal:today',
            'package_type'   => 'required|string|max:255',
            'package_other'  => 'nullable|string|max:255',

            'price'          => 'required|numeric',
            'delivery_fee'   => 'required|numeric',
            'notes'          => 'nullable|string',
            'assign_last_driver' => 'boolean',
            'vehicle_type'   => 'required|string|max:255',
            'user_id'        => 'nullable|exists:users,id',
            'driver_id'      => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'pickup_city.required'    => 'المدينة الخاصة بالاستلام مطلوبة',
            'pickup_area.required'    => 'المنطقة الخاصة بالاستلام مطلوبة',
            'pickup_phone.required'   => 'رقم الهاتف للاستلام مطلوب',

            'delivery_city.required'  => 'المدينة الخاصة بالتسليم مطلوبة',
            'delivery_area.required'  => 'المنطقة الخاصة بالتسليم مطلوبة',
            'delivery_phone.required' => 'رقم الهاتف للتسليم مطلوب',

            'order_type.required'     => 'نوع الطلب مطلوب',
            'order_type.in'           => 'نوع الطلب يجب أن يكون package أو cargo',
            'price.required'          => 'سعر الطلب مطلوب',
            'price.numeric'           => 'سعر الطلب يجب أن يكون رقم',
            'delivery_type.required'  => 'نوع التوصيل مطلوب',
            'delivery_type.in'        => 'نوع التوصيل يجب أن يكون instant أو scheduled',
            'delivery_date.required_if' => 'يجب تحديد تاريخ التوصيل عند اختيار نوع التوصيل scheduled',
            'delivery_date.after_or_equal' => 'تاريخ التوصيل يجب أن يكون اليوم أو بعده',
            'package_type.required'   => 'نوع الطرد مطلوب',
            'package_type.string'     => 'نوع الطرد يجب أن يكون نص',
            'package_type.max'        => 'نوع الطرد يجب ألا يزيد عن 255 حرف',
            'package_other.string'    => 'نوع الطرد الآخر يجب أن يكون نص',
            'package_other.max'       => 'نوع الطرد الآخر يجب ألا يزيد عن 255 حرف',
            
            'delivery_fee.required'   => 'رسوم التوصيل مطلوبة',
            'delivery_fee.numeric'    => 'رسوم التوصيل يجب أن تكون رقم',

            'vehicle_type.required'   => 'وسيلة النقل مطلوبة',
        ];
    }

    protected function prepareForValidation()
    {

        if ($this->order_type === 'cargo' && empty($this->notes)) {
            $this->merge(['notes' => null]); 
        }

        if ($this->package_type === 'other' && empty($this->package_other)) {
            $this->merge(['package_other' => null]); 
        }
    }
}
