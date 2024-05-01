<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\WishList;
use Illuminate\Http\Request;

class WishListController extends Controller
{
    public function index(Request $request) {
        $user_id = $request->user_id ?? null;

        if ($user_id) {
            return \response()->json(WishList::where('user_id', $user_id)
                ->with('product.variants.images')
                ->get(), 200);
        }

        return \response()->json([], 204);
    }

    public function store(Request $request) {
        $data = $request->only('product_id', 'user_id');

        $wishlistItem = WishList::where('product_id', $data['product_id'])
            ->where('user_id', $data['user_id'])
            ->first();
        if ($wishlistItem) {
            return response()->json(['message' => 'Sản phẩm đã có trong danh sách'], 400);
        }

        $wishlist = WishList::create($data);
        return response()->json($wishlist, 201);
    }


    public function destroy($id) {
        $wishlist = WishList::where('id', $id)->first();
        if ($wishlist) {
            $wishlist->delete();
            return \response()->json(true, 200);
        }
        return \response()->json(false, 400);
    }
}
