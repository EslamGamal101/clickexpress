<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * تغيير كلمة المرور
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::SendRespond(401, 'المستخدم غير موجود', []);
        }
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => [
                'required',
                'string',
                'confirmed', 
                Password::min(8)        
                    ->mixedCase()       
                    ->letters()        
                    ->numbers()         
                    ->symbols()         
                    ->uncompromised(),  
            ],
        ]);

        // التحقق من كلمة المرور الحالية
        if (!Hash::check($request->current_password, $user->password)) {
            return ApiResponse::SendRespond(401, 'كلمة المرور الحالية غير صحيحة', []);
        }
        $user->password = Hash::make($request->new_password);
        $user->save();

        return ApiResponse::SendRespond(200, 'تم تغيير كلمة المرور بنجاح', []);
    }
}
