<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPromotion;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Product;
use App\Models\PromotionVariant;
use App\Models\User;
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

            $orders = Order::query();

            if ($status > 0) {
                $orders->where('status_id', '=', $status);
            }

            if ($request->has('type') && $request->type > 0) {
                if ($request->type == 1) {
                    $orders->whereIn('payment_id', [1, 2]);
                } else if ($request->type == 2) {
                    $orders->whereIn('payment_id', [3, 4]);
                }
            }

            $orders = $orders
                ->with('payment')
                ->with('status')
                ->with('details')
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
            return \response()->json(Order::create($request->all()), 200);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $order = Order::with('details')
                ->with('details.promotion')
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
            $order = Order::with(['details', 'variants'])->find($id);
            if ($order) {
                if ($request->method() == 'PATCH') {
                    $order->update($request->all());

                    return $order;

                } else if ($request->method() == 'PUT') {
                    $is_payment = $request->payment_id == 3 ? true : false;
                    $order->update([
                        ...$request->all(),
                        'is_payment' => $is_payment,
                    ]);

                    // cập nhật lại chi tiết đơn hàng trường hợp có thay đổi
                    if ($request->has('details') && $request->details) {
                        $this->handleUpdateDetailOrder($id, $request->details, $request->finish);
                    }

                    $payment = null;
                    if ($request->payment_id == 4) {
                        $order['id'] = $id;
                        $order['return_payment'] = $request->return_payment;
                        $order['amount'] = 0 + $request->shipping_cost - $request->order_discount;
                        foreach ($request->details as $detail) {
                            $order['amount'] += $detail['price'] * $detail['quantity'];
                        }
                        $payment = (new PaymentController())->vnpay_payment($order);
                    }
                    return \response()->json([
                        'data' => $order,
                        'redirect' => $payment,
                        'bill' => $request->finish
                    ], 200);
                } else {
                    return $order;
                }
            }
            return \response()->json([], 404);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function status()
    {
        return \response()->json(OrderStatus::all());
    }

    public function payments()
    {
        try {
            $payment_methods = Payment::query()->whereIn('id', [3, 4])->get();
            return \response()->json($payment_methods);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

    }

    public function customer(Request $request)
    {
        try {
            $customer = User::query()->where('role', '=', 'customer')
                ->where('status', '=', 'active');

            if ($request->has('query')) {
                $customer->where('email', 'LIKE', "%{$request->query('query')}%");
            }

            return \response()->json($customer->get(), 200);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function order_product(Request $request)
    {
        try {
            $search_query = $request->query('query');

            $variants = Variant::query()
                ->whereHas('product', function ($query) use ($search_query) {
                    $query->where('is_active', '=', '1');
                    $query->where('name', 'LIKE', "%{$search_query}%");
                })
                ->with(['color', 'size', 'images', 'promotions', 'product'])
                ->get();
            return response()->json($variants, 200);

        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    public function handleUpdateDetailOrder($id, $details, $finish = null)
    {
        $order_details = OrderDetail::query()->where('order_id', $id)->get();

        if ($order_details) {
            foreach ($order_details as $detail) {
                if ($detail->order_promotion) {
                    $detail->order_promotion->delete();
                }
                $detail->delete();
            }
        }

        if ($details) {
            foreach ($details as $detail) {

                $order_detail_id = OrderDetail::insertGetId([
                    'order_id' => $id,
                    'variant_id' => $detail['id'],
                    'name' => $detail['product']['name'],
                    'unit_price' => $detail['price'],
                    'quantity' => $detail['quantity'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                // trừ số lượng sản phẩm khi đặt hàng thành công
                if ($finish && $order_detail_id) {
                    $variant = Variant::find($detail['id']);
                    $variant ? $variant->quantity = $variant->quantity - $detail['quantity'] : null;
                    $variant->save();
                }
                // thêm đợt giảm giá trường hợp sản phẩm được áp dung
                if ($detail['promotions']) {
                    OrderPromotion::create([
                        'order_detail_id' => $order_detail_id,
                        'promotion_id' => $detail['promotions'][0]['id']
                    ]);
                }
            }
        }
        return true;
    }

}
