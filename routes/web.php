<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminContorller;
use App\Http\Controllers\ServiceController;


Route::prefix('/member')
    ->name('member.')
    ->group(function(){

        Route::get('/join', [AdminContorller::class, 'joinMemberView'])->name('join.html');
        Route::post('/join/submit', [AdminContorller::class, 'joinMemberPI'])->name('join.api');
        Route::get('/join/success', [AdminContorller::class, 'successMemberView'])->name('success.html');

        Route::get('/service/join', [ServiceController::class, 'serviceRegisterView'])->name('service.register.html');
        Route::post('/service/register', [ServiceController::class, 'serviceRegisterAPI'])->name('service.register.api');
        Route::get('/service/success', [ServiceController::class, 'serviceRegisterSuccessView'])->name('service.success.html');
        
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

                Route::get('/', [ServiceController::class, 'serviceListView'])->name('list.html');
                Route::get('/list', [ServiceController::class, 'getServiceListAPI'])->name('get.list.api');
                Route::post('/add', [ServiceController::class, 'addServiceAPI'])->name('add.api');

                Route::post('/action', [ServiceController::class, 'updateServiceActionAPI'])->name('update.action');
                Route::get('/update', [ServiceController::class, 'updateServiceView'])->name('update.html');
                Route::post('/update', [ServiceController::class, 'updateServiceAPI'])->name('update.api');

                Route::get('/registration', [ServiceController::class, 'registrationListView'])->name('registration.html');
                Route::get('/registration/list', [ServiceController::class, 'registrationListAPI'])->name('registration.list.api');
                Route::get('/registration/list-details', [ServiceController::class, 'registrationListDetailsView'])->name('registration.list.details.html');
                Route::get('/registration/list-details/data', [ServiceController::class, 'registrationListDetailsAPI'])->name('registration.list.details.api');
                Route::post('/registration/individual/attend', [ServiceController::class, 'registrationIndividualAttendAPI'])->name('registration.individual.attend.api');

                Route::get('/scan/qr-code', [ServiceController::class, 'scannerView'])->name('scan.html');
                Route::post('/scan/show-list', [ServiceController::class, 'showRegistrationListAPI'])->name('scan.show.registration.api');
                Route::post('/scan/attend-list', [ServiceController::class, 'makeAttendanceListAPI'])->name('scan.make.attendance.api');


                

        });

});


