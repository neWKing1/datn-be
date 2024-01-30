<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ImageGallery;
use App\Models\ImageProduct;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            $product = Product::findOrFail($request[0]['product_id']);

            if ($product) {
                foreach ($request->all() as $item) {
                    $variant = Variant::create([
                        'size_id' => $item['size_id'],
                        'product_id' => $item['product_id'],
                        'color_id' => $item['color_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'weight' => $item['weight']
                    ]);

                    if (!empty($item['listImages']) && count($item['listImages']) > 0) {
                        foreach ($item['listImages'] as $image) {

                            //Do FE bắn cả domain và folder sang lên là sẽ phải xử nó để có thể tìm được id ảnh
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
                }

                //set cho product hoạt động status là cho thêm nhanh, is active là cho hoạt động ngoài web tránh nhầm lẫn
                $product->status = true;
                $product->is_active = true;
                $product->save();
                return response()->json(["Tạo sản phẩm thành công"], 201);
            } else {
                return response()->json(["Tạo sản phẩm thất bại"], 500);
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

    public function addVariants(Request $request)
    {
        try {
            $product = Product::findOrFail($request[0]['product_id']);

            if ($product) {
                foreach ($request->all() as $item) {
                    $variant = Variant::create([
                        'size_id' => $item['size_id'],
                        'product_id' => $item['product_id'],
                        'color_id' => $item['color_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'weight' => $item['weight']
                    ]);

                    if (!empty($item['listImages']) && count($item['listImages']) > 0) {

                        $listGroupVariant = Variant::where('product_id', $variant->product_id)
                            ->where('color_id', $variant->color_id)->get();

                        foreach ($listGroupVariant as $groupVariant) {
                            ImageProduct::where('variant_id', $groupVariant->id)->delete();

                            foreach ($item['listImages'] as $image) {
                                $separateDomain = explode('storage/', $image);
                                $separateFolder = explode('/', $separateDomain[1]);
                                $urlImage = $separateFolder[1];

                                $imageGallery = ImageGallery::where('url', $urlImage)->first();

                                if ($imageGallery) {
                                    ImageProduct::create([
                                        'image_gallery_id' => $imageGallery->id,
                                        'variant_id' => $groupVariant->id
                                    ]);
                                }
                            }
                        }

                    } else {
                        $groupVariant = Variant::where('product_id', $variant->product_id)
                            ->where('color_id', $variant->color_id)->first();

                        $oldImages = ImageProduct::where('variant_id', $groupVariant->id)->get();

                        foreach ($oldImages as $image) {
                            ImageProduct::create([
                                'image_gallery_id' => $image->image_gallery_id,
                                'variant_id' => $variant->id
                            ]);
                        }
                    }
                }
                $product->update([
                    'updated_by' => Auth::user()->name,
                    'updated_at' => now(),
                ]);
                return response()->json(["Tạo sản phẩm thành công"], 201);
            } else {
                return response()->json(["Tạo sản phẩm thất bại"], 500);
            }
        } catch (\Exception $e) {
            return response()->json(["Tạo sản phẩm thất bại: " . $e->getMessage()
            ], 500);
        }
    }
}
