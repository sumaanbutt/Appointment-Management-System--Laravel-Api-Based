<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\AppointmentRecurrence;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:generate-recurring-appointments')]
#[Description('Command description')]
class GenerateRecurringAppointments extends Command
{
    protected $signature = 'appointments:generate-recurrence';

    public function handle()
    {
        $recurrences = AppointmentRecurrence::where(
            'status',
            'ACTIVE'
        )->get();

        foreach ($recurrences as $recurrence) {

            $appointment = Appointment::where(
                'code',
                $recurrence->appointment_code
            )->first();

            if (!$appointment) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | SPECIFIC DATE
            |--------------------------------------------------------------------------
            */

            if ($recurrence->recurrence_date) {

                if (
                    now()->toDateString() >=
                    Carbon::parse(
                        $recurrence->recurrence_date
                    )->toDateString()
                ) {

                    Appointment::create([
                        'business_code' => $appointment->business_code,
                        'client_code' => $appointment->client_code,
                        'location_code' => $appointment->location_code,
                        'appointment_start_date' =>
                            Carbon::parse(
                                $recurrence->recurrence_date
                            )
                                ->setTimeFrom(
                                    Carbon::parse(
                                        $appointment->appointment_start_date
                                    )
                                ),

                        'appointment_end_date' =>
                            Carbon::parse(
                                $recurrence->recurrence_date
                            )
                                ->setTimeFrom(
                                    Carbon::parse(
                                        $appointment->appointment_end_date
                                    )
                                ),

                        'status' => 'PENDING',
                    ]);

                    $recurrence->update([
                        'status' => 'INACTIVE'
                    ]);
                }

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | DAILY / WEEKLY / MONTHLY
            |--------------------------------------------------------------------------
            */

            $startDate = Carbon::parse(
                $appointment->start_date
            );

            $endDate = Carbon::parse(
                $appointment->end_date
            );

            for (
                $i = 1;
                $i <= $recurrence->recurrence_value;
                $i++
            ) {

                $newStart = clone $startDate;
                $newEnd = clone $endDate;

                switch (
                $recurrence->recurrence_uom
                ) {

                    case 'DAILY':

                        $newStart->addDays($i);
                        $newEnd->addDays($i);

                        break;

                    case 'WEEKLY':

                        $newStart->addWeeks($i);
                        $newEnd->addWeeks($i);

                        break;

                    case 'MONTHLY':

                        $newStart->addMonths($i);
                        $newEnd->addMonths($i);

                        break;

                    default:
                        continue 2;
                }

                Appointment::firstOrCreate(

                    [
                        'business_code' => $appointment->business_code,
                        'appointment_start_date' => $newStart,
                        'appointment_end_date' => $newEnd,
                    ],

                    [
                        'client_code' => $appointment->customer_code,
                        'location_code' => $appointment->location_code,
                        'status' => 'PENDING',
                    ]
                );
            }

            $recurrence->update([
                'status' => 'INACTIVE'
            ]);
        }

        $this->info(
            'Recurring appointments generated.'
        );
    }
}
