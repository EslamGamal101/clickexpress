<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::SendRespond(401, 'المستخدم غير مصرح له', []);
        }
        $request->validate([
            'message' => 'required|string|max:2000',
        ],[
            'user_id.exists'  => 'المستخدم غير موجود',
            'message.required' => 'الرسالة مطلوبة',
            'message.string'   => 'الرسالة يجب أن تكون نص',
            'message.max'      => 'الرسالة يجب ألا تتجاوز 2000 حرف',
        ]);

        $feedback = Feedback::create([
            'user_id' => $user->id,
            'message' => $request->message,
        ]);
        return ApiResponse::SendRespond(200, 'تم إرسال الرسالة بنجاح',$feedback);
    }
}
