<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\ImageGallery;
use App\Models\ImageProduct;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                $authName = Auth::user()->name;

                Product::create([
                    'name' => $name,
                    'slug' => $slug,
                    'created_by' => $authName,
                    'updated_by' => $authName
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
        $product = Product::find($id);
        return response()->json($product, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $product = Product::find($id);
        return response()->json($product, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);
        $checkProduct = Product::where('name', $request->name)->count();
        if ($checkProduct >= 1) {
            return response()->json('Tên sản phẩm đã tồn tại', 409);
        }
        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);
        return response()->json('Cập nhật thành công', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function listProductManage(Request $request)
    {
        $status = $request->status;

        $products = ProductResource::collection(
            Product::where('status', 1)
                ->when($status === 'true', function ($query) {
                    return $query->where('is_active', 1);
                })
                ->when($status === 'false', function ($query) {
                    return $query->where('is_active', 0);
                })
                ->with('variants')
                ->get()
        );

        return response()->json($products->isEmpty() ? [] : $products, 200);
    }

    public function changeStatus(Request $request)
    {
        $product = Product::find($request->id);
        $product->is_active = $request->status;
        $product->updated_by = Auth::user()->name;
        $product->updated_at = now();
        $product->save();

        return response()->json("Thay đổi trạng thái thành công", 200);
    }

    public function productDetailEdit(string $id)
    {
        //
        $product = new ProductResource(Product::with('variants.size')
            ->with('variants.color')
            ->with('variants.imageProducts.imageGallery')
            ->find($id));

        return response()->json($product, 200);
    }

    public function updateFast(Request $request)
    {
        $productId = null;

        try {
            foreach ($request->all() as $item) {
                $variant = Variant::find($item['id']);

                if ($variant) {
                    $variant->update($item);
                    if ($productId === null) {
                        $productId = $variant->product_id;
                    }
                }
            }

            if ($productId !== null) {
                $product = Product::find($productId);

                if ($product) {
                    $product->update([
                        'updated_by' => Auth::user()->name,
                        'updated_at' => now(),
                    ]);
                }

                return response()->json("Cập nhật thành công", 200);
            } else {
                return response()->json("Không tìm thấy sản phẩm hoặc biến thể", 404);
            }
        } catch (\Exception $e) {
            return response()->json("Cập nhật thất bại: " . $e->getMessage(), 500);
        }
    }

    public function updateProductDetail(Request $request, string $id)
    {
        $variant = Variant::find($id);

        $productId = $variant->product_id;

        $checkVariant = Variant::where('product_id', $variant->product_id)
            ->where('size_id', $request['data']['size'])
            ->where('color_id', $request['data']['color'])->first();

        $listGroupVariant = Variant::where('product_id', $variant->product_id)
            ->where('color_id', $request['data']['color'])->get();

        if ($checkVariant->id != $id) {
            return response()->json("Biến thể đã tồn tại", 409);
        } else {
            $variant->update($request['data']);
        }

        if (!empty($request->images) && count($request->images) > 0) {
            foreach ($listGroupVariant as $variant) {
                ImageProduct::where('variant_id', $variant->id)->delete();

                foreach ($request->images as $image) {
                    $separateDomain = explode('storage/', $image);
                    $separateFolder = explode('/', $separateDomain[1]);
                    $urlImage = $separateFolder[1];

                    $imageGallery = ImageGallery::where('url', $urlImage)->first();

                    if ($imageGallery) {
                        ImageProduct::create([
                            'image_gallery_id' => $imageGallery->id,
                            'variant_id' => $variant->id
                        ]);
                    }
                }
            }
        } else {
            foreach ($listGroupVariant as $variant) {
                ImageProduct::where('variant_id', $variant->id)->delete();
            }
        }

        $product = Product::find($productId);
        if ($product) {
            $product->update([
                'updated_by' => Auth::user()->name,
                'updated_at' => now(),
            ]);
        }

        return response()->json('Cập nhật thành công', 200);
    }
    public function productDetail(Request $request)
    {
        $products = Product::with('variants.size')
            ->with('variants.color')
            ->with('variants.imageProducts.imageGallery')
            ->whereIn('id', $request->shoes)
            ->get();

        $productResource = ProductResource::collection($products);

        return response()->json($productResource, 200);
    }
}
