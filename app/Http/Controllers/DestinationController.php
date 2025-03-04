<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function index()
    {
        $destinations = Destination::latest()->paginate(10);
        return view('destinations.index', compact('destinations'));
    }

    public function create()
    {
        return view('destinations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|numeric',
            'country' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        Destination::create($request->all());
        return redirect()->route('destinations.index')->with('success', __('messages.Destination added successfully.'));
    }

    public function show(Destination $destination)
    {
        return view('destinations.show', compact('destination'));
    }

    public function edit(Destination $destination)
    {
        return view('destinations.edit', compact('destination'));
    }

    public function update(Request $request, Destination $destination)
    {
        $request->validate([
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|numeric',
            'country' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        $destination->update($request->all());
        return redirect()->route('destinations.index')->with('success', __('messages.Destination updated successfully.'));
    }

    public function destroy(Destination $destination)
    {
        $destination->delete();
        return redirect()->route('destinations.index')->with('success', __('messages.Destination deleted successfully.'));
    }
}
