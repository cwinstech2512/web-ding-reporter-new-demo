<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\SampleFixController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FrontWebController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\DispatchController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Auth\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [LoginController::class, 'logout']);

    Route::get('user', [UserController::class, 'current']);
    Route::get('analytics', [AnalyticsController::class, 'index']);
    Route::get('analytics/dispatch', [AnalyticsController::class, 'gDispatch']);
    // Sample block
    Route::get('sample/group', [SampleFixController::class, 'functionPathGroup']);
    Route::get('sample/{corperation}/{command}', [SampleFixController::class, 'active'])->whereAlpha('corperation')->whereAlpha('command');
    //Sample
    Route::post('sample/{corperation}/{command}', [SampleFixController::class, 'store'])->whereAlpha('corperation')->whereAlpha('command');

    Route::patch('settings/profile', [ProfileController::class, 'update']);
    Route::patch('settings/password', [PasswordController::class, 'update']);

    Route::any('account/{id?}', [UserController::class, 'account']);
    // GET /roles : get all roles
    Route::get('roles', [UserController::class, 'roles']);

    //RT
    Route::post('import/rtness', [ImportController::class, 'importRTNess']);
    Route::get('import/data/{type}', [ImportController::class, 'index']);
    Route::get('import/data/{type}/{id}', [ImportController::class, 'show']);
    //RTPR
    Route::post('import/rtprness', [ImportController::class, 'importRTPRNess']);
    //射源表
    Route::post('import/dialength', [ImportController::class, 'importDiaLength']);
    //射源表
    Route::post('import/dialength/search', [ImportController::class, 'searchDiaLength']);
    
    Route::get('dispatch/data/{type}', [DispatchController::class, 'index']);
    Route::get('dispatch/data/{type}/{id}', [DispatchController::class, 'show'])
        ->whereNumber('id');
    //派單
    Route::post('dispatch', [DispatchController::class, 'create']);
    Route::put('dispatch/{id}', [DispatchController::class, 'update']);
    Route::delete('dispatch', [DispatchController::class, 'delete']);
    Route::get('dispatch/{corperation}/{command}/{id}', [DispatchController::class, 'showFunPathDispatch'])
        ->whereAlpha('corperation')
        ->whereAlpha('command')
        ->whereNumber('id');
    Route::put('dispatch/{corperation}/{command}/{id}', [DispatchController::class, 'storeFunPathDispatch'])
        ->whereAlpha('corperation')
        ->whereAlpha('command')
        ->whereNumber('id');
    // Route::post('dispatch/{corperation}/{command}', [DispatchController::class, 'storeFunPathDispatch'])
    //     ->whereAlpha('corperation')
    //     ->whereAlpha('command')
    //     ->whereNumber('id');
    Route::get('dispatch/{corperation}/{command}', [DispatchController::class, 'funPathDispatch'])->whereAlpha('corperation')->whereAlpha('command');

    Route::put('dispatch_verify/{corperation}/{command}/{id}', [DispatchController::class, 'verify'])
        ->whereAlpha('corperation')
        ->whereAlpha('command')
        ->whereNumber('id');
    Route::put('dispatch_verify_back/{corperation}/{command}/{id}', [DispatchController::class, 'verifyBack'])
        ->whereAlpha('corperation')
        ->whereAlpha('command')
        ->whereNumber('id');
    Route::post('dispatch_download/{corperation}/{command}/{id}', [DispatchController::class, 'download'])
        ->whereAlpha('corperation')
        ->whereAlpha('command')
        ->whereNumber('id');    
    Route::put('dispatch_double_verify/{corperation}/{command}/{id}', [DispatchController::class, 'doubleVerify'])
        ->whereAlpha('corperation')
        ->whereAlpha('command')
        ->whereNumber('id');  
    Route::get('data/{type}/{dately}', [ImportController::class, 'dately']);
    Route::get('dispatch_export/{corperation}/{command}/{id}', [DispatchController::class, 'funPathDispatchExport'])
        ->whereAlpha('corperation')
        ->whereAlpha('command')
        ->whereNumber('id');

    Route::get('roles_all', [RoleController::class, 'index']);
    Route::post('roles', [RoleController::class, 'store']);
    Route::put('roles/{role}', [RoleController::class, 'update']);

    Route::get('menu', [MenuController::class, 'index']);
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [RegisterController::class, 'register']);

    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset']);

    Route::post('email/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend']);

    Route::post('oauth/{driver}', [OAuthController::class, 'redirect']);
    Route::get('oauth/{driver}/callback', [OAuthController::class, 'handleCallback'])->name('oauth.callback');


});

Route::group(['middleware' => ['api','front-web']], function () {
    Route::post('front/usertokencheck',[FrontWebController::class, 'vaild']);
    Route::post('front/chat',[FrontWebController::class, 'storeChatLog']);
    Route::get('front/admintokencheck',[FrontWebController::class, 'vaildAdminToken']);

    Route::post('front/join/{opt}',[FrontWebController::class, 'logJoinRoom'])->where('opt', "(guest|service)");
});
