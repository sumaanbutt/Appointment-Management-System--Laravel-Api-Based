<?php

use App\Http\Controllers\Api\AppointmentParticipantController;
use App\Http\Controllers\Api\ChargesController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\LocationServiceController;
use App\Http\Controllers\Api\UserAbilityController;
use App\Http\Controllers\Api\UserShiftScheduleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\BusinessLocationController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AppointmentController;

require __DIR__.'/auth.php';

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware([
    'auth:api'
])->group(function () {

    Route::apiResource(
        'organizations',
        OrganizationController::class
    );

    Route::apiResource(
        'businesses',
        BusinessController::class
    );

    Route::apiResource(
        'business-locations',
        BusinessLocationController::class
    );

    Route::apiResource(
        'services',
        ServiceController::class
    );

    Route::apiResource(
        'users',
        UserController::class
    );

    Route::apiResource(
        'appointments',
        AppointmentController::class
    );

    Route::apiResource(
        'user-shift-schedules',
        UserShiftScheduleController::class
    );

    Route::apiResource(
        'clients',
        ClientController::class
    );

    Route::apiResource(
        'appointment-participants',
        AppointmentParticipantController::class
    );

    Route::apiResource(
        'charges',
        ChargesController::class
    );

    Route::apiResource(
        'invoices',
        InvoiceController::class
    );

    Route::apiResource(
        'user-abilities',
        UserAbilityController::class
    );

    Route::apiResource(
        'location-services',
        LocationServiceController::class
    );
});
