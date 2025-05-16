<?php

namespace App\Http\Controllers;
use App\Models\Room;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class Rooms extends Controller
{
    public function view_rooms()
    {
        $rooms = Room::orderBy('id', 'desc')->paginate(10);
        return view('admin.rooms_view', compact('rooms'));
    }

    public function filter(Request $request)
    {
        $query = Room::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('room_number', 'like', "%$search%")
                    ->orWhere('price', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%");
            });
        }

        $rooms = $query->orderBy('id', 'desc')->paginate(10);

        return view('admin.room_table', compact('rooms'))->render();
    }

    public function add_rooms_form()
    {
        return view('admin.add_rooms_form');
    }
    public function add_room_form_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_number' => 'required|integer|unique:rooms,room_number',
            'room_type' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            Alert::error('Validation Failed', 'Please correct the errors and try again.');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Room::create([
            'room_number' => $request->room_number,
            'type' => $request->room_type,
            'price' => $request->price,
            'status' => 'available'
        ]);
        Alert::toast('Success', 'Room added successfully!');
        return redirect()->route('rooms');
    }

    public function delete($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return redirect()->route('rooms')->with('success', 'Room deleted successfully!');
    }

    public function edit($id)
    {
        $room = Room::findOrFail($id);
        return view('admin.edit_rooms_form', compact('room'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'room_number' => 'required|integer|unique:rooms,room_number,' . $id,
            'room_type' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        $room = Room::findOrFail($id);
        $room->update([
            'room_number' => $request->room_number,
            'type' => $request->room_type,
            'price' => $request->price,
        ]);

        Alert::toast('Success', 'Room updated successfully!');
        return redirect()->route('rooms');
    }

}