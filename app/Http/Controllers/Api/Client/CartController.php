<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request){
        $cartItems = $request->all() ?? [];
        $data = [];

        try {
            foreach ($cartItems as $cartItem) {
                $data[] = Variant::where('id', $cartItem['variant_id'])
                    ->where('product_id', $cartItem['product_id'])
                    ->with('images')
                    ->with('color')
                    ->with('size')
                    ->with('product.colors')
                    ->with('product.sizes')
                    ->with('product.variants')
                    ->with('promotions')
                    ->first();
            }
            return $data;
        } catch (\Exception $exception){
            return $exception->getMessage();
        }

    }

    public function coupon(Request $request) {
        try {
            $variant = Variant::query()->where('id', $request->variant)->first();
            return $variant->promotions;
        } catch (\Exception $e){
            return $e->getMessage();
        }
    }

}
