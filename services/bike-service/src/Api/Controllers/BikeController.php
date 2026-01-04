<?php

namespace BikeService\Api\Controllers;

use BikeService\Models\Bike;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class BikeController extends Controller
{
    public function index()
    {
        return Bike::all();
    }

    public function show($id)
    {
        return Bike::findOrFail($id);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'brand' => 'required',
            'model' => 'required',
            'year' => 'required|integer'
        ]);

        return Bike::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $bike = Bike::findOrFail($id);
        $bike->update($request->all());
        return $bike;
    }

    public function delete($id)
    {
        Bike::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function maintenance($id)
    {
        $bike = Bike::findOrFail($id);
        return $bike->maintenanceRecords()->orderBy('created_at', 'desc')->get();
    }
}