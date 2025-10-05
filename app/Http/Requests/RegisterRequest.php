<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone'       => ['required', 'digits:9', 'regex:/^[1-9][0-9]{8}$/', 'unique:users'],
            'password'    => [
                'required',
                'string',
                'min:6',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/'
            ],
            'type'     => ['required', 'in:customer,vendor,driver,admin,management_producers'],
            'first_name'  => ['required', 'regex:/^[\p{Arabic}a-zA-Z\s]+$/u'],
            'last_name'   => ['required', 'regex:/^[\p{Arabic}a-zA-Z\s]+$/u'],
            'city'        => ['required', Rule::in([
                'عمان',
                'الزرقاء',
                'إربد',
                'العقبة',
                'البلقاء',
                'المفرق',
                'جرش',
                'مأدبا',
                'الكرك',
                'الطفيلة',
                'معان',
                'عجلون'
            ])],
            'national_id' => ['required', 'digits:10'],
            'vendor_name' => 'required_if:user_type,vendor|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.regex'    => 'رقم الهاتف يجب أن يكون من 9 أرقام ولا يبدأ بصفر.',
            'phone.digits'   => 'رقم الهاتف يجب أن يتكون من 9 أرقام.',
            'phone.unique'   => 'رقم الهاتف مستخدم بالفعل.',

            'password.required' => 'كلمة المرور مطلوبة',
            'password.min'      => 'كلمة المرور يجب أن تتكون من 6 خانات على الأقل.',
            'password.regex'    => 'كلمة المرور يجب أن تحتوي على حرف كبير، حرف صغير، رقم ورمز خاص.',

            'first_name.regex'  => 'الاسم الأول يجب أن يحتوي فقط على حروف عربية أو إنجليزية.',
            'last_name.regex'   => 'اسم العائلة يجب أن يحتوي فقط على حروف عربية أو إنجليزية.',

            'city.required' => 'المدينة مطلوبة',
            'city.in'       => 'المدينة المدخلة غير موجودة.',

            'national_id.digits' => 'الرقم الوطني يجب أن يتكون من 10 أرقام.',

            'type.required' => 'نوع المستخدم مطلوب',
            'type.in'       => 'نوع المستخدم غير صحيح.',
            'national_id.required' => 'الرقم الوطني مطلوب',
            'vendor_name.required_if' => 'اسم المتجر مطلوب للبائعين.',
        ];
    }
}
