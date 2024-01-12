<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SizeResource;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sizes = SizeResource::collection(Size::orderBy('id', 'desc')->get());

        if ($sizes->count() > 0) {
            return response()->json($sizes, 200);
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
            $size = Size::where('name', $request->name)->count();

            if ($size >= 1) {
                return response()->json('Size đã tồn tại', 409);
            } else {
                Size::create([
                    'name' => $request->name
                ]);
                return response()->json("Tạo size thành công", 201);
            }
        } catch (\Exception $e) {
            return response()->json(["Tạo size thất bại: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $size = Size::find($id);
        if ($size) {
            return response()->json($size, 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "Không tìm thấy kết quả!"
            ], 404);
        }
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
