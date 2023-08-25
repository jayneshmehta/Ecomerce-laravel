<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
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
            $data = ["userId" => $request->userId, "productId" => "0," . $request->productId];
            $productsId = ",$request->productId";
            $Wishlist = Wishlist::where("userId", "=", $request->userId)->update(['productId' => DB::raw("CONCAT(productId,'$productsId')")]);
            if (!$Wishlist) {
                $Wishlist =  Wishlist::updateOrCreate($data);
            }
            return response()->json([
                'status' => true,
                'message' => 'Added to wishlist..',
                'wishlist' => $Wishlist
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Not able to add to wishlist..',
                'errors' => $e
            ], 401);
        }
    }
    /**
     * get data by storage.
     */
    public function getWishlistByUserId($id)
    {
        $wishlist = Wishlist::where('userId', '=', $id)->get();

        return  $wishlist;
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wishlist $wishlist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Wishlist $wishlist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $rule = Validator::make(
            $request->all(),
            [
                "userId" => "required",
                "productId" => "required",
            ]
        );

        if ($rule->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $rule->errors()
            ], 401);
        }

        try {
            $userId = $request->userId;
            $productId = $request->productId;
            $wishlist = $this->getWishlistByUserId($userId)->first()->ProductId;
            $wishlist = explode(",", $wishlist);
            if (($key = array_search($productId, $wishlist, true)) !== false) {
                array_splice($wishlist, $key, 1);
            }
            $updateDb = Wishlist::where('userId', '=', $userId)->update(["ProductId" => implode(",", $wishlist)]);
            return response()->json([
                'status' => true,
                'message' => 'Removed product from wishList',
                '$dbresponce' => $updateDb
            ], 200);
            return $updateDb;
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Db error',
                'errors' => $e
            ], 401);
        }
    }
}
