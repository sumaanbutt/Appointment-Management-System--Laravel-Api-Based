<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\AppointmentRecurrence;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:auto-reschedule-appointments')]
#[Description('Command description')]
class AutoRescheduleAppointments extends Command
{
    protected $signature = 'appointments:auto-reschedule';

    public function handle()
    {
        $recurrences = AppointmentRecurrence::whereNotNull(
            'reschedule_after_days'
        )->get();

        foreach ($recurrences as $recurrence) {

            $appointment = Appointment::where(
                'code',
                $recurrence->appointment_code
            )->first();

            if (!$appointment) {
                continue;
            }

            $rescheduleDate = Carbon::parse(
                $recurrence->created_at
            )->addDays(
                $recurrence->reschedule_after_days
            )->startOfDay();

            if (
                now()->startOfDay()->greaterThanOrEqualTo($rescheduleDate) &&
                $appointment->status === 'PENDING'
            ) {

                $startTime = Carbon::parse(
                    $appointment->start_date
                )->format('H:i:s');

                $endTime = Carbon::parse(
                    $appointment->end_date
                )->format('H:i:s');

                $newStartDate = Carbon::parse(
                    $rescheduleDate->toDateString() . ' ' . $startTime
                );

                $newEndDate = Carbon::parse(
                    $rescheduleDate->toDateString() . ' ' . $endTime
                );

                $appointment->update([
                    'start_date' => $newStartDate,
                    'end_date' => $newEndDate,
                    'status' => 'RESCHEDULED',
                ]);
            }
        }

        $this->info('Auto reschedule completed.');
    }
}
