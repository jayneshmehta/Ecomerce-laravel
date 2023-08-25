<?php

namespace App\Http\Controllers;

use App\Models\Coupons;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupons =  Coupons::all();
        return $coupons;
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

            $validated = Validator::make(
                $request->all(),
                [
                    'name' => 'required|unique:coupons',
                    'ExpireDate' => 'required|after:yesterday',
                    'discountPercentage' => 'required|max:70',
                ]
            );
            if ($validated->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validated->errors(),
                ], 500);
            }

            $coupon = Coupons::create([
                "name" => $request->name,
                "ExpireDate" => $request->ExpireDate,
                "discountPercentage" => $request->discountPercentage,
            ]);

            if ($coupon) {
                return response()->json([
                    'status' => true,
                    'message' => 'Coupon successfully added'
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => 'Coupon can\'t be added',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'unable to add the coupons',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function showbyId($id)
    {
        return Coupons::find($id);
    }
    /**
     * Display the specified resource.
     */
    public function show($name)
    {
        try {
            $coupon =  Coupons::where("name", $name)->first();
            if ($coupon) {

                if (date('Y-m-d H:i:s', strtotime("now")) < $coupon->ExpireDate) {
                    return response()->json([
                        'status' => true,
                        "discount" => $coupon->discountPercentage,
                        'message' => 'Coupon Applied .. ',
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Coupon is Expired ..',
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Coupon is Not valid ..',
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Coupon not valid',
                'errors' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function changeStatus(Request $request, Coupons $coupon)
    {
        try {

            $rule = Validator::make(
                $request->all(),
                [
                    'status' => 'required',
                ]
            );
            if ($rule->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $rule->errors(),
                ], 500);
            }

            $sattus = ($request->status) ? "active" : "Inactive";
            $stat = Coupons::whereId($coupon->id)->update(['status' => $sattus]);
            if ($stat) {
                return response()->json([
                    'status' => true,
                    'message' => 'Status has been updated'
                ], 200);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Status has been updated'
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Not able to Update the status..',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupons $coupons)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupons $coupon)
    {
        $rule = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'discountPercentage' => 'required|max:70',
                'ExpireDate' => 'required',
            ]
        );
        if ($rule->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $rule->errors(),
            ], 500);
        }
        $expiredate = $request->ExpireDate;
        $discountPercentage = $request->discountPercentage;
        $name = $request->name;
        $stat = Coupons::whereId($coupon->id)->update(['ExpireDate' => $expiredate, 'discountPercentage' => $discountPercentage, 'name' => $name]);
        if ($stat) {
            return response()->json([
                'status' => true,
                'message' => 'Coupon has been updated'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Coupon has not been updated'
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupons $coupon)
    {
        try{
            Coupons::whereId($coupon->id)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Coupon has been deleted'
            ], 200);
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Coupon can not been deleted',
                "error" => $e->getMessage()
            ], 401);
        }
    }
}
