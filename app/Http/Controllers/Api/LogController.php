<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class LogController extends Controller
{
    /**
     * Get logs by user ID.
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function getLogsByUserId($userId): JsonResponse
    {
        // Validate the user ID is numeric and positive
        if (!is_numeric($userId) || $userId <= 0) {
            return response()->json([
                'message' => 'Invalid user ID'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Fetch the 5 most recent logs for the given user ID
        $logs = Log::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Check if logs exist for the user
        if ($logs->isEmpty()) {
            return response()->json([
                'message' => 'No logs found for the specified user ID'
            ], Response::HTTP_NOT_FOUND);
        }

        // Return logs as a JSON response
        return response()->json([
            'message' => 'Success',
            'data' => $logs
        ], Response::HTTP_OK);
    }
}
