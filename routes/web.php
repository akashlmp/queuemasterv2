<?php

use App\Http\Controllers\AddStaffController;
use App\Http\Controllers\admin\DeveloperAdminController;
use App\Http\Controllers\admin\SubscriptionPlanAdminController;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CanvasController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\EmailNoticeController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\InOutRulesController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueroomController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ShowStaffController;
use App\Http\Controllers\StatsroomController;
use App\Http\Controllers\StatsroomEditController;
use App\Http\Controllers\TempQueueDesignController;
use App\Http\Controllers\StripeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Session;


// Route::post('/swap-temp/js-test/ext.js', [QueueroomController::class, 'callEXTJsFile'])->name('swap-temp/js-test/ext.js');

Route::get('/embed-script.js', [\App\Http\Controllers\ThrottleController::class, 'embedScript'])
    ->name('embed.script');

Route::get('/embed-queue-script.js', [\App\Http\Controllers\ThrottleController::class, 'embedQueueScript'])
    ->name('embed.queue.script');

    
Route::post('/clear-session', function () {
    Session::forget('success');
    Session::forget('error');
    return response()->json(['message' => 'Session cleared']);
})->name('clear-session');

Route::post('/create-payment', [PaymentController::class, 'createPayment']);

Route::get('/terms-of-use', [PageController::class, 'showTermsOfUse'])->name('termsOfUse');
Route::get('/privacy-policy', [PageController::class, 'showPrivacyPolicy'])->name('privacyPolicy');

Route::group([], function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::post('/check-email', [LoginRegisterController::class, 'checkEmail'])->name('check-email');
    Route::get('/register', [LoginRegisterController::class, 'register'])->name('register');
    Route::post('/store', [LoginRegisterController::class, 'store'])->name('store');
    Route::get('/login', [LoginRegisterController::class, 'login'])->name('login');
    Route::post('/authenticate', [LoginRegisterController::class, 'authenticate'])->name('authenticate');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.update');

    Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::get('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
});
Route::get('/subscription', [SubscriptionPlanAdminController::class, 'showSubscription'])->name('subscription');

Route::post('/logout', [LoginRegisterController::class, 'logout'])->name('logout');
// Routes requiring authentication for visitor / user

Route::group(['middleware' => 'auth.user'], function () {
    Route::get('/dashboard', [LoginRegisterController::class, 'dashboard'])->name('dashboard');

    Route::get('/canvas', [CanvasController::class, 'index'])->name('canvas');
    Route::get('/edit-inline-room/{id}/{lang_id}', [CanvasController::class, 'index'])->name('edit.inline.room');
    Route::get('/edit-inline-room/{id}', [CanvasController::class, 'index'])->name('edit.inline.room');

    Route::get('/edit-template-inline-room/{id?}/{lang_id?}/{roomId?}', [CanvasController::class, 'edittemplate'])->name('edittemplate.inline.room');
    Route::get('/create-template-inline-room', [CanvasController::class, 'create'])->name('createtemplate.inline.room');

    Route::post('/canvas-save', [CanvasController::class, 'canvas_store']);
    Route::post('/end-room/{id}', [QueueroomController::class, 'endRoom']);

    Route::post('/getQueueTotalvisitors', [QueueroomController::class, 'getQueueTotalvisitors'])->name('getQueueTotalvisitors');
    Route::get('/create-queue', [QueueroomController::class, 'viewpageCreateQueue'])->name('createqueue');
    Route::get('/queue-room-view', [QueueroomController::class, 'viewpage'])->name('queue-room-view');
    Route::post('/queue-setup', [QueueroomController::class, 'setup'])->name('queue_setup');
    Route::post('/queue_update/{id}', [QueueroomController::class, 'queueRoomUpdate'])->name('queue.update');
    Route::get('/queue-room-edit/{id}', [QueueroomController::class, 'queueRoomEdit'])->name('queue-room-edit');
    Route::get('/queue-room-delete/{id}', [QueueroomController::class, 'queueRoomDelete'])->name('queue-room-delete');
    Route::get('/download-passcode/{id}', [QueueroomController::class, 'downloadPassCodes'])->name('downloadPassCodes');
    Route::get('/get-bypass-data/{id}', [QueueroomController::class, 'getBypassData'])->name('getBypassData');
    Route::get('/get-design-temp-data/{id}', [QueueroomController::class, 'getdesigntempData'])->name('getdesigntempData');
    Route::get('/get-email-data/{id}', [QueueroomController::class, 'getEmailData'])->name('getEmailData');
    Route::get('/get-sms-data/{id}', [QueueroomController::class, 'getsmsData'])->name('getsmsData');
    Route::get('/get-template-data/{id}', [QueueroomController::class, 'getTemplateData'])->name('getTemplateData');

    Route::get('/temp-queue-design', [QueueroomController::class, 'viewQueueRoomDesign'])->name('viewQueueRoomDesign');
    Route::get('/temp-queue-design-edit/{id}/{roomId}', [TempQueueDesignController::class, 'queueDesignEdit'])->name('queueDesignEdit');
    Route::post('/queue-design-update/{id}', [TempQueueDesignController::class, 'queueDesigeupdate'])->name('queueDesignUpdate');
    Route::get('/queue-design-delete/{id}/{queue_id}', [TempQueueDesignController::class, 'queueDesigedelete'])->name('queueDesignDelete');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/addStaff', [AddStaffController::class, 'index'])->name('addStaff');
    Route::post('/save-staff', [AddStaffController::class, 'save'])->name('saveStaff');
    // Route::get('/permissions', [AddStaffController::class, 'index']);
    Route::get('/staff-access-manage', [ShowStaffController::class, 'index'])->name('staff-access-manage');
    Route::get('/edit-user/{id}', [ShowStaffController::class, 'edit'])->name('editStaff');
    Route::post('/update-permissions/{id}', [ShowStaffController::class, 'updatePermissions'])->name('updatePermissions');
    Route::get('/delete-user/{id}', [ShowStaffController::class, 'delete'])->name('deleteStaff');
    Route::get('/activate-deactivate/{id}', [ShowStaffController::class, 'activateDeactivate'])->name('activateDeactivate');

    Route::get('/email-notice', [EmailNoticeController::class, 'index'])->name('email-notice');
    Route::get('/add-email-notice', [EmailNoticeController::class, 'addEmail'])->name('add-email-notice');
    Route::get('/sms-temp-edit/{id}', [EmailNoticeController::class, 'editSms']);
    Route::post('/update-sms-temp/{id}', [EmailNoticeController::class, 'updateSmsTemp']);
    Route::get('/email-temp-edit/{id}', [EmailNoticeController::class, 'editEmail']);
    Route::post('/update-email-temp/{id}', [EmailNoticeController::class, 'updateEmailTemp']);
    Route::post('/save-email-notice', [EmailNoticeController::class, 'saveEmail'])->name('save.email');
    Route::get('/delete-email-notice/{id}', [EmailNoticeController::class, 'deleteEmail'])->name('delete-email-notice');
    // Route::get('/delete-sms-notice/{id}', [EmailNoticeController::class, 'deleteSms'])->name('delete-sms-notice');
    // Route::get('/edit-email-notice/{id}', [EmailNoticeController::class, 'editEmail'])->name('edit-email-notice');
    Route::put('/update-email-notice/{id}', [EmailNoticeController::class, 'updateEmail'])->name('update-email-notice');
    Route::get('/stats-room-view', [StatsroomController::class, 'index'])->name('stats-room-view');
    Route::group(['prefix' => 'developer'], function () {
        Route::get('/', [DeveloperController::class, 'index'])->name('developer');
    });
    Route::get('/in-out-rules', [InOutRulesController::class, 'index'])->name('in-out-rule');

    Route::get('/delete-in-out-rule/{id}', [InOutRulesController::class, 'deleteInOut'])->name('delete-in-out-rule');
    Route::get('/in-out-rules-edit/{id}', [InOutRulesController::class, 'inOutRuleEdit'])->name('edit-in-out-rule');
    Route::post('/update_queue_room_template/{id}', [InOutRulesController::class, 'update'])->name('update_queue_room_template');
    Route::get('/mark-room-completed/{id}', [DeveloperController::class, 'markCompleted'])->name('markCompleted');

    Route::get('/payment', function () {
        return view('test');
    });

    Route::post('/checkout/process/qfpay', [CheckoutController::class, 'process'])->name('checkout.process.qfpay');
    Route::post('/checkout/free-trial', [CheckoutController::class, 'freeTrial'])->name('checkout.freeTrial');
    Route::post('/checkout/process', [StripeController::class, 'createCheckout'])->name('checkout.process');
    Route::get('/checkout/success', [StripeController::class, 'viewSuccessPage'])->name('checkout.success');
    Route::get('/checkout/fail', [StripeController::class, 'viewFailPage'])->name('checkout.fail');
    Route::get('/getPaymentList', [StripeController::class, 'getPaymentList']);

    // Route::get('/checkout/success', function () {
    //     return 'Payment Successful!';
    // })->name('checkout.success');

    Route::get('/checkout/failed', function () {
        return 'Payment Failed!';
    })->name('checkout.failed');

    Route::post('/checkout/notify', function (Request $request) {
        return 'Payment notification!';
    })->name('checkout.notify');
});

Route::any('/admin-logout', [DeveloperAdminController::class, 'logout'])->name('admin.logout');

// Super Admin Routes
Route::group(['prefix' => 'admin'], function () {
    //  Route::group(['middleware' => 'admin'], function () {
    Route::get('/admin-index', function () {
        return view('admin.adminindex');
    })->name('admin-index');

    Route::get('/admin-dash', function () {
        return view('admin.adminindex');
    })->name('admin.dash.route');

    Route::get('/user/profile/update', [DeveloperAdminController::class, 'profileUpdate'])->name('user.profile.update');
    Route::post('/profile/update', [DeveloperAdminController::class, 'adminprofileUpdate'])->name('profile.update22');
    // Route::get('/developers-index', [DeveloperAdminController::class, 'index'])->name('developers-index');
    Route::post('/update-script', [DeveloperAdminController::class, 'updateScript'])->name('update.script');

    Route::get('/subscription-plan', [SubscriptionPlanAdminController::class, 'index'])->name('subscription-plan');
    Route::get('/add-plan', [SubscriptionPlanAdminController::class, 'addsubscriptionplan'])->name('add-plan');
    Route::post('/add-plan', [SubscriptionPlanAdminController::class, 'store'])->name('store-plan');
    Route::get('/edit-subscription-plan/{id}', [SubscriptionPlanAdminController::class, 'editSubscriptionPlan'])->name('edit-subscription-plan');

    Route::put('/update-subscription-plan/{id}', [SubscriptionPlanAdminController::class, 'updateSubscriptionPlan'])->name('update-subscription-plan');
    Route::get('/delete-subscription-plan/{id}', [SubscriptionPlanAdminController::class, 'deleteSubscriptionPlan'])->name('delete-subscription-plan');

    Route::get('/addTermOfUse', [DeveloperAdminController::class, 'addTermOfUse'])->name('admin.addTermOfUse');
    Route::post('/addTermOfUsesave', [DeveloperAdminController::class, 'addTermOfUsesave'])->name('admin.addTermOfUsesave');

    Route::get('/addPrivacyPolicy', [DeveloperAdminController::class, 'addPrivacyPolicy'])->name('admin.addPrivacyPolicy');
    Route::post('/addPrivacyPolicysave', [DeveloperAdminController::class, 'addPrivacyPolicysave'])->name('admin.addPrivacyPolicysave');


    // });
});

//other

Route::get('/temp-manage', function () {
    return view('templateManagement');
});
// Route::get('/in-out-rules', function () {
//     return view('queue-room.in_out_rules');
// });

// Route::get('/stats-edit', function () {
//     return view('stats-room.statsEdit');
// });
Route::get('/stats-edit', [StatsroomEditController::class, 'index']);
Route::post('/stats-filter', [StatsroomEditController::class, 'statFilter'])->name('stats.filter');
Route::get('stats-edit/{id}', [StatsroomEditController::class, 'edit'])->name('stats.edit');
Route::post('/update-max-traffic-visitor/{id}', [StatsroomEditController::class, 'updateMaxTrafficVisitor'])->name('update.max_traffic_visitor');
Route::get('/in-out-rules-edit', function () {
    return view('queue-room.in_out_edit');
});
// Route::get('/temp-queue-design-edit', function () {
//     return view('queue-room.temp_queueDesignEdit');
// });
// Route::get('/in-out-rules', function () {
//     return view('queue-room.in_out_rules');
// });
// Route::get('/temp-queue-design', function () {
//     return view('queue-room.temp_queueDesign');
// });

//Route::get('/temp-queue-design', [TempQueueDesignController::class, 'index']);
