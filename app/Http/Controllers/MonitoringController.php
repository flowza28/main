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

    public function show(Request $request, string $username)
    {
        $userTraffic = $this->freeRadius->getUserTrafficDetail($username);

        return view('monitoring.show', compact('userTraffic'));
    }
}
