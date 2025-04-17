<?php

use App\Http\Controllers\queuebackend\GetVisitorRawDataController;
use App\Http\Controllers\queuebackend\InRuleQueueOperations;
use App\Http\Controllers\queuebackend\OutRuleQueueOperations;
use App\Http\Controllers\queuebackend\PreQueueNotiSender;
use App\Http\Controllers\queuebackend\ProcessDirectAccess;
use App\Http\Controllers\queuebackend\ProcessQueueOperations;
use App\Http\Controllers\queuebackend\QueueNotiSender;
use App\Http\Controllers\queuebackend\StatsApiService;
use App\Http\Controllers\queuebackend\VisitorsDataQueueOperations;
use App\Http\Controllers\queuebackend\PaymentGatewayController;
use App\Http\Controllers\QueueroomController;
use App\Http\Controllers\StripeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/showPage', [QueueroomController::class, 'showPage']);

Route::get('/paymentInquire', [PaymentGatewayController::class, 'paymentInquire'])->name('paymentInquire');
Route::get('/processPayment', [PaymentGatewayController::class, 'processPayment'])->name('processPayment');
Route::get('/initiateCheckout', [PaymentGatewayController::class, 'initiateCheckout'])->name('initiateCheckout');

Route::post('/checkUserSpace', [VisitorsDataQueueOperations::class, 'checkUserSpace'])->name('checkUserSpace');
Route::post('/get-browser-data', [GetVisitorRawDataController::class, 'getData'])->name('get.Data');
Route::post('/q-operations', [VisitorsDataQueueOperations::class, 'operateQueue'])->name('operate.Queue');
Route::post('/updateQueueNumber', [VisitorsDataQueueOperations::class, 'updateQueueNumber']);
Route::post('/verify-bypass-code', [VisitorsDataQueueOperations::class, 'verifyBypassCode'])->name('cron.verify.bypass.code');
Route::post('/check-bypass-code-status', [VisitorsDataQueueOperations::class, 'checkBypassCodeStatus'])->name('cron.verify.bypass.code.status');
Route::post('/notification-request', [VisitorsDataQueueOperations::class, 'notificationRequest'])->name('cron.notification.Request');
Route::post('/api-submit', [VisitorsDataQueueOperations::class, 'notificationRequest'])->name('cron.notification.Request');
Route::post('/waiting-room-noti-submit', [VisitorsDataQueueOperations::class, 'waitingRoomNotificationRequest'])->name('cron.waitingRoomNotificationRequest.waitingRoomNotificationRequest');

//apis
Route::any('/dashbaord-graph-data', [StatsApiService::class, 'dashGraphData'])->name('stats.dashGraphData');
Route::any('/dashbaord-sanky-data', [StatsApiService::class, 'dashSankyData'])->name('stats.dashSankyData');

Route::any('/q-start', [InRuleQueueOperations::class, 'startQueue'])->name('cron.Queue.startQueue');
Route::any('/q-end', [OutRuleQueueOperations::class, 'endQueue'])->name('cron.Queue.endQueue');
Route::any('/q-process', [ProcessQueueOperations::class, 'processQueue'])->name('cron.Queue.processQueue');
Route::any('/q-process-direct-access', [ProcessDirectAccess::class, 'ProcessDirectAccess'])->name('cron.Queue.ProcessDirectAccess');
Route::any('/q-prequeue-noti-sender', [PreQueueNotiSender::class, 'PreQueueNotiSender'])->name('cron.PreQueueNotiSender.PreQueueNotiSender');
Route::any('/q-queue-noti-sender', [QueueNotiSender::class, 'QueueNotiSender'])->name('cron.QueueNotiSender.QueueNotiSender');

Route::any('/webhookEvent', [StripeController::class, 'stripeWebhook']);


Route::post('/throttle/check', [\App\Http\Controllers\ThrottleController::class, 'check'])->name('check-api');
Route::post('/throttle/retry', [\App\Http\Controllers\ThrottleController::class, 'retry'])->name('retry-api');    