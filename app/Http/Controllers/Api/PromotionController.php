<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\PromotionVariant;
use App\Models\Variant;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        if ($request->status == "") {
            $promotions = Promotion::all();
        } else if ($request->status == 0) {
            $promotions = Promotion::where('status', 'upcoming')->get();
        } else if ($request->status == 1) {
            $promotions = Promotion::where('status', 'happenning')->get();
        } else if ($request->status == 2) {
            $promotions = Promotion::where('status', 'finished')->get();
        }
        return response()->json($promotions, 200);
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
        $promotion = new Promotion();
        $promotion->code = $request->code;
        $promotion->name = $request->name;
        $promotion->value = $request->value;
        $promotion->start_date = $request->start_date;
        $promotion->end_date = $request->end_date;

        $currentTime = now();
        $savePromotion = true; // Flag to determine whether to save the promotion

        if ($currentTime > $promotion->start_date && $currentTime < $promotion->end_date) {
            $promotion->status = 'happenning';
        } else if ($currentTime > $promotion->end_date) {
            $promotion->status = 'finished';
        } else if ($currentTime < $promotion->start_date) {
            $promotion->status = 'upcoming';
        }

        foreach ($request->productDetails as $variant) {
            $listItem = PromotionVariant::where('variant_id', $variant)->get();
            foreach ($listItem as $promotionItem) {
                $checkPromotion = Promotion::find($promotionItem->promotion_id);

                if ($checkPromotion->status == 'happenning' || $checkPromotion->status == 'upcoming') {
                    $savePromotion = false; // Set the flag to false if there's an existing promotion
                    break; // Break out of the inner loop since we've found a conflicting promotion
                }
            }

            if ($savePromotion) {
                $promotion->save();
            } else {
                return response()->json("Đang có đợt giảm giá với sản phẩm đã chọn", 409);
            }

            $promotionVariant = new PromotionVariant();
            $promotionVariant->variant_id = $variant;
            $promotionVariant->promotion_id = $promotion->id;
            $promotionVariant->save();
        }
        return response()->json("Thêm đợt giảm giá thành công", 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $promotion = Promotion::find($id);
        return response()->json($promotion, 200);
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
            'code' => 'required|unique:promotions,code,' . $id,
            'name' => 'required|unique:promotions,name,' . $id,
        ]);
        $promotion = Promotion::find($id);
        $promotion->name = $request->name;
        $promotion->code = $request->code;
        $promotion->value = $request->value;
        $promotion->start_date = $request->start_date;
        $promotion->end_date = $request->end_date;

        $currentTime = now();
        $savePromotion = true; // Flag to determine whether to save the promotion

        if ($currentTime > $promotion->start_date && $currentTime < $promotion->end_date) {
            $promotion->status = 'happenning';
        } else if ($currentTime > $promotion->end_date) {
            $promotion->status = 'finished';
        } else if ($currentTime < $promotion->start_date) {
            $promotion->status = 'upcoming';
        }

        PromotionVariant::where('promotion_id', $id)->delete();

        foreach ($request->productDetails as $variant) {
            $listItem = PromotionVariant::where('variant_id', $variant)->get();
            foreach ($listItem as $promotionItem) {
                $checkPromotion = Promotion::find($promotionItem->promotion_id);

                if ($checkPromotion->status == 'happenning' || $checkPromotion->status == 'upcoming') {
                    $savePromotion = false; // Set the flag to false if there's an existing promotion
                    break; // Break out of the inner loop since we've found a conflicting promotion
                }
            }

            if ($savePromotion) {
                $promotion->save();
            } else {
                return response()->json("Đang có đợt giảm giá với sản phẩm đã chọn", 409);
            }


            $promotionVariant = new PromotionVariant();
            $promotionVariant->variant_id = $variant;
            $promotionVariant->promotion_id = $promotion->id;
            $promotionVariant->save();
        }
        return response()->json('Cập nhật đợt giảm giá thành công', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    protected function listShoeId(string $id)
    {
        $promotion = Promotion::find($id);

        if (!$promotion) {
            return response()->json(['error' => 'Promotion not found'], 404);
        }

        $promotionVariants = PromotionVariant::where('promotion_id', $promotion->id)->get();
        $products = [];

        foreach ($promotionVariants as $item) {
            $variant = Variant::find($item->variant_id);

            if ($variant) {
                $product = Product::find($variant->product_id);

                if ($product && !in_array($product->id, $products)) {
                    $products[] = $product->id;
                }
            }
        }
        return response()->json($products, 200);
    }

    protected function listShoeDetailId(string $id)
    {
        $promotion = Promotion::find($id);

        $promotionVariants = PromotionVariant::where('promotion_id', $promotion->id)->get();
        $listVariants = [];
        foreach ($promotionVariants as $promotionVariant) {
            if (!in_array($promotionVariant->variant_id, $listVariants))
                $listVariants[] = $promotionVariant->variant_id;
        }
        return response()->json($listVariants, 200);
    }
}
