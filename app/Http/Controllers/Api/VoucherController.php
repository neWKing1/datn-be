<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Voucher;
use App\Models\VoucherUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        if ($request->status == "") {
            $vouchers = Voucher::all();
        } else if ($request->status == 0) {
            $vouchers = Voucher::where('status', 'upcoming')->get();
        } else if ($request->status == 1) {
            $vouchers = Voucher::where('status', 'happening')->get();
        } else if ($request->status == 2) {
            $vouchers = Voucher::where('status', 'finished')->get();
        }
        return response()->json($vouchers, 200);
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
        $voucher = new Voucher();
        $voucher->name = $request->name;
        $voucher->code = $request->code;
        $voucher->value = $request->value;
        $voucher->min_bill_value = $request->min_bill_value;
        $voucher->start_date = $request->start_date;
        $voucher->end_date = $request->end_date;
        $voucher->type = $request->type;
        $voucher->quantity = $request->quantity;

        $currentTime = now();
        $voucher->status = $this->calculateVoucherStatus($currentTime, $voucher);

        $voucher->save();

        if ($request->type == 'private') {
            foreach ($request->users as $userId) {
                $voucherUser = new VoucherUser();
                $voucherUser->user_id = $userId;
                $voucherUser->voucher_id = $voucher->id;
                $voucherUser->save();
            }
        }
        return response()->json("Tạo phiếu giảm giá thành công", 201);
    }

    private function calculateVoucherStatus($currentTime, $voucher)
    {
        if ($currentTime > $voucher->start_date && $currentTime < $voucher->end_date) {
            return 'happening';
        } else if ($currentTime > $voucher->end_date) {
            return 'finished';
        } else if ($currentTime < $voucher->start_date) {
            return 'upcoming';
        }

        return null;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $voucher = Voucher::with('users')->find($id);
        return response()->json($voucher, 200);
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
        $request->validate([
            'code' => 'required|unique:vouchers,code,' . $id,
            'name' => 'required|unique:vouchers,name,' . $id,
        ]);
        $voucher = Voucher::find($id);
        if ($voucher->type == 'private' && $request->type == 'public') {
            VoucherUser::where('voucher_id', $id)->delete();

            $voucher->name = $request->name;
            $voucher->code = $request->code;
            $voucher->value = $request->value;
            $voucher->min_bill_value = $request->min_bill_value;
            $voucher->start_date = $request->start_date;
            $voucher->end_date = $request->end_date;
            $voucher->type = $request->type;
            $voucher->quantity = $request->quantity;

            $currentTime = now();
            $voucher->status = $this->calculateVoucherStatus($currentTime, $voucher);

            $voucher->save();
        } else if ($voucher->type == 'public' && $request->type == 'public') {

            $voucher->name = $request->name;
            $voucher->code = $request->code;
            $voucher->value = $request->value;
            $voucher->min_bill_value = $request->min_bill_value;
            $voucher->start_date = $request->start_date;
            $voucher->end_date = $request->end_date;
            $voucher->type = $request->type;
            $voucher->quantity = $request->quantity;

            $currentTime = now();
            $voucher->status = $this->calculateVoucherStatus($currentTime, $voucher);

            $voucher->save();
        } else if ($voucher->type == 'private' && $request->type == 'private') {
            VoucherUser::where('voucher_id', $id)->delete();

            $voucher->name = $request->name;
            $voucher->code = $request->code;
            $voucher->value = $request->value;
            $voucher->min_bill_value = $request->min_bill_value;
            $voucher->start_date = $request->start_date;
            $voucher->end_date = $request->end_date;
            $voucher->type = $request->type;
            $voucher->quantity = $request->quantity;

            $currentTime = now();
            $voucher->status = $this->calculateVoucherStatus($currentTime, $voucher);

            $voucher->save();

            foreach ($request->users as $userId) {
                $voucherUser = new VoucherUser();
                $voucherUser->user_id = $userId;
                $voucherUser->voucher_id = $voucher->id;
                $voucherUser->save();
            }

        } else if ($voucher->type == 'public' && $request->type == 'private') {
            $voucher->name = $request->name;
            $voucher->code = $request->code;
            $voucher->value = $request->value;
            $voucher->min_bill_value = $request->min_bill_value;
            $voucher->start_date = $request->start_date;
            $voucher->end_date = $request->end_date;
            $voucher->type = $request->type;
            $voucher->quantity = $request->quantity;

            $currentTime = now();
            $voucher->status = $this->calculateVoucherStatus($currentTime, $voucher);

            $voucher->save();

            foreach ($request->users as $userId) {
                $voucherUser = new VoucherUser();
                $voucherUser->user_id = $userId;
                $voucherUser->voucher_id = $voucher->id;
                $voucherUser->save();
            }
        }

        return response()->json("Cập nhật phiếu giảm giá thành công", 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getVoucher(Request $request)
    {
        $voucherPublic = Voucher::where('status', 'happening')
            ->where('type', 'public')
            ->get();

        $vouchers = $voucherPublic;

        if ($request->userId != '0') {
            $voucherPrivate = Voucher::where('status', 'happening')
                ->where('type', 'private')
                ->with('voucherUsers', function ($query) use ($request) {
                    $query->where('user_id', $request->userId);
                })
                ->get();

            $voucherPrivate = $voucherPrivate->filter(function ($voucher) {
                return $voucher->voucherUsers->isNotEmpty();
            });

            $vouchers = $voucherPublic->merge($voucherPrivate);
        }
        return response()->json($vouchers, 200);
    }
    public function findVoucher(Request $request)
    {
        $voucher = Voucher::where('name', $request->name)
            ->first();

        return response()->json($voucher, 200);
    }
}
