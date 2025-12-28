<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->middleware('auth:api');
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get dashboard statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->dashboardService->getStats();
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

