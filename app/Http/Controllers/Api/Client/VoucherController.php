<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherUser;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index(Request $request) {
        $user_id = $request->user['id'] ?? null;

        if ($user_id) {
            $user_vouchers = VoucherUser::where('user_id', $user_id)->pluck('voucher_id')->toArray();

            $public_vouchers = Voucher::query()
                ->where('status', '=', 'happening')
                ->where('type', '=', 'public')
                ->get();

            $vouchers = $public_vouchers->merge(Voucher::whereIn('id', $user_vouchers)->get());
            return response()->json($vouchers, 200);
        }

        $vouchers = Voucher::query()
            ->where('status', '=', 'happening')
            ->where('type', '=', 'public')
            ->get();
        return response()->json($vouchers, 200);
    }

}
