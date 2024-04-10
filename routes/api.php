<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsppController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::group([
	"prefix" => "/webhook",
], function()  {
    Route::post('/inbound', [WhatsppController::class, 'inboundMessageAPI'])->name('webhook.inbound.json');
    Route::post('/status', [WhatsppController::class, 'statusAPI'])->name('webhook.status.json');

});
    