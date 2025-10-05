<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\DriverLocation;
use Illuminate\Http\Request;

class DriverLocationController extends Controller
{
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $user = $request->user();

        if ($user->type !== 'driver') {
            return ApiResponse::SendRespond(403, 'غير مصرح', []);
        }
        $location = DriverLocation::updateOrCreate(
            ['driver_id' => $user->id],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]
        );

        return ApiResponse::SendRespond(200, 'تم تحديث الموقع بنجاح', $location);
    }
}
