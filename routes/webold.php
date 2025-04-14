<?php

use App\Http\Controllers\AddStaffController;
use App\Http\Controllers\admin\DeveloperAdminController;
use App\Http\Controllers\admin\PermissionAccessAdminController;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\EmailNoticeController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueroomController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ShowStaffController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/queueroom', function () {
    return view('queue-room.queueRoom');
});

// Route::get('/create-queue', function () {
//     return view('queue-room.createqueue');
// });
Route::get('/create-queue', [QueueroomController::class, 'viewpageCreateQueue'])->name('createqueue');

// Routes requiring authentication
Route::group(['middleware' => 'auth.user'], function () {
    // Define routes that require authentication here
    Route::get('/dashboard', [LoginRegisterController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [LoginRegisterController::class, 'logout'])->name('logout');
});

// Routes not requiring authentication
Route::group([], function () {
    // Define routes that do not require authentication here
    Route::get('/register', [LoginRegisterController::class, 'register'])->name('register');
    Route::post('/store', [LoginRegisterController::class, 'store'])->name('store');
    Route::get('/login', [LoginRegisterController::class, 'login'])->name('login');
    Route::post('/authenticate', [LoginRegisterController::class, 'authenticate'])->name('authenticate');
});

Route::post('/queue-setup', [QueueroomController::class, 'setup'])->name('queue_setup');
Route::get('/queue-room-view', [QueueroomController::class, 'viewpage'])->name('queue-room-view');

Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetPasswordForm'])
    ->name('password.reset');

Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])
    ->name('password.update');

Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::get('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

Route::put('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');

Route::get('/addStaff', [AddStaffController::class, 'index'])->name('addStaff');
Route::post('/save-staff', [AddStaffController::class, 'save'])->name('saveStaff');
Route::get('/staff-access-manage', [ShowStaffController::class, 'index'])->name('staff-access-manage');

Route::get('/edit-user/{id}', [ShowStaffController::class, 'edit'])->name('editStaff');
Route::post('/update-permissions/{id}', [ShowStaffController::class, 'updatePermissions'])->name('updatePermissions');
Route::get('/delete-user/{id}', [ShowStaffController::class, 'delete'])->name('deleteStaff');
Route::get('/activate-deactivate/{id}', [ShowStaffController::class, 'activateDeactivate'])->name('activateDeactivate');

Route::get('/email-notice', [EmailNoticeController::class, 'index'])->name('email-notice');

Route::get('/add-email-notice', [EmailNoticeController::class, 'addEmail'])->name('add-email-notice');
Route::post('/save-email-notice', [EmailNoticeController::class, 'saveEmail'])->name('save.email');
Route::get('/delete-email-notice/{id}', [EmailNoticeController::class, 'deleteEmail'])->name('delete-email-notice');
Route::get('/edit-email-notice/{id}', [EmailNoticeController::class, 'editEmail'])->name('edit-email-notice');
Route::put('/update-email-notice/{id}', [EmailNoticeController::class, 'updateEmail'])->name('update-email-notice');

Route::get('/developer', [DeveloperController::class, 'index'])->name('developer');

// Route::group(['prefix'=>'admin'], function()
// {

// });
Route::get('admin-index', function () {
    return view('admin.adminindex');
    // echo "Admin Index";
});
Route::group(['prefix' => 'admin'], function () {
    Route::get('/admin-index', [PermissionAccessAdminController::class, 'index'])->name('admin.index');

    Route::get('/developers-index', [DeveloperAdminController::class,'index'])->name('developers-index');
    Route::post('/update-script', [DeveloperAdminController::class,'updateScript'])->name('update.script');
});
// Route::get('developers-index', function () {

//     return view('admin.developer');
//     //echo "developers Index";
// });
