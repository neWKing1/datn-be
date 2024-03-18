<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users = User::where('status', 'active')->get();
        return response()->json($users, 200);
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
    }

    public function createCustomer(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required',
            'specific_address' => 'required',
            'province' => 'required',
            'district' => 'required',
            'ward' => 'required',
        ]);

        $validatedData['password'] = bcrypt('password');

        User::create($validatedData);

        return response()->json('Thêm thành công', 201);
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

    public function getCustomer(Request $request)
    {
        $name = $request->name;
        $customers = UserResource::collection(User::where('name', 'like', "%$name%")
            ->where('role', 'customer')
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->get());
        return response()->json($customers, 200);
    }

    public function showCustomer(string $id)
    {
        $customer = new UserResource(User::find($id));
        return response()->json($customer, 200);
    }
}
