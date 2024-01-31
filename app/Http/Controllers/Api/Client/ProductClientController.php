<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductClientController extends Controller
{
    public function index(Request $request){
        $products = $this->productFilter($request);
        // lấy biến thể
        $products->map(function ($product) {
            $product->variants = $product->variants ?? [];
            // Lấy ảnh của biến thể
            $product->variants->map(function ($variant) {
                return $variant->images ?? [];
            });
            return $product;
        });
        return \response()->json($products);
    }

    public function detail($slug){
        $product = Product::where('slug', $slug)
            ->with('variants')
            ->first();

        $product->variants->map(function ($variant) {
            return $variant->images ?? [];
        });

        return \response()->json($product);
    }

    public function attributes($slug){
        $product = Product::query()->where('slug', $slug)->first();
        if ($product) {

            return \response()->json([
                "colors" => $product->colors,
                "sizes" => $product->sizes,
            ], 200);
        }

        return \response()->json([]);
    }

    public function productFilter($options){
        $products = Product::query();

        if ($options->has('page') && $options->page >= 1) {
            $offset = ($options->page - 1) * $options->pageSize;
            $products->limit($options->pageSize)->offset($offset);
        } else {
            $products->limit(8);
        }

        return $products->get();
    }
}
