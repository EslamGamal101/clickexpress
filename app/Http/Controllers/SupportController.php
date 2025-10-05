<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupportInfo;
use App\Helpers\ApiResponse;
use App\Models\AppInfo;

class SupportController extends Controller
{
    // GET /support
    public function info()
    {
        // نفترض إن عندنا سجل واحد بس بيحتوي بيانات الدعم
        $support = AppInfo::first();

        if (!$support) {
            return ApiResponse::SendRespond(404, 'لا توجد بيانات للدعم حالياً', null);
        }

        $data = [
            'terms'        => $support->terms,
            'about_us'     => $support->about_us,
            'whatsapp'  => $support->whatsapp,
            'facebook'  => $support->facebook,
            'instagram' => $support->instagram,
        ];

        return ApiResponse::SendRespond(200, 'بيانات الدعم', $data);
    }

    // POST /support/update  (للتحديث من لوحة تحكم مثلاً)
    // public function update(Request $request)
    // {
    //     $request->validate([
    //         'terms'        => 'nullable|string',
    //         'about_us'     => 'nullable|string',
    //         'social_links' => 'nullable|array', // لازم يجي Array
    //     ]);

    //   //  $support = info::firstOrNew(); // ياخد السجل الأول أو يعمل واحد جديد

    //     if ($request->has('terms')) {
    //         $support->terms = $request->terms;
    //     }

    //     if ($request->has('about_us')) {
    //         $support->about_us = $request->about_us;
    //     }

    //     if ($request->has('social_links')) {
    //         $support->social_links = json_encode($request->social_links, JSON_UNESCAPED_UNICODE);
    //     }

    //     $support->save();

    //     return ApiResponse::SendRespond(200, 'تم تحديث بيانات الدعم بنجاح', [
    //         'terms'        => $support->terms,
    //         'about_us'     => $support->about_us,
    //         'social_links' => json_decode($support->social_links, true)
    //     ]);
    // }
}
