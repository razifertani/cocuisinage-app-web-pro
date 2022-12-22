<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CollegueController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\EstablishmentController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\PlanningController;
use App\Http\Controllers\Api\ProfessionalController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\TaskController;
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

Route::post('login', [AuthController::class, 'login']);

Route::post('register', [AuthController::class, 'register']);

Route::post('password/email', [ForgotPasswordController::class, 'send_reset_password_email']);

Route::post('password/verify_code', [ForgotPasswordController::class, 'verify_code']);

Route::post('password/reset', [ForgotPasswordController::class, 'reset_password']);

Route::post('/collegue/accept_invitation/{url_token}', [CollegueController::class, 'accept_collegue_invitation']);

Route::group(['middleware' => 'auth:sanctum'], function () {

    /*
     * Test related routes
     */

    Route::post('test', [AuthController::class, 'test']);

    /*
     * Authenticated related routes
     */

    Route::get('config/mobile', [AuthController::class, 'config_mobile']);

    Route::get('/user', [ProfessionalController::class, 'user']);

    Route::post('/user/{id}', [ProfessionalController::class, 'update']);

    Route::post('/user/{id}/toggle_notification_type_active_param', [ProfessionalController::class, 'toggle_notification_type_active_param']);

    Route::delete('/user/{id}', [ProfessionalController::class, 'delete']);

    /*
     * Collegue related routes
     */

    Route::post('/collegue/invite', [CollegueController::class, 'invite_collegue']);

    Route::post('/collegue/toggle_permission', [CollegueController::class, 'toggle_permission']);

    /*
     * Planning related routes
     */

    Route::post('/plannings', [PlanningController::class, 'store']);

    Route::post('/planning/{id}', [PlanningController::class, 'update']);

    Route::post('/planning/{id}/update_time', [PlanningController::class, 'update_time']);

    /*
     * Task related routes
     */

    Route::post('/tasks', [TaskController::class, 'store']);

    Route::post('/tasks/{id}', [TaskController::class, 'update']);

    /*
     * Authenticated related routes
     */

    Route::get('/logout', [AuthController::class, 'logout']);

    /*
     * Roles related routes
     */

    Route::post('/roles', [RoleController::class, 'store']);

    Route::post('/roles/{id}', [RoleController::class, 'update']);

    Route::delete('/roles/{id}', [RoleController::class, 'delete']);

    /*
     * Companies related routes
     */

    Route::post('/company/{id}', [CompanyController::class, 'update']);

    /*
     * Establishments related routes
     */

    Route::post('/establishments', [EstablishmentController::class, 'store']);

    Route::post('/establishment/{id}', [EstablishmentController::class, 'update']);

    Route::delete('/establishment/{id}', [EstablishmentController::class, 'delete']);

    /*
     * Reservations related routes
     */

    Route::post('/reservations', [ReservationController::class, 'store']);

    Route::post('/reservation/{id}', [ReservationController::class, 'update']);

    Route::post('/reservation/{id}/assign_table_to_reservation/{table_id}', [ReservationController::class, 'assign_table_to_reservation']);

    Route::delete('/reservation/{id}', [ReservationController::class, 'delete']);

    /*
     * Tables related routes
     */

    Route::post('/tables', [TableController::class, 'store']);

    Route::post('/table/{id}', [TableController::class, 'update']);

    Route::delete('/table/{id}', [TableController::class, 'delete']);

});

Route::any('/{any}', function () {
    return response()->json([
        'error' => true,
        'message' => url()->to('/'),
    ], 404);
})->where('any', '.*');
