<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MapLocation;
use App\Services\FreeRadiusService;
use Illuminate\Http\Request;

class MapLocationController extends Controller
{
    protected FreeRadiusService $freeRadius;

    public function __construct(FreeRadiusService $freeRadius)
    {
        $this->freeRadius = $freeRadius;
    }

    public function index()
    {
        $mapLocations = MapLocation::orderBy('type')->get();
        $customers = Customer::whereNotNull('latitude')->whereNotNull('longitude')->get();

        // Get online status for customers
        $traffic = $this->freeRadius->getTrafficSummary();
        $onlineStatuses = collect($traffic)->pluck('online', 'username');

        $mapMarkers = $mapLocations->map(function ($location) {
            return [
                'type' => $location->type,
                'name' => $location->name,
                'address' => $location->address,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'type_label' => $location->type_label,
                'active' => $location->active,
            ];
        })->toArray();

        $customerMarkers = $customers->map(function ($customer) use ($onlineStatuses) {
            return [
                'name' => $customer->name,
                'username' => $customer->username,
                'latitude' => $customer->latitude,
                'longitude' => $customer->longitude,
                'type' => 'client',
                'online' => $onlineStatuses->get($customer->username, false),
            ];
        })->toArray();

        return view('map-locations.index', compact('mapLocations', 'customers', 'mapMarkers', 'customerMarkers', 'onlineStatuses'));
    }

    public function create()
    {
        return view('map-locations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(MapLocation::TYPES)),
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $data['active'] = $request->boolean('active');

        MapLocation::create($data);

        return redirect()->route('map-locations.index')->with('success', 'Lokasi peta berhasil ditambahkan.');
    }

    public function edit(MapLocation $mapLocation)
    {
        return view('map-locations.edit', compact('mapLocation'));
    }

    public function update(Request $request, MapLocation $mapLocation)
    {
        $data = $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(MapLocation::TYPES)),
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $data['active'] = $request->boolean('active');

        $mapLocation->update($data);

        return redirect()->route('map-locations.index')->with('success', 'Lokasi peta berhasil diperbarui.');
    }

    public function destroy(MapLocation $mapLocation)
    {
        $mapLocation->delete();

        return redirect()->route('map-locations.index')->with('success', 'Lokasi peta berhasil dihapus.');
    }
}
