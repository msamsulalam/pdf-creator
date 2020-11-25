<?php

namespace App\Http\Controllers;

use App\Business;
use App\OrderDetail;
use App\Orders;
use App\PackageDetail;
use App\Products;
use App\Packages;
use Auth;

use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $products_in_packages = [];
        $products = Products::all();
        $packages = Packages::all();

        foreach ($packages as $package){
            $products_in_packages[] = Packages::find($package->id)->products;
        }
        $business_id = $id;
//        $no = 1;
//        for ($i = 0; $i<count($products_in_packages[$no]);$i++){
//            echo $products_in_packages[$no][$i]->product_name;
//        }
//        dd($products_in_packages);

        return view('client.product-order', compact('packages', 'products', 'products_in_packages','business_id'));
    }

    public function autoOrder(Request $request){
       //dd($request->all());
        $date = date('Y-m-d', strtotime($request->input('order_date')));
        $order = new Orders();
        $order->client_id = Auth::user()->id;
        $order->business_id = $request->input('b_id');
        $order->date = $date;
        $order->time_range = $request->input('order_time');
//        getting total price
        $packages = Packages::all();
        $packagesids = [];
        $total = 0;
        $allorders = $request->get('order_package');


        foreach ($allorders as $package_id => $order_values){
            if($order_values[1]>0){
                $packagesids[] = $package_id;
                $price =    $packages->where('id',$package_id)->first()->price;
                $total +=   $price*$request->input('order_qty');
            }

//        end total price

        }




        $order->total = $total;
        $order->status = 1;
        $order->sos_order = 0;
        $order->save();

//        order detail
        $order_id = $order->id;
        $product_ids = PackageDetail::whereIn('packages_id', $packagesids)->get();
        //dd($packagesids,$product_ids);
//            $qty = $request->input('qty');
        foreach ($product_ids as $product_id){
            $producttt = $product_id->product;
            $producttt->product_qty = $producttt->product_qty-$product_id->qty;
            $producttt->save();

            $order_detail = new OrderDetail();
            $order_detail->order_id = $order_id;
            $order_detail->product_id = $product_id->products_id;
            $order_detail->qty = $product_id->qty;
            $order_detail->package_id = $product_id->packages_id;
            $order_detail->package_qty = $allorders[$product_id->packages_id][1];
            $order_detail->save();
        }
        return redirect()->route('client-dashboard')->with('success','Thanks for your order');
        //return back();
    }

    public function SetupAutoOrder($id, Request $request){
           //dd($request->all());

            $business =  Business::find($id);
            $user_id  =  auth()->user()->id;
            $packages = $request->get('packages');

           foreach ($packages as $package_id => $qty){
               if($qty>0){
                   $business->auto_setup_packages()->updateorcreate(['package_id'=>$package_id,'business_id'=>$business->id,'user_id'=>$user_id],['qty'=>$qty]);
               }else{
                   $business->auto_setup_packages()->where('package_id',$package_id)->delete();
               }
           }

           return redirect()->route('client-dashboard')->with('success','Auto setup saved successfully');


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {

        $packages = $request->get('packages');
        $products = $request->get('products');


        $date = date('Y-m-d', strtotime($request->input('date')));
        $order = new Orders();
        $order->client_id = Auth::user()->id;
        $order->business_id = $id;
        $order->date = $date;
        $order->time_range = $request->input('time_range');
        $order->total = 0;
        $order->status = 1;
        $order->sos_order = 0;
        $order->save();
        $totalamt = 0;

        foreach ($packages as $package_id => $qty){

            if($qty>0){
                $package = Packages::find($package_id);
                $totalamt   +=  $package->price*$qty;

                $order_id = $order->id;
                $product_ids = PackageDetail::where('packages_id', $package_id)->get();

                foreach ($product_ids as $product_id){

                    $producttt = $product_id->product;
                    $producttt->product_qty = $producttt->product_qty-$product_id->qty;
                    $producttt->save();

                    $order_detail = new OrderDetail();
                    $order_detail->order_id = $order_id;
                    $order_detail->product_id = $product_id->products_id;
                    $order_detail->qty = $product_id->qty;
                    $order_detail->package_id = $package_id;
                    $order_detail->package_qty = $qty;
                    $order_detail->save();
                }
            }

        }


        foreach ($products as $product_id => $qty){

            if ($qty>0){
                $order_detail = new OrderDetail();
                $order_id = $order->id;
                $order_detail->order_id = $order_id;
                $order_detail->product_id = $product_id;
                $order_detail->qty = $qty;
                $order_detail->save();

            }
        }

        return redirect()->route('client-dashboard')->with('success','Order placed successfully');


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function show(Orders $orders)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function edit(Orders $orders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Orders $orders)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Orders::whereId($id)->delete();
        return back();
    }

}
