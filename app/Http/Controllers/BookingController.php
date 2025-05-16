<?php

namespace App\Http\Controllers;
use App\Models\Room;
use App\Models\Booking;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function view_bookings()
    {
        $bookings = Booking::orderBy('id', 'desc')->paginate(10);
        return view('bookings.booking_view', compact('bookings'));
    }

    public function filterBookings(Request $request)
    {
        $query = Booking::with(['user', 'room']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('room', fn($q) => $q->where('room_number', 'like', "%{$search}%"))
                    ->orWhereHas('room', fn($q) => $q->where('type', 'like', "%{$search}%"))
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('check_in', 'desc')->paginate(10);

        return view('bookings.booking_table', compact('bookings'))->render();
    }


    public function add_bookings_form()
    {
        $today = now()->toDateString();
        $rooms = Room::whereDoesntHave('bookings', function ($query) use ($today) {
            $query->where('check_out', '>=', $today);
        })->get();
        return view('bookings.booking_form', compact('rooms'));
    }

    public function getAvailableRooms(Request $request)
    {
        $checkIn = $request->input('check_in');
        $checkOut = $request->input('check_out');

        if (!$checkIn || !$checkOut) {
            return response()->json(['rooms' => []]);
        }

        // Get room IDs that are already booked in the selected date range
        $bookedRoomIds = Booking::where('status', '!=', 'cancelled')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($query) use ($checkIn, $checkOut) {
                        $query->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })->pluck('room_id')->toArray();

        // Fetch available rooms
        $rooms = Room::whereNotIn('id', $bookedRoomIds)->get();

        return response()->json(['rooms' => $rooms]);
    }

    public function add_bookings_form_submit(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in',
        ]);

        $room_id = $validated['room_id'];
        $check_in = Carbon::parse($validated['check_in_date']);
        $check_out = Carbon::parse($validated['check_out_date']);

        // Check for overlapping bookings
        $hasConflict = Booking::where('room_id', $room_id)
            ->where(function ($query) use ($check_in, $check_out) {
                $query->whereBetween('check_in', [$check_in, $check_out->copy()->subDay()])
                    ->orWhereBetween('check_out', [$check_in->copy()->addDay(), $check_out])
                    ->orWhere(function ($q) use ($check_in, $check_out) {
                        $q->where('check_in', '<=', $check_in)
                            ->where('check_out', '>=', $check_out);
                    });
            })
            ->exists();

        if ($hasConflict) {
            return response()->json([
                'status' => 'error',
                'message' => 'Room is already booked for the selected dates.',
            ]);
        }

        // Store the booking

        Booking::create([
            'user_id' => Auth::user()->id,
            'room_id' => $room_id,
            'check_in' => $check_in,
            'check_out' => $check_out,
            'status' => 'confirmed',
        ]);


        return response()->json([
            'status' => 'success',
            'message' => 'Room booked successfully!',
        ]);


    }
    public function destroy($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found.');
        }


        $booking->status = 'cancelled';
        $booking->save();

        return redirect()->route('bookings')->with('success', 'Booking cancelled successfully.');
    }

}
