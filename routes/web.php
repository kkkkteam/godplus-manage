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


Route::prefix('/admin')
    ->name('admin.')
    ->group(function(){

        Route::prefix('/whatsapp-list')
            ->name('command.')
            ->group(function(){

                Route::get('/', [AdminContorller::class, 'commandListView'])->name('list.html');
                Route::get('/list', [AdminContorller::class, 'getCommandListAPI'])->name('get.list.api');
                Route::post('/set', [AdminContorller::class, 'setCommandAPI'])->name('set.api');
                Route::post('/delete', [AdminContorller::class, 'deleteCommandAPI'])->name('delete.api');

                Route::post('/action', [AdminContorller::class, 'updateActionAPI'])->name('update.action');
                Route::get('/update', [AdminContorller::class, 'updateCommandView'])->name('update.html');
                Route::post('/update-action', [AdminContorller::class, 'updateCommandAPI'])->name('update.api');
                

        });

        Route::prefix('/service')
            ->name('service.')
            ->group(function(){

        });

});


