<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Services\FreeRadiusService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected FreeRadiusService $freeRadius;

    public function __construct(FreeRadiusService $freeRadius)
    {
        $this->freeRadius = $freeRadius;
    }

    public function index()
    {
        Invoice::generateExpiringInvoices();

        $totalUsers = Customer::count();
        $activeUsers = Customer::where('active', true)
            ->where(function ($query) {
                $query->whereNull('expired_at')
                      ->orWhere('expired_at', '>', now());
            })->count();
        $bandwidthUsage = $this->freeRadius->getTrafficSummary();
        $totalUsage = collect($bandwidthUsage)->sum(fn ($row) => $row['download'] + $row['upload']);
        $currentMonthRevenue = Invoice::where('status', 'lunas')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        return view('dashboard.index', compact('totalUsers', 'activeUsers', 'totalUsage', 'currentMonthRevenue', 'bandwidthUsage'));
    }
}
