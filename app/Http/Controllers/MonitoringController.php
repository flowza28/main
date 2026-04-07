<?php

namespace App\Http\Controllers;

use App\Services\FreeRadiusService;
use App\Services\MikrotikService;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    protected FreeRadiusService $freeRadius;
    protected MikrotikService $mikrotik;

    public function __construct(FreeRadiusService $freeRadius, MikrotikService $mikrotik)
    {
        $this->freeRadius = $freeRadius;
        $this->mikrotik = $mikrotik;
    }

    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $traffic = $this->freeRadius->getTrafficSummary($search);
            $onlineUsers = collect($traffic)->where('online', true)->count();
            $offlineUsers = collect($traffic)->where('online', false)->count();

            // Get Mikrotik interface traffic
            $mikrotikConfig = config('services.mikrotik');
            $interfaceTraffic = $this->mikrotik->getAllInterfacesTraffic(
                $mikrotikConfig['host'],
                $mikrotikConfig['username'],
                $mikrotikConfig['password']
            );

            // Prepare chart data for interfaces
            $chartData = [
                'labels' => array_keys($interfaceTraffic),
                'rx_bytes' => array_column($interfaceTraffic, 'rx_bytes'),
                'tx_bytes' => array_column($interfaceTraffic, 'tx_bytes'),
            ];

            return view('monitoring.index', compact('traffic', 'onlineUsers', 'offlineUsers', 'chartData', 'search'));
        } catch (\Exception $e) {
            return view('monitoring.index', [
                'traffic' => [],
                'onlineUsers' => 0,
                'offlineUsers' => 0,
                'chartData' => ['labels' => [], 'rx_bytes' => [], 'tx_bytes' => []],
                'search' => $request->query('search'),
                'error' => 'Unable to connect to services: ' . $e->getMessage()
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
