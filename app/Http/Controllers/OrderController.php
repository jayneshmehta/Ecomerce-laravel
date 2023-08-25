<?php

namespace App\Http\Controllers;

use App\Mail\Demoemail;
use App\Mail\Ordermail;
use App\Models\notifications;
use App\Models\order;
use App\Models\product;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $order =  order::all();
        return $order;
    }
    /**
     * Display a listing of the resource for perticular user.
     *
     * @return \Illuminate\Http\Response
     */
    public function userOrders(User $user)
    {
        $order = order::whereIn('userId', [$user->id])->get();
        return $order;
    }

    public function ordersByGroupId($groupId)
    {
        $order = order::join('products', "products.id", "orders.productId")->select('orders.*', 'products.*', "orders.created_at as orderedDate")->where('orders.orderGroupId', $groupId)->get();
        return $order;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $userId =  $request['user']['id'];
            $contactNo = $request['user']['contactNo'];
            $ShippingAddress = $request['ShippingAddress'];
            $coupon = $request['coupon'];
            $paymentType = $request['paymentType'];
            $orderGroupid = "ORDG" . $userId . random_int(100000, 99999999);
            // $orderGroupid = $request->orderId;
            $shippingType = ($request['shippingType'] == 30) ? "Normal" : (($request['shippingType'] == 90) ? "Express" : "Prime");

            foreach ($request['allProducts'] as $key => $value) {
                $orderid = "ORD" . $userId . random_int(100000, 99999999);
                $price = (int)$value['price'];
                $discoutPrice = ($price - ($price * $value['discountPercentage']) / 100) * (int)$value['quantity'];
                $filnalPrice = $discoutPrice - (($discoutPrice * (int)$request->coupon) / 100);
                DB::table('orders')->insert([
                    "orderId" => $orderid . $value['id'],
                    "orderGroupId" => $orderGroupid,
                    "productId" => $value['id'],
                    "userId" => $userId,
                    "quantity" => $value['quantity'],
                    "ShippingAddress" => $ShippingAddress,
                    "contactNo" => $contactNo,
                    "coupon" => $coupon,
                    "paymentType" => $paymentType,
                    "shippingType" => $shippingType,
                    "TotalAmount" => $filnalPrice,
                ]);
            }

            $data = order::join('products', "products.id", "orders.productId")->join('users',"orders.userId","users.id")->select('orders.*', 'products.*', "orders.created_at as orderedDate","users.*")->where('orders.orderGroupId', $orderGroupid)->get();
            $total = 0;

            foreach ($data as $key => $value) {
                $total += (float)$value['TotalAmount'];
            }
            $sendMailto = "jayneshmehta1@gmail.com";
            try{
                Mail::to("jayneshmehta1@gmail.com")->send(new Ordermail($data,$total));
            }catch(Exception $e){
                  return $e->getMessage();  
            }
            try {
                notifications::create([
                    'userId' => $userId,
                    'activity' => 'order',
                    'message' => "order Placed successfully",
                    'icon' => "order",
                ]);
            }catch(Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ], 401);
            }
            return response()->json([
                'status' => true,
                'message' => 'Order placed Successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Order is not placed',
                "error" => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return order::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\order  $order
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, order $order)
    {
        $stock = product::select('stock')->whereId($order->productId)->first()->stock;

        $validateOrder = Validator::make(
            $request->all(),
            [
                'ShippingAddress' => 'required',
                'contactNo' => 'required|size:10',
                'quantity' => 'required|max:' . $stock,
                'updstatus' => 'required',
            ]
        );

        if ($validateOrder->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateOrder->errors()
            ], 401);
        }

        try {
            $price = $order->TotalAmount / $order->quantity;
            $id = order::where("id", $order->id)->update([
                "orderId" => $order->orderId,
                "orderId" => $order->orderId,
                "productId" => $order->productId,
                "userId" => $order->userId,
                "quantity" => $request->quantity,
                "ShippingAddress" => $request->ShippingAddress,
                "contactNo" => $request->contactNo,
                "TotalAmount" => $price * $request->quantity,
                "status" => $request->updstatus,
            ]);
            $NewOrderData = order::where("id", $order->id)->first();
            try {
                notifications::create([
                    'userId' => $order->userId,
                    'activity' => 'order',
                    'message' => "update the order",
                    'icon' => "update",
                ]);
            }catch(Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ], 401);
            }
            return response()->json([
                'status' => true,
                'message' => 'Order Updated Successfully',
                'order' => $NewOrderData,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' =>  "Not able to update the order",
                "error" => $e
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = order::findOrFail($id);

        if (!$order->delete()) {
            return response()->json([
                'status' => false,
                'message' => 'Could not delete Order!'
            ], 500);
        }
        return response()->json([
            'status' => true,
            'message' => 'Order has deleted successfully!'
        ], 200);
    }

    /**
     * Update the Status of specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\order  $order
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $orders =  order::where('orderGroupId', $id)->update(['status' => $request->status]);
            if (!$orders) {
                return response()->json([
                    'status' => false,
                    'message' => 'Could not update the Order Status!'
                ], 500);
            }
            $order = order::where('orderGroupId',$id)->first();     
            $user = $order->userId;
            try {
                notifications::create([
                    'userId' => $user,
                    'activity' => 'order',
                    'message' => "update the order status",
                    'icon' => "update",
                ]);
            }catch(Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ], 401);
            }
            return response()->json([
                'status' => true,
                'message' => 'Order status updated successfully!'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not update Order status..!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
