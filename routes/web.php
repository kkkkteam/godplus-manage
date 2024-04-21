<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminContorller;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/join', [AdminContorller::class, 'joinMemberView'])->name('join.html');
Route::post('/join/submit', [AdminContorller::class, 'joinMemberPI'])->name('join.api');