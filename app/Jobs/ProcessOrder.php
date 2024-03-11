<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Variant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $orders;
    /**
     * Create a new job instance.
     */
    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->orders as $order) {
            $result = $this->validateOrder($order);

            if ($result['isValid']) {
                $order->status_id = 3; // 3 chờ xác nhận
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'order_status_id' => 3,
                    'note' => 'Xử lý đơn hàng thành công'
                ]);

                // cập nhật lại số lượng sản phẩm còn lại khi đặt hàng thành công
                $this->handleOrder($order);
            } else {
                $order->status_id = 10; // 10 đơn hàng xử lý thất bại
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'order_status_id' => 10,
                    'note' => $result['error']
                ]);
            }
            $order->save();
        }
    }

    public function validateOrder($order){
        $result = [
            'isValid' => true,
            'error' => ''
        ];

        foreach ($order->details as $order_detail) {
            $variant = Variant::where('id', '=', $order_detail->variant_id)->first();

            // kiểm ra số lượng sản phẩm còn lại
            if ($variant->quantity < $order_detail->quantity) {
                $result['isValid'] = false;
                $result['error'] = 'Sản phẩm không đủ số lượng';
            }

//            // kiểm tra mã giảm giá
            if ($order_detail->promotion) {

                if ($order_detail->promotion->status != 'happenning') {
                    $result['isValid'] = false;
                    $order_detail->promotion->status == 'finished' ?
                        $result['error'] = 'Đợt giảm giá đã hết hạn' :
                        $result['error'] = 'Phiếu giảm giá chưa thể áp dụng';
                }
            }
        }

        return $result;
    }

    public function handleOrder($order): void {
        foreach ($order->details as $order_detail) {
            $variant = Variant::where('id', '=', $order_detail->variant_id)->first();
            $variant->quantity = $variant->quantity - $order_detail->quantity;
            $variant->save();
        }
    }
}
