<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillHistory;
use App\Models\PaymentHistory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        DB::beginTransaction();

        if ($request->return_money) {
            PaymentHistory::create([
                'bill_id' => $request->bill_id,
                'note' => $request->note,
                'created_by' => Auth::user()->name,
                'total_money' => $request->return_money,
                'trading_code' => $request->trading_code,
                'method' => 'refund'
            ]);

            $bill = Bill::find($request->bill_id);
            $bill->update([
                'timeline' => '6'
            ]);

            DB::commit();

            return response()->json('Cập nhật trạng thái thành công', 200);
        } else {
            try {
                // Here, we assume $request->total_money is sent in the request
                if(!PaymentHistory::where('bill_id', $request->bill_id)->where('method', 'pay')->first()){
                    PaymentHistory::create([
                        'bill_id' => $request->bill_id,
                        'note' => $request->note,
                        'created_by' => Auth::user()->name,
                        'total_money' => $request->total_money, // Corrected line
                        'trading_code' => $request->trading_code
                    ]);
                }


                $bill = Bill::find($request->bill_id);
                $bill->update([
                    'timeline' => '3'
                ]);

                if(!BillHistory::where('bill_id', $bill->id)->where('note', 'Đã thanh toán đủ tiền')->first()){
                    BillHistory::create([
                        'note' => 'Đã thanh toán đủ tiền',
                        'status' => '3',
                        'bill_id' => $bill->id,
                        'created_by' => Auth::user()->name
                    ]);
                }

                DB::commit();

                return response()->json('Cập nhật trạng thái thành công', 200);
            } catch (Exception $e) {
                DB::rollback();
                return response()->json('Có lỗi xảy ra trong quá trình xử lý', 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $paymentHistories = PaymentHistory::where('bill_id', $id)->get();
        return response()->json($paymentHistories, 200);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
