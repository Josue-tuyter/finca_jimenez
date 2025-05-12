<?php

namespace App\Console;

use App\Models\Harvest_planning;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Define the application's command schedule.
     */
    // protected function schedule(Schedule $schedule): void
    // {
    //     // Programar el envío de recordatorios para planificaciones de cosecha
    //     $schedule->call(function () {
    //         $today = now()->startOfDay();
    //         $tomorrow = $today->addDay();

    //         // Obtener planificaciones que terminan mañana
    //         $harvestPlannings = Harvest_planning::whereDate('date_end', $tomorrow)->get();

    //         foreach ($harvestPlannings as $planning) {
    //             $user = $planning->user; // Asegúrate de que la relación con User esté configurada
    //             $worker = $planning->worker; // Relación con Worker

    //             // Enviar correo a ambos
    //             $recipients = collect([$user, $worker])->filter(); // Filtrar nulos
    //             foreach ($recipients as $recipient) {
    //                 Mail::send('emails.planning_reminder', ['planning' => $planning], function ($message) use ($recipient) {
    //                     $message->to($recipient->email)
    //                         ->subject('Recordatorio: Planificación próxima a finalizar');
    //                 });
    //             }
    //         }
    //     })->dailyAt('08:00'); // Puedes ajustar el horario según sea necesario
    // }

    protected function schedule(Schedule $schedule): void
{
    // Programar el envío de recordatorios para planificaciones de cosecha, secado y fermentación
    $schedule->command('send:planning-reminders')->dailyAt('08:00'); // Ejecución diaria a las 8:00 am
}

}
