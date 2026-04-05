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
    }
}
