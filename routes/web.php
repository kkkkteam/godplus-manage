<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminContorller;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\NewcomerController;


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

                Route::get('/welcome-message/list', [AdminContorller::class, 'getWelcomeMessageListAPI'])->name('get.welcome.list.api');
                // Route::post('/welcome-message/set', [AdminContorller::class, 'setWelcomeMessageAPI'])->name('welcome.set.api');
                // Route::post('/welcome-message/delete', [AdminContorller::class, 'deleteWelcomeMessageAPI'])->name('welcome.delete.api');

                Route::post('/welcome-message/action', [AdminContorller::class, 'updateWelcomeMessageActionAPI'])->name('welcome.update.action');
                Route::get('/welcome-message/update', [AdminContorller::class, 'updateWelcomeMessageView'])->name('welcome.update.html');
                Route::post('/welcome-message/update-action', [AdminContorller::class, 'updateWelcomeMessageAPI'])->name('welcome.update.api');
                
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

                // registration
                Route::get('/registration', [ServiceController::class, 'registrationListView'])->name('registration.html');
                Route::get('/registration/list', [ServiceController::class, 'registrationListAPI'])->name('registration.list.api');
                Route::get('/registration/list-details', [ServiceController::class, 'registrationListDetailsView'])->name('registration.list.details.html');
                Route::get('/registration/list-details/data', [ServiceController::class, 'registrationListDetailsAPI'])->name('registration.list.details.api');
                Route::post('/registration/individual/attend', [ServiceController::class, 'registrationIndividualAttendAPI'])->name('registration.individual.attend.api');
                
                Route::get('/download-qrcode', [ServiceController::class, 'downloadServiceQRCodeAPI'])->name('download.qrcode.api');  

                // attendance update page
                Route::get('/attendance/update', [ServiceController::class, 'attendanceUpdateView'])->name('attendance.update.html');
                Route::get('/attendance/list', [ServiceController::class, 'attendancelistAPI'])->name('attendance.list.api');
                Route::post('/attendance/add', [ServiceController::class, 'attendanceAddAPI'])->name('attendance.add.api');
                Route::post('/attendance/detele', [ServiceController::class, 'attendanceDeteleAPI'])->name('attendance.detele.api');
                
                Route::get('/attendance-summary', [ServiceController::class, 'attendanceSummaryView'])->name('attendance_summary.html');
                Route::get('/attendance-summary/summary/data', [ServiceController::class, 'attendanceSummaryAPI'])->name('attendance.summary.api');

                Route::get('/attendance-summary/select-one/{service_slug}', [ServiceController::class, 'attendanceServiceView'])->name('attendance.select.html');
                Route::get('/attendance-summary/detail', [ServiceController::class, 'attendanceServiceAPI'])->name('attendance.detail.api');
                
                Route::get('/attendance-summary/by-people', [ServiceController::class, 'attendanceServiceByPoepleView'])->name('attendance.by.poeple.html');
                Route::get('/attendance-summary/by-people/data', [ServiceController::class, 'attendanceServiceByPoepleAPI'])->name('attendance.by.poeple.api');

                Route::get('/scan/qr-code', [ServiceController::class, 'scannerView'])->name('scan.html');
                Route::post('/scan/show-list', [ServiceController::class, 'showRegistrationListAPI'])->name('scan.show.registration.api');
                Route::post('/scan/attend-list', [ServiceController::class, 'makeAttendanceListAPI'])->name('scan.make.attendance.api');

        });



        Route::prefix('/newcomer')
            ->name('newcomer.')
            ->group(function(){

                Route::get('/', [NewcomerController::class, 'newcomerListView'])->name('list.html');
                Route::get('/list', [NewcomerController::class, 'getNewcomerListAPI'])->name('get.list.api');

            });

});


