<?php

namespace App\Console\Commands;

use App\Models\Promotion;
use Illuminate\Console\Command;

class PromotionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:promotion-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info('Command executed at: ' . now());
        $date = date('Y-m-d H:i:s', strtotime(now()));
        info($date);

        $promotions = Promotion::where('status', '!=', 'finished')->get();

        $currentTime = now();
        foreach ($promotions as $promotion) {
            $this->updatePromotionStatus($promotion, $currentTime);
        }
    }
    protected function updatePromotionStatus($promotion, $currentTime)
    {
        if ($currentTime > $promotion->start_date && $currentTime < $promotion->end_date) {
            $promotion->update(['status' => 'happenning']);
        } else if ($currentTime > $promotion->end_date) {
            $promotion->update(['status' => 'finished']);
        } else if ($currentTime < $promotion->start_date) {
            $promotion->update(['status' => 'upcoming']);
        }
    }
}
