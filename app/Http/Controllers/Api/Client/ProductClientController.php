<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductClientController extends Controller
{
    protected $paginate = 1;
    public function index(Request $request){
        $products = $this->productFilter($request);
        // lấy biến thể
        $products->map(function ($product) {
            $product->variants = $product->variants ?? [];
            // Lấy ảnh của biến thể
            $product->variants->map(function ($variant) {
                return $variant->images ?? [];
            });
            $product->variants->map(function ($variant) {
                return $variant->promotions ?? [];
            });
            return $product;
        });
        return \response()->json([
            'products' => $products,
            'total' => $this->paginate
        ], 200);
    }

    public function detail($slug){
        $product = Product::where('slug', $slug)
            ->with('variants')
            ->with('variants.promotions')
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
        $products = Product::query()
            ->where('status', '!=', '0')
            ->where('is_active', '!=', '0');
        /*Lọc theo khoảng giá*/
        if ($options->has('min') && $options->has('max') && is_numeric($options->max) && $options->max > 0){
            $products->whereHas('variants', function ($query) use ($options) {
                $query->whereBetween('price', [$options->min, $options->max]);
            });
        }

        /*phân trang*/
        $this->paginate = $products->count();
        if ($options->has('page') && $options->page >= 1) {
            $offset = ($options->page - 1) * $options->pageSize;
            $products->limit($options->pageSize)->offset($offset);
        } else {
            $products->limit(8);
        }

        return $products->get();
    }

    public function rangePrice(){
        return \response()->json([
            "min" => Variant::min('price'),
            "max" => Variant::max('price')
        ]);
    }
}
