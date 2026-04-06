<?php

namespace App\Http\Controllers;

use App\Services\FreeRadiusService;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    protected FreeRadiusService $freeRadius;

    public function __construct(FreeRadiusService $freeRadius)
    {
        $this->freeRadius = $freeRadius;
    }

    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $traffic = $this->freeRadius->getTrafficSummary($search);
            $onlineUsers = collect($traffic)->where('online', true)->count();
            $offlineUsers = collect($traffic)->where('online', false)->count();

            $chartData = [
                'labels' => collect($traffic)->pluck('username'),
                'download' => collect($traffic)->pluck('download'),
                'upload' => collect($traffic)->pluck('upload'),
            ];

            return view('monitoring.index', compact('traffic', 'onlineUsers', 'offlineUsers', 'chartData', 'search'));
        } catch (\Exception $e) {
            return view('monitoring.index', [
                'traffic' => [],
                'onlineUsers' => 0,
                'offlineUsers' => 0,
                'chartData' => ['labels' => [], 'download' => [], 'upload' => []],
                'search' => $request->query('search'),
                'error' => 'Unable to connect to RADIUS database: ' . $e->getMessage()
            ]);
        }
    }

    public function show(Request $request, string $username)
    {
        try {
            $userTraffic = $this->freeRadius->getUserTrafficDetail($username);
            return view('monitoring.show', compact('userTraffic'));
        } catch (\Exception $e) {
            return view('monitoring.show', [
                'userTraffic' => [
                    'username' => $username,
                    'total_sessions' => 0,
                    'total_download' => '0 B',
                    'total_upload' => '0 B',
                    'total_traffic' => '0 B',
                    'sessions' => []
                ],
                'error' => 'Unable to connect to RADIUS database: ' . $e->getMessage()
            ]);
        }
    }
}
