<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\AppointmentRecurrence;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:auto-cancel-appointments')]
#[Description('Command description')]
class AutoCancelAppointments extends Command
{
    protected $signature = 'appointments:auto-cancel';

    public function handle()
    {
        $recurrences = AppointmentRecurrence::whereNotNull(
            'auto_cancel_after_days'
        )->get();

        foreach ($recurrences as $recurrence) {

            $appointment = Appointment::where(
                'code',
                $recurrence->appointment_code
            )->first();

            if (!$appointment) {
                continue;
            }

            $cancelDate = Carbon::parse(
                $recurrence->created_at
            )->addDays(
                $recurrence->auto_cancel_after_days
            );#->startOfDay();

            if (
                now()->greaterThanOrEqualTo($cancelDate) &&             #->startOfDay() {after now()};
                in_array(
                    $appointment->status,
                    ['PENDING']
                )
            )
//                dd([
//                    'created_at' => $recurrence->created_at,
//                    'cancel_date' => $cancelDate,
//                    'now' => now(),
//                ]);
            {
                $appointment->update([
                    'status' => 'CANCELLED'
                ]);
            }
        }

        $this->info('Auto cancel completed.');
    }
}
