<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillResource;
use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\Variant;
use Illuminate\Http\Request;

class BillDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bill = Bill::with('billDetails')
            ->with('returnProducts')
            ->with('variants')
            ->with('variants.size')
            ->with('variants.color')
            ->with('variants.product')
            ->with('variants.imageProducts.imageGallery')
            ->with('variants.promotions')
            ->find($request->id);

        if (!$bill) {
            return response()->json('Bill not found', 404);
        }

        $billDetail = new BillResource($bill);

        return response()->json($billDetail, 200);
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
        $variant = BillDetail::where('bill_id', $request->bill_id)
            ->where('variant_id', $request->variant_id)->count();
        if ($variant > 0) {
            return response()->json("Sản phẩm đã được thêm vào giỏ hàng", 409);
        }
        BillDetail::create($request->all());

        $billDetails = BillDetail::where('bill_id', $request->bill_id)->get();
        $sum = 0;

        foreach ($billDetails as $item) {
            $sum += $item->quantity * $item->price;
        }

        $bill = Bill::find($request->bill_id);
        $bill->update([
            'total_money' => $sum,
        ]);
        return response()->json("Thêm sản phẩm thành công", 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $billDetails = new BillResource(
            Bill::with('billDetails')
                ->with('variants')
                ->with('variants.product')
                ->with('variants.size')
                ->with('variants.color')
                ->with('variants.imageProducts.imageGallery')
                ->find($id)
        );
        return response()->json($billDetails, 200);
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
        $billDetail = BillDetail::find($id);

        if (!$billDetail) {
            return response()->json("BillDetail not found", 404);
        }

        $variantPromotion = Variant::with('promotions')->find($billDetail->variant_id);

        if (!$variantPromotion) {
            return response()->json("Variant not found", 404);
        }

        $discountedPrice = $variantPromotion->price;

        foreach ($variantPromotion->promotions as $promotion) {
            if ($promotion && $promotion->status === 'happenning') {
                $discountedPrice -= ($variantPromotion->price * $promotion->value / 100);
            }
        }


        $billDetail->update([
            'quantity' => $request->quantity,
            'price' => $discountedPrice
        ]);

        return response()->json("Cập nhật thành công", 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        BillDetail::destroy($id);
        return response()->json("Xóa sản phẩm thành công", 200);
    }

    public function updateQtyQuickly(Request $request, string $id)
    {
        try {
            \DB::beginTransaction();

            $billDetail = BillDetail::find($id);
            $billDetail->update([
                'quantity' => $request->quantity,
            ]);

            $billDetails = BillDetail::where('bill_id', $billDetail->bill_id)->get();
            $sum = 0;

            foreach ($billDetails as $item) {
                $sum += $item->quantity * $item->price;
            }

            $bill = Bill::find($billDetail->bill_id);
            $bill->update([
                'total_money' => $sum,
            ]);

            \DB::commit();

            return response()->json("Cập nhật thành công", 200);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json("Có lỗi xảy ra: " . $e->getMessage(), 500);
        }
    }
}
