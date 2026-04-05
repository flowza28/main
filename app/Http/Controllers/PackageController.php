<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Services\FreeRadiusService;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    protected FreeRadiusService $freeRadius;

    public function __construct(FreeRadiusService $freeRadius)
    {
        $this->freeRadius = $freeRadius;
    }

    public function index()
    {
        $packages = Package::latest()->paginate(15);
        return view('packages.index', compact('packages'));
    }

    public function create()
    {
        return view('packages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'download_speed' => 'required|integer|min:1',
            'upload_speed' => 'required|integer|min:1',
            'price' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $data['rate_limit'] = $this->makeRateLimit($data['download_speed'], $data['upload_speed']);
        Package::create($data);

        return redirect()->route('packages.index')->with('success', 'Paket berhasil dibuat.');
    }

    public function edit(Package $package)
    {
        return view('packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'download_speed' => 'required|integer|min:1',
            'upload_speed' => 'required|integer|min:1',
            'price' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $data['rate_limit'] = $this->makeRateLimit($data['download_speed'], $data['upload_speed']);
        $package->update($data);

        foreach ($package->customers as $customer) {
            $this->freeRadius->syncCustomer($customer);
        }

        return redirect()->route('packages.index')->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('packages.index')->with('success', 'Paket berhasil dihapus.');
    }

    protected function makeRateLimit(int $download, int $upload): string
    {
        return sprintf('%sk/%sk', $download, $upload);
    }
}
