<?php

namespace App\Http\Controllers;

use App\Models\category;
use App\Models\order;
use App\Models\product;
use App\Models\review;
use App\Models\sub_category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product =  Product::all();
        return $product;
    }

    public function productsWithSub_category()
    {
        $product =  Product::join('sub_categories', 'sub_categories.id', "=", "products.category_id")->select('products.*', 'sub_categories.Sub_category_Name as Sub_categories')->get();
        return $product;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rule = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
                'discountPercentage' => 'required',
                'brand' => 'required',
                'stock' => 'required',
                'price' => 'required',
                'category_id' => 'required',
                'thumbnail' => 'required|mimes:jpg,png,jpeg',
                'images.*' => 'required|mimes:jpg,png,jpeg',
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
            $productid = Product::create([
                'title' => $request->title,
                'description' => $request->description,
                'discountPercentage' => $request->discountPercentage,
                'rating' => $request->rating,
                'brand' => $request->brand,
                'stock' => $request->stock,
                'price' => $request->price,
                'category_id' => $request->category_id,
            ])->id;

            if (isset($request->images)) {
                foreach ($request->images as $key => $value) {
                    $image_name = $value->getClientOriginalName();
                    $value->move(public_path("uploads/Products/product$productid"), $image_name);
                    $product['images'][$key] = url("uploads/Products/product$productid/$image_name");
                }
                $product['images'] = implode(",", $product['images']);
            }
            if (isset($request->thumbnail)) {
                $thumbnail_name = trim($request->thumbnail->getClientOriginalName(), '"');
                $request->thumbnail->move(public_path("uploads/Products/product$productid/thumbnail"), $thumbnail_name);
                $product['thumbnail'] = url("uploads/Products/product$productid/thumbnail/$thumbnail_name");
            }

            if (isset($product)) {
                $allproduct = Product::whereId($productid)->update(['images' => $product['images'], 'thumbnail' =>  $product['thumbnail']]);
                return response()->json([
                    'status' => true,
                    'message' => 'Product added Successfully',
                    'productDetails' => $product,
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $e
            ], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $products = Product::find($id);
        $products['review'] = Product::join('reviews','products.id','reviews.productId')->where("reviews.productId","=",$id)->select("reviews.*")->get();
        return $products;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        try {
            $rule = Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'description' => 'required',
                    'discountPercentage' => 'required',
                    'brand' => 'required',
                    'stock' => 'required',
                    'price' => 'required',
                    'category_id' => 'required',
                    'thumbnail' => 'mimes:jpg,png,jpeg',
                    'images.*' => 'mimes:jpg,png,jpeg',
                ]
            );
            if ($rule->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $rule->errors()
                ], 401);
            }

            $product = [
                'title' => $request->title,
                'description' => $request->description,
                'discountPercentage' => $request->discountPercentage,
                'brand' => $request->brand,
                'stock' => $request->stock,
                'price' => $request->price,
                'category_id' => $request->category_id
            ];

            if (isset($request['images'])) {
                $old_images = product::select('images')->whereId($id)->get()->first();
                $old_images = explode(",", $old_images->images);
                if (file_exists(public_path("uploads/Products/product$id"))) {
                    foreach ($old_images as $key => $value) {
                        $imagename = basename($value);
                        if (file_exists(public_path("uploads/Products/product$id/$imagename"))) {
                            // unlink(public_path("uploads/Products/product$id/$imagename"));
                        }
                    }
                }
                foreach ($request->images as $key => $value) {
                    $image_name = $value->getClientOriginalName();
                    $value->move(public_path("uploads/Products/product$id"), $image_name);
                    $product['images'][$key] = url("/uploads/Products/product$id/$image_name");
                }
                $product['images'] = implode(",", $product['images']);
            }
            if (isset($request['thumbnail'])) {
                $old_thumbnail = product::select('thumbnail')->whereId($id)->get()->first();
                $old_thumbnail = basename($old_thumbnail->thumbnail);
                if (file_exists(public_path("uploads/Products/product$id/thumbnail/$old_thumbnail"))) {
                    unlink(public_path("uploads/Products/product$id/thumbnail/$old_thumbnail"));
                }
                if (file_exists(public_path("uploads/Products/product$id"))) {
                    $thumbnail_name = trim($request->thumbnail->getClientOriginalName(), '"');
                    $request->thumbnail->move(public_path("uploads/Products/product$id/thumbnail"), $thumbnail_name);
                    $product['thumbnail'] = url("/uploads/Products/product$id/thumbnail/$thumbnail_name");
                }
            }
            $allproduct = product::whereId($id)->update($product);
            if ($allproduct) {
                return response()->json([
                    'status' => true,
                    'message' => 'Product Updated Successfully',
                    'productDetails' => $product,
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $product->errors()
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Product doesn't updated ",
                'errors' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $thumbnail = $product->thumbnail;
        $images = $product->images;
        $images = explode(",", $images);
        $thumbnail = basename($thumbnail);
        if (file_exists(public_path("uploads/Products/product$id"))) {
            if (file_exists(public_path("uploads/Products/product$id/thumbnail/$thumbnail"))) {
                unlink(public_path("uploads/Products/product$id/thumbnail/$thumbnail"));
            }
            foreach ($images as $key => $value) {
                $imagename =  basename($value);
                if (file_exists(public_path("uploads/Products/product$id/$imagename"))) {
                    unlink(public_path("uploads/Products/product$id/$imagename"));
                }
            }
            rmdir(public_path("uploads/Products/product$id/thumbnail"));
            rmdir(public_path("uploads/Products/product$id"));
        }
        try {
            order::where('productId', $id)->delete();
            if (!$product->delete()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Could not delete Product!',
                ], 500);
            }
            return response()->json([
                'status' => true,
                'message' => 'Product has deleted successfully!',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not delete Product ,Product in Order List!',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    public function getProductByCategory($id)
    {

        $product = Product::join('sub_categories', 'sub_categories.id', '=', 'products.category_id')
            ->where('sub_categories.category_id', '=', $id)->distinct()->get('products.*');
        return $product;
    }
    public function getProductBySub_Category($id)
    {
        $product = Product::where('category_id', '=', $id)->get();
        return $product;
    }
}
