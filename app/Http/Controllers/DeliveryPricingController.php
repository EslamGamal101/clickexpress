<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\DeliveryPricing; // <-- Import the model
use Illuminate\Http\JsonResponse;

class DeliveryPricingController extends Controller
{
    /**
     * Display a listing of the delivery pricing options.
     */
    public function index(): JsonResponse
    {
        // Fetch all records from the database
        $pricings = DeliveryPricing::all();

        // Return the data as a JSON response
        return ApiResponse::SendRespond(200, 'Delivery pricing options retrieved successfully', $pricings);
    }
}
