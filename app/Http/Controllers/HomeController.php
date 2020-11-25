<?php

namespace App\Http\Controllers;

use App\Area;
use App\OrderDetail;
use App\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\User;
use App\Products;
use App\Packages;
use App\PackageDetail;
use App\Business;
use PDF;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->is_admin()) {
            return redirect('admin/orders');
        } else if ($user->is_client()) {
            return redirect('client/dashboard');
        } else if ($user->is_user()) {
            return redirect('user/order-list');
        }
    }

    public function orders(Request $request)
    {


        $orderquery = Orders::has('client')->with(['details', 'details.product', 'details.package']);

        if ($request->filled('order_date')) {
            $date = $request->get('order_date');
            $filter = explode('-', $date);
            $start_date = date('Y-m-d', strtotime(trim($filter[0])));
            $end_date = date('Y-m-d', strtotime(trim($filter[1])));

        } else {
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d');
        }

        $orderquery->where('date', '>=', $start_date)->where('date', '<=', $end_date);
        if ($request->filled('client_id')) {
            $orderquery->where('client_id', $request->input('client_id'));
        }


        $orders = $orderquery->orderby('date')->get();


        $clients = User::where('role', 'client')->get();
        return view('admin.orders', compact('orders', 'clients'));
    }

    public function filterOrder(Request $request)
    {
        $date = $request->get('filter_date');
        $filter = explode('-', $date);
        $start_date = date('Y-m-d', strtotime(trim($filter[0])));
        $end_date = date('Y-m-d', strtotime(trim($filter[1])));

        //$filter_date = date('Y-m-d', strtotime($request->input('filter_date')));
        $client_id = $request->input('client_id');
        $final_orders = [];
        $products = [];
        $temp_products = [];
        $package_name = [];
        $orders = '';
        if ($client_id == 'all') {
            $orders = Orders::has('client')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->orderby('date')->get();
        } else {
            $orders = Orders::has('client')->where('date', '>=', $start_date)->where('date', '<=', $end_date)
                ->where('client_id', $client_id)->orderby('date')->get();
        }
        foreach ($orders as $order) {
            $client_name = User::whereId($order->client_id)->select('name')->get();
            $address = Business::whereId($order->business_id)->get();
            $items = OrderDetail::where('order_id', $order->id)->select('product_id', 'package_id')->get();
//            dd($items);
            foreach ($items as $item) {
                $p_name = Products::whereId($item->product_id)->get();
//                dd($p_name);
                $temp = array([
                    'product_name' => $p_name[0]->product_name,
                    'product_qty' => $item->qty
                ]);
                array_push($temp_products, $temp);
                $package_name = Packages::whereId($item->package_id)->get();
            }
            array_push($products, $temp_products);
            $temp_products = [];

//            dd($package_name[0]->name);
            $total_orders = array([
                'time' => $order->time_range,
                'date' => $order->date,
                'client' => $client_name[0]->name,
                'address' => $address[0]->address,
                'product' => $products,
                'package' => count($package_name) ? $package_name[0]->name : ""
            ]);

            array_push($final_orders, $total_orders);
            $products = [];
        }
        return response()->json($final_orders);
    }

//    pdf download
    public function pdfDownload(Request $request)
    {

        $orderquery = Orders::has('client')->with(['details', 'details.product', 'details.package']);

        if ($request->filled('client_id')) {
            $orderquery->where('client_id', $request->input('client_id'));
        }
        if ($request->get('order_date')) {
            $date = $request->get('order_date');
            $filter = explode('-', $date);
            $start_date = date('Y-m-d', strtotime(trim($filter[0])));
            $end_date = date('Y-m-d', strtotime(trim($filter[1])));
            $orderquery->where('date', '>=', $start_date)->where('date', '<=', $end_date);
        }

        $orders = $orderquery->orderby('date')->get();
        $order_date = @$request->order_date;


        //dd($orders);
        //return view('pdf', compact('final_orders'));
        $pdf = PDF::loadView('pdf', compact('orders', 'order_date'));

        return $pdf->download('orders.pdf');
    }

    public function dashboard()
    {
        $ordered = Orders::where('status', 1)->get();
        $ordered = count($ordered);
        $delivered = Orders::where('status', 2)->get();
        $delivered = count($delivered);
//        products
        $products = Products::all();
        return view('admin.dashboard', compact('ordered', 'delivered', 'products'));
    }

    public function product()
    {
        $products = Products::all();
        return view('admin.product', compact('products'));
    }

    public function package()
    {
        $product_list = [];
        $packages = Packages::all();
        foreach ($packages as $package) {
            $package_list[] = $package->id;
            $product_list[] = Packages::find($package->id)->products;
        }
//        $no = 0;
//        foreach ($packages as $package){
////            echo $package->id."<br>";
//            for ($i=0;$i<count($product_list[$no]);$i++){
//                echo $product_list[$no][$i]->product_name."<br>";
//            }
//            $no++;
//        }
//        dd($product_list);
        return view('admin.package', compact('packages', 'product_list'));
    }

//    client CRUD
    public function clients()
    {
        $clients = User::where('role', 'client')->get();
        return view('admin.clients', compact('clients'));
    }

    public function newClient()
    {
        return view('admin.add-newclient');
    }

    public function addClient(Request $request)
    {

        if ($request->hasFile('profile')) {
            $profile_img = $request->file('profile')->getClientOriginalName();
            $request->file('profile')->move(public_path('avatar'), $profile_img);
        }
        $client = new User();
        $client->name = $request->input('name');
        $client->email = $request->input('email');
        $client->password = Hash::make($request->input('password'));
        $client->avatar = $request->file('profile')->getClientOriginalName();
        $client->address = $request->input('address');
        $client->phone = $request->input('phone');
        $client->status = $request->input('status');
        $client->note = $request->input('note');
        $client->role = 'client';

        $client->save();
        return redirect('admin/users/clients');
    }

    public function updateGet($id)
    {
        $client = User::where('id', $id)->get();

        return view('admin.update-client', compact('client'));
    }

    public function updateClient(Request $request, $id)
    {
        $profile = 'default.png';
        if ($request->hasFile('profile')) {
            $profile_img = $request->file('profile')->getClientOriginalName();
            $request->file('profile')->move(public_path('avatar'), $profile_img);
            $profile = $profile_img;
        }
        $formData = array(
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'avatar' => $profile,
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
            'status' => $request->input('status'),
            'note' => $request->input('note')
        );
        User::where('id', $id)->update($formData);
        return back();
    }

    public function deleteClient($id)
    {
        User::whereId($id)->delete();
        return redirect()->route('clients');
    }
//    end client
// delivery man
    public function deliveryMan()
    {
        $deliverys = User::where('role', 'user')->get();
        return view('admin.delivery', compact('deliverys'));
    }

    public function newDeliveryMan()
    {
        return view('admin.add-newdelivery');
    }

    public function addDeliveryMan(Request $request)
    {
        if ($request->hasFile('profile')) {
            $profile_img = $request->file('profile')->getClientOriginalName();
            $request->file('profile')->move(public_path('avatar'), $profile_img);
        }
        $client = new User();
        $client->name = $request->input('name');
        $client->email = $request->input('email');
        $client->password = Hash::make($request->input('password'));
        $client->avatar = $request->file('profile')->getClientOriginalName();
        $client->address = $request->input('address');
        $client->phone = $request->input('phone');
        $client->status = $request->input('status');
        $client->note = $request->input('note');
        $client->role = 'user';

        $client->save();
        return redirect('admin/users/delivery-man');
    }

    public function getDelivery($id)
    {
        $delivery = User::where('id', $id)->get();
        return view('admin.delivery-update', compact('delivery'));
    }

    public function updateDelivery(Request $request, $id)
    {
        $profile = 'default.png';
        if ($request->hasFile('profile')) {
            $profile_img = $request->file('profile')->getClientOriginalName();
            $request->file('profile')->move(public_path('avatar'), $profile_img);
            $profile = $profile_img;
        }
        $formData = array(
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'avatar' => $profile,
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
            'status' => $request->input('status'),
            'note' => $request->input('note')
        );
        User::where('id', $id)->update($formData);
        return redirect()->route('delivery');
    }

    public function deleteDelivery($id)
    {
        User::whereId($id)->delete();
        return redirect()->route('delivery');
    }
//    end delivery man

//places start
    public function places()
    {
        $users = User::all();
        return view('admin.places', compact('users'));
    }

    public function placeDetail($id)
    {
        $place_details = Business::where('client_id', $id)->get();
        $areas = Area::all();
        return view('admin.place-detail', compact('place_details', 'areas'));
    }

    public function addArea(Request $request, $id)
    {

        Business::whereId($id)->update(['area_id' => $request->input('area')]);
        return back();
    }

//end places
    public function areas()
    {
        $areas = Area::all();
        return view('admin.areas', compact('areas'));
    }

//    client part
    public function cDashboard(Request $request)
    {
        $businesses = Business::where('client_id', Auth::user()->id)->get();
        $current_date = date('Y-m-d');
        $orders = Orders::where('date', '>=', $current_date)
            ->where('client_id', Auth::user()->id)->get();

        $packages = Packages::all();
        return view('client.dashboard', compact('businesses', 'orders', 'packages'));
    }

    public function cCatalog()
    {
        $products = Products::all();
        return view('client.catalog', compact('products'));
    }

//    cplace part
    public function cPlaces()
    {
        $businesses = Business::where('client_id', Auth::user()->id)->get();
        return view('client.places', compact('businesses'));
    }

    public function addPlace()
    {
        return view('client.add-place');
    }

    /**
     * @param Request $request
     */

    public function storePlace(Request $request)
    {

        $client_id = Auth::user()->id;
        $address = $request->input('address');
        $city = $request->input('city');
        $floor = $request->input('floor');
        $number = $request->input('number');
        $contact_number = $request->input('contact_number');
        $code = $request->input('code');
        $note = $request->input('note');

        $business = new Business();
        $business->address = $address;
        $business->city = $city;
        $business->floor = $floor;
        $business->house_cnt = $number;
        $business->note = $note;
        $business->code = $code;
        if ($request->input('apartment')) {
            $business->kind = 0;
        } else {
            $business->kind = 1;
        }

        $business->contact = $contact_number;
        $business->client_id = $client_id;
        $business->save();
        return redirect()->route('client-places');
    }

    public function getPlace($id)
    {
        $place = Business::where('id', $id)->get();
        return view('client.update-place', compact('place'));
    }

    public function updatePlace(Request $request, $id)
    {
        $address = $request->input('address');
        $city = $request->input('city');
        $floor = $request->input('floor');
        $number = $request->input('number');
        $contact_number = $request->input('contact_number');
        $code = $request->input('code');
        $note = $request->input('note');
        if ($request->input('apartment')) {
            $kind = 0;
        } else {
            $kind = 1;
        }
        Business::where('id', $id)->update(['address' => $address, 'city' => $city, 'floor' => $floor, 'house_cnt' => $number,
            'note' => $note, 'code' => $code, 'kind' => $kind, 'contact' => $contact_number]);
        return redirect()->route('client-places');

    }

    public function placeDestroy($id)
    {
        Business::where('id', $id)->delete();
        return redirect()->route('client-places');
    }

    //    end cplace part
    public function orderHistory(Request $request)
    {


        $orderquery = Orders::where('client_id', Auth::user()->id);
        if ($request->filled('order_date')) {
            $date = $request->get('order_date');
            $filter = explode('-', $date);
            $start_date = date('Y-m-d', strtotime(trim($filter[0])));
            $end_date = date('Y-m-d', strtotime(trim($filter[1])));
            $orderquery->where('date', '>=', $start_date)->where('date', '<=', $end_date);
        }


        $orders = $orderquery->orderBy('date', 'DESC')->get();


        return view('client.order-history', compact('orders'));
    }

    public function cProfile()
    {
        return view('client.profile');
    }

    public function updateProfile(Request $request, $id)
    {
        $profile_img = 'default.png';
        if ($request->hasFile('profile')) {
            $profile_img = $request->file('profile')->getClientOriginalName();
            $request->file('profile')->move(public_path('avatar'), $profile_img);
        }
        $name = $request->input('name');
        $email = $request->input('email');
        $avatar = $profile_img;
        $address = $request->input('address');
        $phone = $request->input('phone');
        User::whereId($id)->update(['name' => $name, 'email' => $email, 'avatar' => $avatar, 'address' => $address
            , 'phone' => $phone]);
        return back();
    }

//    delivery user
    public function orderList(Request $request)
    {
        $final_orders = [];
        $orders = Orders::all();
        $areas = Area::all();

        $orders = Orders::has('client');

        if ($request->filled('order_date')) {
            $date = $request->get('order_date');
            $filter = explode('-', $date);
            $start_date = date('Y-m-d', strtotime(trim($filter[0])));
            $end_date = date('Y-m-d', strtotime(trim($filter[1])));
            $orders->where('date', '>=', $start_date)->where('date', '<=', $end_date);
        }


        $orders = $orders->get();
        //dd($orders);
        foreach ($orders as $order) {
            $where_to_put = '';
            $client_name = User::whereId($order->client_id)->select('name')->get();
            $business = Business::whereId($order->business_id)->get();
            if (isset($business[0]) && $business[0]->kind === 0) {
                $kind = 'Apartment';
            } else $kind = 'Hotel';
            if (isset($business[0])) {
                $where_to_put = $business[0]->city;
            }
            if (!isset($client_name[0])) continue;
            $total_orders = array([
                'time' => $order->time_range,
                'date' => $order->date,
                'customer' => isset($client_name[0]) ? $client_name[0]->name : '',
                'address' => isset($business[0]) ? $business[0]->address : '',
                'kind' => $kind,
                'house_cnt' => isset($business[0]) ? $business[0]->house_cnt : '',
                'floor' => isset($business[0]) ? $business[0]->floor : '',
                'code' => isset($business[0]) ? $business[0]->code : '',
                'where_to_put' => $where_to_put,
                'order_id' => $order->id
            ]);

            array_push($final_orders, $total_orders);
        }
//        dd($final_orders);
        return view('user.order-list', compact('final_orders', 'areas'));
    }

    public function userOrderDetail($id)
    {

        $order = Orders::findorfail($id);
        return view('user.order-detail', compact('order'));


    }
}
