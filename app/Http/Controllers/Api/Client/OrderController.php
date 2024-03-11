<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPromotion;
use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use App\Models\Variant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $status = $request->status;
            $user_id = $request->has('user') ? $request->user['id'] : null;
            $orders = Order::query();

            if ($status > 0) {
                $orders->where('status_id', '=', $status);
            }
            $orders->where('user_id', '=', $user_id);
            $orders->whereNull('seller_by');

            $orders = $orders
                ->whereHas('details')
                ->with('payment')
                ->with('status')
                ->with('status_histories')
                ->with('status_histories.status')
                ->with('details.variant.images')
                ->orderBy('id', 'DESC')
                ->get();
            return \response()->json($orders);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $isOrder = $this->orderValidated($request);
            if ($isOrder) {
                $order_data = [
                    'user_id' => $request->user_id,
                    'recipient_name' => $request->recipient_name,
                    'recipient_phone' => $request->recipient_phone,
                    'recipient_email' => $request->recipient_email,
                    'recipient_city' => $request->recipient_city,
                    'recipient_district' => $request->recipient_district,
                    'recipient_ward' => $request->recipient_ward,
                    'recipient_detail' => $request->recipient_detail,
                    'recipient_note' => $request->recipient_note,
                    'shipping_by' => $request->shipping_by,
                    'shipping_cost' => $request->shipping_cost,
                    'payment_id' => $request->payment['id'],
                    'status_id' => $request->payment['id'] == 2 ? 1 : 2, // payment['id'] == 2 thanh toán qua vnpay
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $order = Order::create($order_data);

                // insert chi tiết đơn hàng
                foreach ($request->order_details as $detail) {
                    $order_detail_id = OrderDetail::insertGetId([
                        'order_id' => $order->id,
                        'variant_id' => $detail['variant_id'],
                        'name' => $detail['name'],
                        'unit_price' => $detail['unit_price'],
                        'quantity' => $detail['quantity']
                    ]);
                    // thêm khuyến mãi trường hợp sản phẩm đang được giảm giá
                    if ($order_detail_id && $detail['promotion_id']) {
                        OrderPromotion::create([
                            'order_detail_id' => $order_detail_id,
                            'promotion_id' =>  $detail['promotion_id']
                        ]);
                    }
                }

                // thanh toán trực tuyến nếu người dùng chọn lựa chọn
                $payment = null;
                if ($request->payment['id'] == 2) {
                    $order['id'] = $order->id;
                    $order['return_payment'] = $request->return_payment;
                    $order['amount'] = 0 + $request->shipping_cost;
                    foreach ($request->order_details as $detail) {
                        $order['amount'] += $detail['unit_price'] * $detail['quantity'];
                    }
                    $payment = (new PaymentController())->vnpay_payment($order);
                }

                return \response()->json([
                    'redirect' => $payment,
                    'order_id' => $order->id,
                    'message' => 'Đặt hàng thành công'
                ], 201);
            } else {
                return \response()->json([
                    'message' => 'Đặt hàng thất bại'
                ], 404);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $order = OrderDetail::with('variant')
                ->with('variant.images')
                ->with('variant.color')
                ->with('variant.size')
                ->with('promotion')
                ->where('order_id', $id)
                ->get();
            if ($order) {
                return \response()->json($order);
            }
            return [];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $order = Order::with('details')
                ->with('details.variant.color')
                ->with('details.variant.size')
                ->with('details.variant.images')
                ->with('payment')
                ->find($id);

            if ($order) {
                return \response()->json($order);
            }
            return [];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $order = Order::find($id);
            if ($order) {
                // cập nhật đơn hàng
                if ($request->orderChange) {
                    $order->update($request->orderChange);
                }
                if ($request->orderHistory) {
                    OrderStatusHistory::create($request->orderHistory);
                }
                // cập nhật chi tiết đơn hàng
                if ($request->orderDetailChange) {
                    foreach($request->orderDetailChange as $detail) {
                        $order_detail = OrderDetail::find($detail['id']);
                        if ($order_detail) {
                            $order_detail->variant->update([
                                'quantity' => $order_detail->variant->quantity - ($detail['quantity'] - $order_detail->quantity) >= 0 ?
                                    $order_detail->variant->quantity - ($detail['quantity'] - $order_detail->quantity) :
                                    $order_detail->variant->quantity - ($detail['quantity'] - $order_detail->quantity) * -1
                            ]);

                            $order_detail->update($detail);
                        }
                    }
                }
                return $order;

            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::find($id);

    }

    public function status()
    {
        return \response()->json(OrderStatus::all());
    }

    public function status_histories(Request $request)
    {
        try {
            return OrderStatusHistory::where('order_id', $request->order_id)
                ->with('status')
                ->orderBy('id', 'DESC')
                ->get();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateStatusHistory(Request $request)
    {
        try {
            $history = OrderStatusHistory::create([
                'order_id' => $request->order_id,
                'order_status_id' => $request->status_id,
                'note' => $request->note ?? null,
            ]);

            return \response()->json($history, 201);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function orderValidated($request)
    {
        // kiểm tra số lượng sản phẩm còn lại
        $isValid = true;
        foreach ($request->order_details as $detail) {
            $variant = Variant::find($detail['variant_id']);
            if ($variant) {
                $variant->quantity < $detail['quantity'] ? $isValid = false : null;
            }
        }

        return $isValid;
    }
}
