<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImageGalleryResource;
use App\Models\ImageGallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImageGalleryController extends Controller
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
        //
        foreach ($request->images as $image) {
            $imageData = uniqid() . '-' . $image->getClientOriginalName();
            ImageGallery::create([
                'url' => $imageData,
                'folder' => $request->folder
            ]);

            $image->storeAs($request->folder, $imageData, 'public');
        }

        return response()->json('Thêm ảnh vào hệ thống thành công', 200);
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

    public function getListPhotoByColor(string $colorFolder): JsonResponse
    {
        $images = ImageGalleryResource::collection(ImageGallery::where('folder', $colorFolder)->orderBy('id', 'desc')->get());
        if ($images->count() > 0) {
            return response()->json($images, 200);
        } else {
            return response()->json([], 200);
        }
    }
}
