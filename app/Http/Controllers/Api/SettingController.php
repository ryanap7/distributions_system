<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function getAnnouncement(): JsonResponse
    {
        $announcement = Setting::where('key', 'Pengumuman')->value('value');

        $response = [
            'message' => 'Success',
            'data' => [
                'announcement' => $announcement,
            ],
        ];

        return response()->json($response);
    }

    public function getContact(): JsonResponse
    {
        $contactPhoneNumber = Setting::where('key', 'Kontak Admin')->value('value');

        $response = [
            'message' => 'Success',
            'data' => [
                'contact_phone_number' => $contactPhoneNumber,
            ],
        ];

        return response()->json($response);
    }
}
