<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\BillHistory;
use App\Models\BillPromotion;
use App\Models\BillStatus;
use App\Models\Promotion;
use App\Models\Variant;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $orders = Bill::query();

            if ($status > 0) {
                $orders->where('status_id', '=', $status);
            }
            $orders->where('customer_id', '=', $user_id);
//            $orders->whereNull('seller_by');

            $orders = $orders
                ->whereHas('billDetails')
                ->with('payment')
                ->with('status_histories.status')
                ->with('status')
                ->with('billDetails.variant.images')
                ->with('billDetails.variant.product')
                ->orderBy('id', 'DESC')
                ->get();
            return \response()->json($orders, 200);
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
            $order_data = $request->only(
                'customer_id', 'voucher_id',
                'money_ship', 'email', 'address',
                'address_information', 'money_reduce',
                'total_money', 'phone_number', 'customer_name', 'note');

            $order_data['type'] = 'delivery';
            $order_data['payment_id'] = $request->payment['id'];
            $order_data['status_id'] = $request->payment['id'] == 100 ? 102 : 100;
            $order_data['status'] = 'active';
            $order_data['code'] = 'HD' . uniqid();
            $order_data['timeline'] = '2';
            $order = Bill::create($order_data);
//            return $order;
            // thêm sản phẩm cho đơn hàng
            foreach ($request->order_details as $detail) {
                $order_detail = BillDetail::create([
                    'bill_id' => $order->id,
                    ...$detail
                ]);

                // thêm đợt giảm giá trường hợp sản phẩm đang được giảm
                if ($order_detail && $detail['promotion_id']) {
                    BillPromotion::create([
                       'bill_detail_id' => $order_detail->id,
                       'promotion_id' =>  $detail['promotion_id']
                    ]);
                }
            }
            // thanh toán trực tuyến nếu người dùng chọn lựa chọn
            $payment = null;
            if ($request->payment['id'] == 101) {
                $order_payment['id'] = $order->code;
                $order_payment['return_payment'] = $request->return_payment;
                $order_payment['amount'] = $order->total_money + $order->money_ship - $order->money_reduce;
                $payment = (new PaymentController())->vnpay_payment($order_payment);
            }

            BillHistory::create([
                'note' => "Tạo đơn hàng",
                'status' => '1',
                'bill_id' => $order->id,
                'created_by' => "Khách hàng",
                'status_id' => $request->payment['id'] == 100 ? 102 : 100,
            ]);

            $validate = $this->validateOrder($request);

            if ($validate['isValid']) {
                foreach ($request->order_details as $order_detail) {
                    $variant = Variant::where('id', '=', $order_detail['variant_id'])->first();
                    $variant->quantity = $variant->quantity - $order_detail['quantity'];
                    $variant->save();
                }
//                if (!$payment) {
                    BillHistory::create([
                        'note' => "Chờ xác nhận",
                        'status' => '2',
                        'bill_id' => $order->id,
                        'created_by' => "Khách hàng",
                        'status_id' => 102
                    ]);
//                }
//                else {
//                    BillHistory::create([
//                        'note' => "Chờ thanh toán",
//                        'status' => '2',
//                        'bill_id' => $order->id,
//                        'created_by' => "Khách hàng",
//                        'status_id' => 100
//                    ]);
//                }
            } else {
                BillHistory::create([
                    'note' => $validate['message'],
                    'status' => '7',
                    'bill_id' => $order->id,
                    'created_by' => "Hệ thống",
                    'status_id' => 109
                ]);
            }

            return \response()->json([
                'redirect' => $payment,
                'order' => $order,
                'message' => 'Đặt hàng thành công'
            ], 201);
        } catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $order = BillDetail::with('variant')
                ->with('variant.images')
                ->with('variant.color')
                ->with('variant.size')
                ->with('variant.product')
                ->with('promotion')
                ->where('bill_id', $id)
                ->get();
            if ($order) {
                return \response()->json($order, 200);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $order = Bill::find($id);

            if ($request->method() == 'PATCH') {
                if (isset($request->status_id) && $request->status_id) {
                    $order->status_id = $request->status_id;
                }

                BillHistory::create([
                    'bill_id' => $order->id,
                    'status_id' => $request->status_id ?? $order->status_id,
                    'note' => $request->note,
                    'created_by' => $request->created_by,
                ]);

                $order->save();
                return \response()->json($order, 200);
            } else {
                if ($order) {
                    // trường hợp thông tin đơn hàng thay đổi
                    if ($request->has('orderData')) {
                        $order->update($request->orderData);
                    }

                    // trường hợp thay đổi số lượng sản phẩm
                    if ($request->has('orderDetailChange')) {
                        foreach ($request->orderDetailChange as $od) {
                            $order_detail = BillDetail::where('id', $od->id)->first();
                            $variant = Variant::where('id', $order_detail->variant_id)->first();
                            $old_qty = $order_detail->quantity;
                            $new_qty = $od->quantity;
                            $qty = $new_qty - $old_qty;


                        }
                    }
                    return $order;
                }
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
        $order = Bill::find($id);

        if ($order) {
            $order->update([
                'status_id' => '108',
                'timeline' => '7'
            ]);

            // hoàn số lượng sản phẩm
            $order_details = BillDetail::where('bill_id', $order->id)->get();
            foreach ($order_details as $detail) {
                $variant = Variant::where('id', $detail->variant_id)->first();
                $variant->quantity = $variant->quantity + $detail->quantity;
                $variant->save();
            }

            BillHistory::create([
                'bill_id' => $order->id,
                'status_id' => '108',
                'status' => '7',
                'note' => 'Đã hủy',
                'created_by' => 'Khách hàng'
            ]);
            return \response()->json(true, 204);
        }
        return \response()->json(false, 404);
    }
    public function return_order($id) {
        $order = Bill::find($id);
        if ($order) {
            $order->update(['status_id' => '107']);
            BillHistory::create([
                'bill_id' => $order->id,
                'status_id' => '108',
                'note' => 'Chờ người bán xác nhận',
                'created_by' => 'Khách hàng'
            ]);
            return \response()->json(true, 204);
        }
        return \response()->json(false, 404);
    }
    public function status(){
        return \response()->json(BillStatus::all(), 200);
    }

    public function validateOrder($request)
    {
        $result = [
            'isValid' => true,
            'message' => 'Đặt hàng thành công',
        ];

        foreach ($request->order_details as $order_detail) {
            $variant = Variant::where('id', '=', $order_detail['variant_id'])->first();

            // kiểm ra số lượng sản phẩm còn lại
            if ($variant->quantity < $order_detail['quantity']) {
                $result['isValid'] = false;
                $result['message'] = 'Sản phẩm không đủ số lượng';
            }
            // kiểm tra đợt giảm giá
            if ($order_detail['promotion_id']) {
                $promotion = Promotion::where('id', '=', $order_detail['promotion_id'])->first();
                if ($promotion->status != 'happenning') {
                    $result['isValid'] = false;
                    $promotion->status == 'finished' ?
                        $result['message'] = 'Đợt giảm giá đã hết hạn' :
                        $result['message'] = 'Đợt giảm giá chưa thể áp dụng';
                }
            }
        }
        // kiểm tra phiếu giảm giá
        if ($request->voucher_id) {
            $voucher = Voucher::where('id', '=', $request->voucher_id)->first();
            if ($voucher->quantity < 1) {
                $result['isValid'] = false;
                $result['message'] = 'Voucher đã hết!';
            }
        }

        return $result;
    }
}
