<?php

namespace App\Http\Controllers;

use App\Models\Origin;
use Illuminate\Http\Request;

class OriginController extends Controller
{
    public function index()
    {
        $origins = Origin::all();
        return view('origins.index', compact('origins'));
    }

    public function create()
    {
        return view('origins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|numeric|digits_between:4,10',
            'country' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        Origin::create($request->all());

        return redirect()->route('origins.index')->with('success', 'Origin added successfully.');
    }

    public function edit(Origin $origin)
    {
        return view('origins.edit', compact('origin'));
    }

    public function update(Request $request, Origin $origin)
    {
        $request->validate([
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:10',
            'country' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        $origin->update($request->all());

        return redirect()->route('origins.index')->with('success', 'Origin updated successfully.');
    }

    public function destroy(Origin $origin)
    {
        $origin->delete();

        return redirect()->route('origins.index')->with('success', 'Origin deleted successfully.');
    }
}

