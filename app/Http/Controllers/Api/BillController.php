<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillDetailResource;
use App\Http\Resources\BillResource;
use App\Http\Resources\ProductResource;
use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\BillHistory;
use App\Models\PaymentHistory;
use App\Models\Product;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->status;

        $query = Bill::where('status', 'active')
            ->orderBy('created_at', 'desc');

        if ($status !== null && $status != 1) {
            $query->where('timeline', $status);
        }

        $bills = $query->get();

        return response()->json($bills, 200);
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
        //
        $bills = Bill::where('status', 'no-active')->count();
        if ($bills >= 5) {
            return response()->json("Chỉ được tạo tối đa 5 đơn hàng ", 409);
        }
        $bill = Bill::create([
            'code' => 'HD' . uniqid()
        ]);
        BillHistory::create([
            'note' => 'Tạo đơn hàng',
            'status' => 1,
            'bill_id' => $bill->id,
            'created_by' => Auth::user()->name
        ]);
        return response()->json("Tạo đơn hàng thành công", 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $bill = Bill::find($id);
        return response()->json($bill, 200);
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
        $bill = Bill::find($id);

        if (!$bill) {
            return response()->json('Bill not found', 404);
        }
        if (!empty($request->customer)) {
            $user = User::find($request->customer);
            //có người dùng
            if ($request->type == 1) {
                //thanh toán online (trường hợp giao hàng)
                if ($request->paymentMethod == '1') {
                    $bill->update([
                        'customer_id' => empty($request->customer) ? null : $request->customer,
                        'voucher_id' => empty($request->voucher) ? null : $request->voucher,
                        'customer_name' => $request->customerName,
                        'note' => empty($request->note) ? null : $request->note,
                        'payment_method' => 'card',
                        'status' => 'no-active',
                        'total_money' => $request->totalMoney,
                        'money_reduce' => $request->moneyReduce,
                        'money_ship' => $request->moneyShip,
                        'address' => $request->address,
                        'timeline' => (string)$request->timeline,
                        'type' => 'delivery',
                        'phone_number' => $user->phone_number,
                        'email' => $request->email
                    ]);

                    return $this->vnPay($request, $bill);
                }
                //thanh toán khi nhận hàng (trường hợp giao hàng)
                $bill->update([
                    'customer_id' => empty($request->customer) ? null : $request->customer,
                    'voucher_id' => empty($request->voucher) ? null : $request->voucher,
                    'customer_name' => $request->customerName,
                    'note' => empty($request->note) ? null : $request->note,
                    'payment_method' => 'cash',
                    'status' => 'active',
                    'total_money' => $request->totalMoney,
                    'money_reduce' => $request->moneyReduce,
                    'timeline' => (string)$request->timeline,
                    'type' => 'delivery',
                    'phone_number' => $request->phoneNumber,
                    'address' => $request->address,
                    'money_ship' => $request->moneyShip,
                    'email' => $request->email
                ]);
                BillHistory::create([
                    'note' => 'Chờ giao',
                    'status' => 4,
                    'bill_id' => $bill->id,
                    'created_by' => Auth::user()->name
                ]);

            } else {
                //thanh toán online (trường hợp không giao hàng)
                if ($request->paymentMethod == '1') {
                    $bill->update([
                        'customer_id' => empty($request->customer) ? null : $request->customer,
                        'voucher_id' => empty($request->voucher) ? null : $request->voucher,
                        'customer_name' => $request->customerName,
                        'note' => empty($request->note) ? null : $request->note,
                        'payment_method' => 'card',
                        'status' => 'no-active',
                        'total_money' => $request->totalMoney,
                        'money_reduce' => $request->moneyReduce,
                        'timeline' => (string)$request->timeline,
                        'type' => 'at the counter',
                        'phone_number' => $user->phone_number,
                        'email' => $request->email
                    ]);

                    return $this->vnPay($request, $bill);
                }

                //thánh toán khi nhận hàng (trường hợp không giao hàng)
                $bill->update([
                    'customer_id' => empty($request->customer) ? null : $request->customer,
                    'voucher_id' => empty($request->voucher) ? null : $request->voucher,
                    'customer_name' => $request->customerName,
                    'note' => empty($request->note) ? null : $request->note,
                    'payment_method' => 'cash',
                    'status' => 'active',
                    'total_money' => $request->totalMoney,
                    'money_reduce' => $request->moneyReduce,
                    'timeline' => (string)$request->timeline,
                    'type' => 'at the counter',
                    'phone_number' => $user->phone_number,
                    'email' => $request->email
                ]);
                BillHistory::create([
                    'note' => 'Đã thanh toán đủ tiền',
                    'status' => '3',
                    'bill_id' => $bill->id,
                    'created_by' => Auth::user()->name
                ]);
                BillHistory::create([
                    'note' => 'Hoàn thành',
                    'status' => 6,
                    'bill_id' => $bill->id,
                    'created_by' => Auth::user()->name
                ]);
                PaymentHistory::create([
                    'bill_id' => $bill->id,
                    'note' => 'Đã thanh toán đủ tiền',
                    'created_by' => Auth::user()->name,
                    'total_money' => $request->totalMoney,
                    'trading_code' => $request->trading_code
                ]);

            }

            //không có người dùng - trường hợp này chỉ mua được hàng tại quầy
        } else {
            //thanh toán online
            if ($request->paymentMethod == '1') {
                $bill->update([
                    'customer_id' => empty($request->customer) ? null : $request->customer,
                    'voucher_id' => empty($request->voucher) ? null : $request->voucher,
                    'customer_name' => $request->customerName,
                    'note' => empty($request->note) ? null : $request->note,
                    'payment_method' => 'card',
                    'status' => 'no-active',
                    'total_money' => $request->totalMoney,
                    'money_reduce' => $request->moneyReduce,
                    'timeline' => (string)$request->timeline,
                    'type' => 'at the counter',
                    'email' => $request->email
                ]);

                return $this->vnPay($request, $bill);
            }

            //thanh toán tiền mặt
            $bill->update([
                'customer_id' => empty($request->customer) ? null : $request->customer,
                'voucher_id' => empty($request->voucher) ? null : $request->voucher,
                'customer_name' => $request->customerName,
                'note' => empty($request->note) ? null : $request->note,
                'payment_method' => 'cash',
                'status' => 'active',
                'total_money' => $request->totalMoney,
                'money_reduce' => $request->moneyReduce,
                'timeline' => (string)$request->timeline,
                'type' => 'at the counter',
                'email' => $request->email
            ]);
            BillHistory::create([
                'note' => 'Đã thanh toán đủ tiền',
                'status' => '3',
                'bill_id' => $bill->id,
                'created_by' => Auth::user()->name
            ]);
            BillHistory::create([
                'note' => 'Hoàn thành',
                'status' => 6,
                'bill_id' => $bill->id,
                'created_by' => Auth::user()->name
            ]);
            PaymentHistory::create([
                'bill_id' => $bill->id,
                'note' => 'Đã thanh toán đủ tiền',
                'created_by' => Auth::user()->name,
                'total_money' => $request->totalMoney,
                'trading_code' => $request->trading_code
            ]);
        }

        return response()->json('Lưu đơn hàng thành công', 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        Bill::destroy($id);
        return response()->json("Xóa đơn hàng thành công", 200);
    }

    public function getBillNotActive()
    {
        $bills = Bill::where('status', 'no-active')->get();
        return response()->json($bills, 200);
    }

    public function changeStatus(Request $request, string $id)
    {
        $bill = Bill::find($id);

        switch ($request->note) {
            case 'Đã bàn giao cho đơn vị vận chuyển':
                $billHistories = BillHistory::where('bill_id', $id)->get();
                foreach ($billHistories as $billHistory) {
                    if ($billHistory->status == '5') {
                        return response()->json('Trạng thái đã tồn tại', 409);
                    }
                }
                $bill->update([
                    'timeline' => '5',
                    'status_id' => '104'
                ]);
                BillHistory::create([
                    'note' => $request->note,
                    'status' => '5',
                    'status_id' => '104',
                    'bill_id' => $bill->id,
                    'created_by' => Auth::user()->name
                ]);
                return response()->json('Cập nhật trạng thái thành công', 200);
                break;
            case 'Đơn hàng đã được giao thành công':
                $billHistories = BillHistory::where('bill_id', $id)->get();
                $flag = false;
                foreach ($billHistories as $billHistory) {
                    if ($billHistory->status == '3') {
                        $bill->update([
                            'timeline' => '6',
                            'status_id' => '105'
                        ]);
                        BillHistory::create([
                            'note' => $request->note,
                            'status' => '6',
                            'status_id' => '105',
                            'bill_id' => $bill->id,
                            'created_by' => Auth::user()->name
                        ]);
                        $flag = true;
                    }
                }
                if ($flag) {
                    return response()->json('Cập nhật trạng thái thành công', 200);
                } else {
                    return response()->json('Vui lòng xác nhận thanh toán đơn hàng', 409);
                }
                break;
            case 'Đã xác nhận đơn hàng':
                $billHistories = BillHistory::where('bill_id', $id)->get();
                foreach ($billHistories as $billHistory) {
                    if ($billHistory->status == '4') {
                        return response()->json('Trạng thái đã tồn tại', 409);
                    }
                }
                $bill->update([
                    'timeline' => '4',
                    'status_id' => '103'
                ]);
                BillHistory::create([
                    'note' => 'Chờ giao',
                    'status' => '4',
                    'status_id' => '103',
                    'bill_id' => $bill->id,
                    'created_by' => Auth::user()->name
                ]);
                return response()->json('Cập nhật trạng thái thành công', 200);
                break;
            case 'Đã hủy đơn hàng':
                $billHistories = BillHistory::where('bill_id', $id)->get();
                foreach ($billHistories as $billHistory) {
                    if ($billHistory->status == '7') {
                        return response()->json('Trạng thái đã tồn tại', 409);
                    }
                }
                $bill->update([
                    'timeline' => '7',
                    'status_id' => '108'
                ]);
                BillHistory::create([
                    'note' => $request->note,
                    'status' => '7',
                    'status_id' => '108',
                    'bill_id' => $bill->id,
                    'created_by' => Auth::user()->name
                ]);
                return response()->json('Cập nhật trạng thái thành công', 200);
                break;
            default:
                return "";
        }
    }

    public function changeInfo(Request $request, string $id)
    {
        $bill = Bill::find($id);
        $bill->update($request->all());

        return response()->json("Cập nhật thông tin thành công", 200);
    }

    public function vnPay(Request $request, Bill $bill)
    {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = 'http://localhost:5173/admin/order';
        $vnp_TmnCode = "CAL4BV81"; //Mã website tại VNPAY
        $vnp_HashSecret = "VJMBKWWRRCWMVNQHRLSLPDSRQSSNTOAV"; //Chuỗi bí mật

        $vnp_TxnRef = uniqid(); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = $bill->id;
        $vnp_OrderType = '123';
        $vnp_Amount = 100 * ($request->totalMoney + $request->moneyShip + -$request->moneyReduce);
        $vnp_Locale = "VN";
        $vnp_BankCode = $request['bank_code'];
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
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret); //
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array(
            'code' => '00', 'message' => 'success', 'data' => $vnp_Url
        );
        return response()->json(['url' => $vnp_Url], 200);
    }

    public function updateStatusBillSuccessVnPay(string $id)
    {
        $bill = Bill::find($id);

        $bill->update([
            'status' => 'active',
        ]);

        if (!BillHistory::where('bill_id', $bill->id)->where('status', '3')->first()) {
            BillHistory::create([
                'note' => 'Đã thanh toán đủ tiền',
                'status' => '3',
                'bill_id' => $bill->id,
                'created_by' => Auth::user()->name
            ]);
        }
        if ($bill->type != 'delivery') {
            if (!BillHistory::where('bill_id', $bill->id)->where('status', '6')->first()) {
                BillHistory::create([
                    'note' => 'Hoàn thành',
                    'status' => 6,
                    'bill_id' => $bill->id,
                    'created_by' => Auth::user()->name
                ]);
            }
        } else {
            if (!BillHistory::where('bill_id', $bill->id)->where('status', '4')->first()) {
                BillHistory::create([
                    'note' => 'Chờ giao',
                    'status' => 4,
                    'bill_id' => $bill->id,
                    'created_by' => Auth::user()->name
                ]);
            }
        }

        if (!PaymentHistory::where('bill_id', $bill->id)->where('note', 'Đã thanh toán đủ tiền')->first()) {
            PaymentHistory::create([
                'bill_id' => $bill->id,
                'note' => 'Đã thanh toán đủ tiền',
                'created_by' => Auth::user()->name,
                'total_money' => $bill->total_money,
                'trading_code' => 'VNPay'
            ]);
        }

        return response()->json('Lưu đơn hàng thành công', 201);
    }

    public function getTopProductInOrder()
    {
        // Group the BillDetail records by variant_id and then calculate the sum of quantity for each group
        $top3Sums = BillDetail::groupBy('variant_id')
            ->selectRaw('variant_id, sum(quantity) as total_quantity')
            ->orderByDesc('total_quantity') // Order by total_quantity in descending order
            ->limit(3) // Limit the results to top 3
            ->get();

        // Get the variant IDs of the top 3 sums
        $top3VariantIds = $top3Sums->pluck('variant_id')->toArray();

        $variants = [];
        foreach ($top3VariantIds as $id) {
            // Find the variant by variant_id
            $variant = Variant::with('size', 'product', 'color')->find($id);

            // Add the total_quantity to the variant object
            $variant->total_quantity = $top3Sums->where('variant_id', $id)->first()->total_quantity;

            // Add the variant to the products array
            $variants[] = $variant;
        }

        // Return the top 3 sums and the corresponding products
        return response()->json($variants, 200);
    }

    public function getStatusBillToday()
    {
        $bills = Bill::whereDate('updated_at', today()) // Filter bills created today
        ->where('timeline', '!=', '0')
            ->selectRaw('timeline, COUNT(*) as total_quantity') // Count the number of bills for each timeline status
            ->groupBy('timeline')
            ->get();

        return response()->json($bills, 200);
    }
}
