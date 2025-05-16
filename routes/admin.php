<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Rooms;

// Admin Dashboard
Route::get('/admin_home', function () {
    return view('admin.dashboard_home');
})->name('admin.dashboard');

// Room Management
Route::get('/rooms', [Rooms::class, 'view_rooms'])->name('rooms');
Route::get('/add-room', [Rooms::class, 'add_rooms_form'])->name('add_rooms');
Route::post('/add-room', [Rooms::class, 'add_room_form_submit'])->name('add_room_form_submit');
Route::get('/edit-room/{id}', [Rooms::class, 'edit'])->name('edit_room');
Route::put('/update-room/{id}', [Rooms::class, 'update'])->name('update_room');
Route::get('/delete-room/{id}', [Rooms::class, 'delete'])->name('delete_room');
