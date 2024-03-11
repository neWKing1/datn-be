<?php

namespace App\Console\Commands;

use App\Jobs\ProcessOrder;
use App\Models\Order;
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
    protected $description = 'Đẩy đơn hàng ở trạng thái đang xử lý vào queue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::where('status_id', '=', 2) // 2 đơn hàng đang chờ xử lý
            ->where('is_process', '=', 0) // 0  đơn hàng chưa được đẩy vào queue
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
