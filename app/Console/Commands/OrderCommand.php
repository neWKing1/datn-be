<?php

namespace App\Console\Commands;

use App\Jobs\ProcessOrder;
use App\Models\Bill;
use Illuminate\Console\Command;

class OrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:order-command';

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
        $orders = Bill::query()
            ->where('status_id', '=', '101')
            ->where('is_process', '=', 0)
            ->get();

        if ($orders->isNotEmpty()){
            dispatch(new ProcessOrder($orders))->onQueue('order');

            foreach ($orders as $order) {
                $order->is_process = 1;
                $order->save();
            }
        }
    }
}
