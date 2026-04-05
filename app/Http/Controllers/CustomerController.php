<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Package;
use App\Services\FreeRadiusService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected FreeRadiusService $freeRadius;

    public function __construct(FreeRadiusService $freeRadius)
    {
        $this->freeRadius = $freeRadius;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $customers = Customer::with('package')
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        $packages = Package::all();
        return view('customers.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:customers,username',
            'password' => 'required|string|min:6',
            'package_id' => 'required|exists:packages,id',
            'expired_at' => 'nullable|date',
            'active' => 'boolean',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
        ]);

        $data['password'] = $data['password'];
        $data['active'] = $request->boolean('active');
        $data['status'] = $data['active'] ? 'active' : 'nonaktif';

        $customer = Customer::create($data);
        $this->freeRadius->syncCustomer($customer);

        return redirect()->route('customers.index')->with('success', 'Customer ISP berhasil dibuat.');
    }

    public function edit(Customer $customer)
    {
        $packages = Package::all();
        return view('customers.edit', compact('customer', 'packages'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:customers,username,' . $customer->id,
            'password' => 'nullable|string|min:6',
            'package_id' => 'required|exists:packages,id',
            'expired_at' => 'nullable|date',
            'active' => 'boolean',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
        ]);

        if ($data['password']) {
            $customer->password = $data['password'];
        }

        $customer->fill(array_filter($data, fn ($value) => ! is_null($value) && $value !== ''));
        $customer->active = $request->boolean('active');
        $customer->status = $customer->active ? 'active' : 'nonaktif';
        $customer->save();

        $this->freeRadius->syncCustomer($customer);

        return redirect()->route('customers.index')->with('success', 'Customer ISP berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $this->freeRadius->deleteCustomer($customer);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer ISP berhasil dihapus.');
    }

    public function toggleStatus(Customer $customer)
    {
        $customer->active = ! $customer->active;
        $customer->status = $customer->active ? 'active' : 'nonaktif';
        $customer->save();

        if ($customer->active) {
            $this->freeRadius->enableCustomer($customer);
        } else {
            $this->freeRadius->disableCustomer($customer);
        }

        return redirect()->route('customers.index')->with('success', 'Status customer berhasil diubah.');
    }
}
