<?php

namespace App\Jobs;

use App\Mail\PlanningReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPlanningReminderJob implements ShouldQueue
{
    use  Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $planning;
    protected $userEmail;

    /**
     * Create a new job instance.
     */
    public function __construct($planning, $userEmail)
    {
        $this->planning = $planning;
        $this->userEmail = $userEmail;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Mail::to($this->userEmail)->send(new PlanningReminder($this->planning, $this->userEmail));
    }
}
