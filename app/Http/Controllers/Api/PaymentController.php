<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $vnp_HashSecret = "UWUKJJXNQHENGXXJJBKWPUTPHOQUWGSH";
    protected $vnp_TmnCode = "WGDECDU1";

    public function vnpay_payment($order)
    {

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = $order['return_payment'];
        $vnp_TmnCode = $this->vnp_TmnCode;
        $vnp_HashSecret = $this->vnp_HashSecret;

        $vnp_TxnRef = $order['id'];
        $vnp_OrderInfo = "mua_hang_truc_tuyen";
        $vnp_OrderType = 'dat_hang';
        $vnp_Amount = $order['amount'] * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = '';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

//var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);//
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array('code' => '00'
        , 'message' => 'success'
        , 'data' => $vnp_Url);
        if (isset($_POST['redirect'])) {
            return $vnp_Url;
            die();
        } else {
            return $returnData;
        }
    }

    public function orderPayment(Request $request) {
        return $this->vnpay_payment($request->all());
    }

    public function checkPayment(Request $request)
    {
        if (
            $request->has('vnp_TxnRef') &&
            $request->has('vnp_TmnCode') &&
            $request->has('vnp_ResponseCode')) {

            try {
                if ($request->vnp_ResponseCode == "00" && $request->vnp_TmnCode == $this->vnp_TmnCode) {
                    $order = Order::find($request->vnp_TxnRef);
                    if ($order) {
                        $order->payment_id == 4 || $order->payment_id == 3 ? $order->status_id = 11 : $order->status_id = 2;
                        $order->is_payment = true;
                        $order->save();
                        return \response()->json($order, 200);
                    } else {
                        return \response()->json([], 404);
                    }
                }
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }

        return [];
    }
}
