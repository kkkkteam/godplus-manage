<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminContorller;


// Route::get('/', function () {
//     return view('welcome');
// });

Route::prefix('/member')
    ->name('member.')
    ->group(function(){

        Route::get('/join', [AdminContorller::class, 'joinMemberView'])->name('join.html');
        Route::post('/join/submit', [AdminContorller::class, 'joinMemberPI'])->name('join.api');
        Route::get('/join/success', [AdminContorller::class, 'successMemberView'])->name('success.html');
        
});

