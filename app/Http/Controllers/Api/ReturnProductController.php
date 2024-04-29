<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillHistory;
use App\Models\ReturnProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ReturnProductController extends Controller
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
        foreach ($request->variants as $variant) {
            ReturnProduct::create([
                'bill_id' => $request->id,
                'variant_id' => $variant['variant_id'], // Access variant_id as an array element
                'price' => $variant['price'], // Access price as an array element
                'quantity' => $variant['quantity'] // Access quantity as an array element
            ]);
        }

        // Create a new BillHistory record
        if(!BillHistory::where('bill_id', $request->id)->where('status' ,8)->first()){
            BillHistory::create([
                'note' => $request->note,
                'status' => 8,
                'status_id' => '107',
                'bill_id' => $request->id,
                'created_by' => $request->user()->name
            ]);

            Bill::find($request->id)->update([
                'timeline' => '8',
                'status_id' => '107'
            ]);
        }

        return response()->json("Gửi yêu cầu hoàn hàng thành công", 200);
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
