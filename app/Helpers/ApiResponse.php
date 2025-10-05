<?php
namespace App\Helpers;


class ApiResponse
{
    static function SendRespond($code,$message,$data)
    {
        $response=[
            'status'=>$code,
            'msg'=>$message,
            'data'=>$data,
        ];
        return response()->json($response,$code);
    }


}