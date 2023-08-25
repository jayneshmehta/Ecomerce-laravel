<?php

namespace App\Http\Controllers;

use App\Models\notifications;
use App\Models\review;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
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
            $rule = Validator::make(
                $request->all(),
                [
                    'productId' => 'required',
                    'userId' => 'required',
                ]
            );
            if ($rule->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $rule->errors()
                ], 401);
            }
            $data = [
                'productId' => $request->productId,
                'userId' => $request->userId,
                'rating' => $request->rating,
                'features' => $request->features,
                'comments' => ($request->comments != "")?$request->comments:"",
            ];
            try{
                $user = review::where(["productId" => $request->productId, "userId" => $request->userId])->get();
                if (count($user)!= 0) {
                    $addreview = review::where(["productId" => $request->productId, "userId" => $request->userId])->update($data);
                } else {
                    $addreview = review::create($data)->id;
                }
            }catch(Exception $e){
                return $e->getMessage();
            }

            if ($addreview) {
                try {
                    notifications::create([
                        'userId' => $request->userId,
                        'activity' => 'review',
                        'message' => "Write a review",
                        'icon' => "review",
                    ]);
                }catch(Exception $e) {
                    return response()->json([
                        'status' => false,
                        'message' => $e->getMessage()
                    ], 401);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Thanks for review..'
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Not able to add review..',
                'errors' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $reviews = review::join("users", 'users.id', "reviews.userId")->where('reviews.productId', "=", $id)->select('reviews.*',"users.name","users.profile",'users.id')->get();
        return $reviews;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(review $review)
    {
        //
    }
}
