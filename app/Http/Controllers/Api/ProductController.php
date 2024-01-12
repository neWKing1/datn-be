<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = ProductResource::collection(Product::where('status', 0)->orderBy('id', 'desc')->get());

        if ($products->count() > 0) {
            return response()->json($products, 200);
        } else {
            return response()->json([], 200);
        }
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
        try {
            $product = Product::where('name', $request->name)->count();

            if ($product >= 1) {
                return response()->json('Sản phẩm đã tồn tại', 409);
            } else {
                $name = $request->name;
                $slug = Str::slug($name);

                Product::create([
                    'name' => $name,
                    'slug' => $slug
                ]);
                return response()->json("Tạo sản phẩm thành công", 201);
            }
        } catch (\Exception $e) {
            return response()->json(["Tạo sản phẩm thất bại: " . $e->getMessage()
            ], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
