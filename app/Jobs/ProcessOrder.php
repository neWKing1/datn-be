<?php

namespace App\Jobs;

use App\Models\BillHistory;
use App\Models\Variant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
            $status_id = 109;
            if ($result['isValid']) {
                $status_id = 102;
            }
            BillHistory::create([
                'bill_id' => $order->id,
                'status_id' => $status_id,
                'created_by' => 'Hệ thống',
                'note' => $result['message']
            ]);
            $order->status_id = $status_id;
            $order->save();
        }
    }

    public function validateOrder($order)
    {
        $result = [
            'isValid' => true,
            'message' => 'Đặt hàng thành công',
        ];

        foreach ($order->billDetails as $order_detail) {
            $variant = Variant::where('id', '=', $order_detail->variant_id)->first();

            // kiểm ra số lượng sản phẩm còn lại
            if ($variant->quantity < $order_detail->quantity) {
                $result['isValid'] = false;
                $result['message'] = 'Sản phẩm không đủ số lượng';
            }

            // kiểm tra mã giảm giá
            if ($order_detail->promotion) {

                if ($order_detail->promotion->status != 'happenning') {
                    $result['isValid'] = false;
                    $order_detail->promotion->status == 'finished' ?
                        $result['message'] = 'Đợt giảm giá đã hết hạn' :
                        $result['message'] = 'Đợt giảm giá chưa thể áp dụng';
                }
            }
        }

        return $result;
    }
}
