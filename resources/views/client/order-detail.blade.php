@extends("layouts.backend")
@section("content")

    <div class="content-body">
        <div class="container-fluid">
            <div class="row page-titles">
                <div class="col p-md-0">
                    <a href="javascript:void(0)" onclick="javascript: window.history.back();">
                        <h4><- Order Detail</h4>
                    </a>
                </div>
            </div>
            <div class="row">

                <div class="col-xl-12">
                    <div class="card forms-card">
                        <div class="card-body">
                            <div class="address">
                                <div><h3>CITY: {{$order->order_address->city}}</h3></div>
                                <div><h3>FLOOR: {{$order->order_address->floor}}</h3></div>
                            </div>
                            <div class="row package">
                                @foreach($order->details->groupby('package_id') as $package => $details)
                                    <h4 class="col-md-12">Package: {{ \App\Packages::find($package)->name }}</h4>
                                    @foreach($details as $detail)
                                        <div class="col-lg-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">{{ $detail->product->product_name }}</h3>
                                                    <div class="form-group row align-items-center">
                                                        <label class="col-sm-12 col-form-label text-label">
                                                            Quantity: {{  $detail->qty }}
                                                        </label>
                                                    </div>
                                                    <div class="form-group row align-items-center">
                                                        <label class="col-sm-12 col-form-label text-label">
                                                            {{ $detail->product->prduct_detail }}
                                                        </label>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
