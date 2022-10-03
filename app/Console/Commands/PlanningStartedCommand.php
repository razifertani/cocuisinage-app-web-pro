<?php

namespace App\Console\Commands;

use App\Models\Planning;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PlanningStartedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'planning:started';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Planning started';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Planning::whereDate('day', now())
            ->whereTime('should_start_at', Carbon::now()->format('H:i'))
            ->get()
            ->each(function ($planning) {
                logger("Sending FCM to " . $planning->professional_id . ", Planning " . $planning->id . " started !");
                (new FCMService())->sendFCM($planning->professional_id, 'Planning commencé', 'Votre journée commence maintenant !');
            });
    }
}
