<?php

namespace App\Http\Controllers;

use App\Models\PoolGroup;
use Illuminate\Http\Request;

class PoolGroupController extends Controller
{
    public function index()
    {
        $poolGroups = PoolGroup::latest()->paginate(15);

        return view('pool_groups.index', compact('poolGroups'));
    }

    public function create()
    {
        return view('pool_groups.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:pool_groups,name',
            'start_ip' => 'required|ip',
            'end_ip' => 'required|ip',
            'description' => 'nullable|string',
        ]);

        PoolGroup::create($data);

        return redirect()->route('pool-groups.index')->with('success', 'Pool address berhasil ditambahkan.');
    }

    public function edit(PoolGroup $poolGroup)
    {
        return view('pool_groups.edit', compact('poolGroup'));
    }

    public function update(Request $request, PoolGroup $poolGroup)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:pool_groups,name,' . $poolGroup->id,
            'start_ip' => 'required|ip',
            'end_ip' => 'required|ip',
            'description' => 'nullable|string',
        ]);

        $poolGroup->update($data);

        return redirect()->route('pool-groups.index')->with('success', 'Pool address berhasil diperbarui.');
    }

    public function destroy(PoolGroup $poolGroup)
    {
        $poolGroup->delete();

        return redirect()->route('pool-groups.index')->with('success', 'Pool address berhasil dihapus.');
    }
}
