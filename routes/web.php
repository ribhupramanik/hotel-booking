<?php
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Rooms;
use Illuminate\Support\Facades\Route;


Auth::routes();
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/dashboard', function () {
    return view('admin.dashboard_home');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/view_rooms', [Rooms::class, 'view_rooms'])->name('rooms');
    Route::get('/add_rooms_form', [Rooms::class, 'add_rooms_form'])->name('add_rooms');
    Route::post('/add_rooms_form_submit', [Rooms::class, 'add_room_form_submit'])->name('add_room_form_submit');
    Route::get('/filter-rooms', [Rooms::class, 'filter'])->name('filter_rooms');
    Route::get('/edit-room/{id}', [Rooms::class, 'edit'])->name('edit_room');
    Route::put('/update-room/{id}', [Rooms::class, 'update'])->name('update_room');
    Route::get('/delete-room/{id}', [Rooms::class, 'delete'])->name('delete_room');
});

Route::middleware(['auth', 'client'])->group(function () {

    Route::get('/view_bookings', [BookingController::class, 'view_bookings'])->name('bookings');
    Route::get('/available-rooms', [BookingController::class, 'getAvailableRooms'])->name('available_rooms');
    Route::get('/add_bookings_form', [BookingController::class, 'add_bookings_form'])->name('add_bookings');
    Route::post('/add_bookings_form', [BookingController::class, 'add_bookings_form_submit'])->name('add_bookings_submit');
    Route::get('/filter-bookings', [BookingController::class, 'filterBookings'])->name('filter_bookings');
    Route::get('/delete-booking/{id}', [BookingController::class, 'destroy'])->name('bookings_delete');
    // Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});