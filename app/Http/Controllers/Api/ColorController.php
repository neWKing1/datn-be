<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ColorResource;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $colors = ColorResource::collection(Color::orderBy('id', 'desc')->get());

        if ($colors->count() > 0) {
            return response()->json($colors, 200);
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
            $color = Color::where('name', $request->name)->count();

            if ($color >= 1) {
                return response()->json('Color đã tồn tại', 409);
            } else {
                Color::create([
                    'name' => $request->name
                ]);
                return response()->json("Tạo color thành công", 201);
            }
        } catch (\Exception $e) {
            return response()->json(["Tạo color thất bại: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $color = Color::find($id);
        if ($color) {
            return response()->json($color, 200);
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
