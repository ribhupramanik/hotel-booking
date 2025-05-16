<?php

namespace App\Http\Controllers;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    public function dashboard()
{
    // Booking trends: count bookings grouped by month for current year
    $bookingTrends = Booking::select(
            DB::raw("DATE_FORMAT(check_in, '%Y-%m') as month"),
            DB::raw('count(*) as count')
        )
        ->whereYear('check_in', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    // Most booked room type
    $mostBookedRoomType = Booking::select('rooms.type', DB::raw('count(*) as count'))
        ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
        ->groupBy('rooms.type')
        ->orderByDesc('count')
        ->first();

    // Upcoming vs Past bookings counts
    $today = Carbon::today();
    $upcomingBookingsCount = Booking::where('check_in', '>=', $today)->count();
    $pastBookingsCount = Booking::where('check_in', '<', $today)->count();

    // Total rooms and total bookings counts
    $totalRooms = Room::count();
    $totalBookings = Booking::count();

    return view('admin.dashboard_home', compact(
        'bookingTrends',
        'mostBookedRoomType',
        'upcomingBookingsCount',
        'pastBookingsCount',
        'totalRooms',
        'totalBookings'
    ));
}
}
