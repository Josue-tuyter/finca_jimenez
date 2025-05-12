<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Harvest_planning;
use App\Models\Drying_Planning;
use App\Models\Fermentation_Planning;
use App\Mail\PlanningReminder;
use App\Models\User;

class SendPlanningReminders extends Command
{
    protected $signature = 'send:planning-reminders';
    protected $description = 'Enviar recordatorios por correo de planificaciones próximas a finalizar';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Obtener planificaciones que finalizan mañana
        $tomorrow = now()->addDay()->toDateString();

        // Obtener planificaciones de cosecha, secado y fermentación
        $harvestPlannings = Harvest_planning::where('date_end', $tomorrow)->get(); 
        $dryingPlannings = Drying_Planning::where('D_date_end', $tomorrow)->get();
        $fermentationPlannings = Fermentation_Planning::where('F_date_end', $tomorrow)->get();

        // Enviar recordatorios para cada tipo de planificación
        $this->sendReminders($harvestPlannings);
        $this->sendReminders($dryingPlannings);
        $this->sendReminders($fermentationPlannings);

        $this->info('Recordatorios enviados correctamente.');
    }

    /**
     * Enviar recordatorios a los usuarios de las planificaciones.
     */
    private function sendReminders($plannings)
    {
        // Obtener todos los usuarios con roles específicos
        $usersWithRoles = User::role(['Admin', 'Super admin', ' Register'])->get();

        // Iterar sobre las planificaciones
        foreach ($plannings as $planning) {
            // Enviar correo a los usuarios asociados a la planificación
            foreach ($planning->users ?? [] as $user) {
                if ($user->email) {
                    Mail::to($user->email)->send(new PlanningReminder($planning));
                }
            }

            // Enviar correo a los usuarios con roles
            foreach ($usersWithRoles as $user) {
                if ($user->email) {
                    Mail::to($user->email)->send(new PlanningReminder($planning));
                }
            }
        }
    }
}
