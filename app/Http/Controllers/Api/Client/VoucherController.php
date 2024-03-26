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
        $vouchers = Voucher::query()->where('status', '=', 'happening');

        if ($user_id) {
            $user_vouchers = VoucherUser::where('user_id', $user_id)->pluck('voucher_id');
            $vouchers->where(function ($query) use ($user_vouchers) {
                $query->whereIn('id', $user_vouchers)
                    ->orWhere('type', '=', 'public');
            });
        }

        return response()->json($vouchers->get(), 200);
    }

}
