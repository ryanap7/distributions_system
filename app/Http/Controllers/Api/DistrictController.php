<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function index(): JsonResponse
    {
        $districts = District::all();

        $response = [
            'message' => 'Success',
            'data' => $districts,
        ];

        return response()->json($response);
    }
}
