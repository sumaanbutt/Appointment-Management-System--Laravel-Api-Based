<?php

use App\Http\Controllers\Api\AppointmentChargeController;
use App\Http\Controllers\Api\AppointmentDiscountController;
use App\Http\Controllers\Api\AppointmentHistoryController;
use App\Http\Controllers\Api\AppointmentParticipantController;
use App\Http\Controllers\Api\AppointmentQuotationController;
use App\Http\Controllers\Api\AppointmentRecurrenceController;
use App\Http\Controllers\Api\AppointmentRequestController;
use App\Http\Controllers\Api\AppointmentServiceController;
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

Route::middleware(['auth:api'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware([
    'auth:api'
])->group(function () {

    Route::apiResource(
        'organizations',
        OrganizationController::class
    );

    Route::patch(
        'organizations/{organization}/status',
        [ OrganizationController::class, 'updateStatus']
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

    Route::get(
        'appointments/{appointment}/availability',
        [AppointmentController::class,'availability']
    );

    Route::post(
        'appointments/{appointment}/approve',
        [AppointmentController::class,'approve']
    );

    Route::patch(
        'appointments/{appointment}/status',
        [AppointmentController::class,'updateStatus']
    );

    Route::get(
        'appointments/{appointment}/history',
        [AppointmentController::class,'history']
    );

    Route::post(
        'appointments/{appointment}/reschedule',
        [AppointmentController::class,'reschedule']
    );

    Route::get(
        'user-shift-schedules/check-staff-availability',
        [UserShiftScheduleController::class, 'checkStaffAvailability']
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

    Route::apiResource(
        'appointment-charges',
        AppointmentChargeController::class
    );

    Route::apiResource(
        'appointment-discounts',
        AppointmentDiscountController::class
    );

    Route::apiResource(
        'appointment-histories',
        AppointmentHistoryController::class
    );

    Route::apiResource(
        'appointment-quotations',
        AppointmentQuotationController::class
    );

    Route::apiResource(
        'appointment-recurrences',
        AppointmentRecurrenceController::class
    );

    Route::apiResource(
        'appointment-requests',
        AppointmentRequestController::class
    );

    Route::apiResource(
        'appointment-services',
        AppointmentServiceController::class
    );
});
