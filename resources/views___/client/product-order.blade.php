@extends("layouts.backend")
@section("content")
    <div class="content-body">
        <div class="container-fluid">
            <div class="row page-titles" style="margin-bottom: 15px;">
                <div class="col p-md-0 col-md-4">
                    <a href="javascript:void(0)" onclick="javascript: window.history.back();">
                        <h4><- Order</h4>
                    </a>
                </div>
                <div class="col p-md-0">
                    <ol class="breadcrumb">
                        <li style="padding: 15px;">
                            <button type="button" class="btn btn-info btn-ft" id="setup_auto_order">Setup Auto</button>
                            <button type="button" class="btn btn-danger btn-ft"
                                    data-toggle="modal"
                                    data-target="#basicModal"
                            >Auto</button>

                        </li>
                        <li style="padding: 15px;">
                            <input id="txtfuturedate" type="text" style="width: 150px;" class="form-control" placeholder="Choose date" />
                        </li>
                        <li style="padding: 15px;">
                            <select  tabindex="-98" class="form-control" id="time-range">
                                <option>9:00</option>
                                <option>11:00</option>
                                <option>14:00</option>
                            </select>
                        </li>
                        {{--<li style="padding: 15px;">--}}
                            {{--<input class="form-check-input styled-checkbox" type="checkbox" id="sos_check">--}}
                            {{--<label class="form-check-label mr-sm-5" for="sos_check">SOS</label>--}}
                        {{--</li>--}}
                        <li class="breadcrumb-item active" style="padding: 15px;">
                            <button class="btn btn-primary btn-ft order-now">Order</button>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="row">

                @foreach (['danger', 'warning', 'success', 'info'] as $key)
                    @if(Session::has($key))
                        <div class="col-md-12 alert alert-{{ $key }}">{{ Session::get($key) }}</div>
                    @endif
                @endforeach

                <div class="col-xl-12">
                    <div class="card forms-card">
                        <div class="card-body">
                            <div class="warning-msg">

                            </div>
                            <div class="row package">
                                @php($no=0)
                                @foreach($packages as $package)
                                    @php ($autosetupqty = $package->auto_setup_qty->where('business_id',$business_id)->first())
                                    <div class="col-lg-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h3 class="card-title">{{$package->name}}</h3>
                                                <div class="form-group row align-items-center">
                                                    <label class="col-sm-12 col-form-label text-label">
                                                    @for($i=0; $i<count($products_in_packages[$no]);$i++ )
                                                        {{$products_in_packages[$no][$i]->product_name}},
                                                    @endfor
                                                    </label>
                                                </div>
                                                <div class="form-check row">
                                                    <div class="d-flex justify-content-around">
                                                        <div style="padding: 15px;">
                                                            <label >
                                                                {{--${{$package->price}}--}}
                                                            </label>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <input type="number" class="form-control"
                                                                   value="{{ $autosetupqty ? $autosetupqty->qty : 0 }}"
                                                                   package-price="{{$package->price}}"
                                                                   package-id = "{{$package->id}}"
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @php($no++)
                                @endforeach
                            </div>
                            <div class="row product">
                            @foreach($products as $product)
                            <div class="col-lg-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{$product->product_name}}</h5>
                                        <div class="form-group row align-items-center">
                                            <label class="col-sm-6 col-form-label
                                            text-label">{{$product->product_detail}}</label>
                                        </div>
                                        <div class="form-check row">
                                            <div class="d-flex">
                                                <div >
                                                    <input type="number" class="form-control"
                                                           product-id="{{$product->id}}"
                                                           product-price="{{$product->product_price}}"
                                                           value="0"
                                                    >
                                                </div>
                                                <div style="padding:14px;">
                                                    <input class="form-check-input styled-checkbox" type="checkbox" id="p_check{{$product->id}}">
                                                    <label class="form-check-label mr-sm-5" for="p_check{{$product->id}}">Add</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>



        <div class="modal fade" id="basicModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body auto-order-popup">
                        <form action="{{route('auto-order')}}" id="auto_order_form" method="post">
                            @csrf
                            <input type="hidden" value="{{ $business_id }}" id="business-id"  name="b_id">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date</label>
                                        <input required id="txtfuturedate2" type="text" class="form-control" name="order_date" placeholder="Choose date" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Time</label>
                                        <select class="form-control" name="order_time">
                                            <option>09:00</option>
                                            <option>11:00</option>
                                            <option>14:00</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" style="margin-bottom: 5px">
                                        <label>Package</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" style="margin-bottom: 5px">
                                        <label>Quantity</label>
                                    </div>
                                </div>
                            </div>
                            @foreach($packages as $package)
                                @php ($autosetupqty = $package->auto_setup_qty->where('business_id',$business_id)->first())

                                <div class="row packages">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" readonly name="order_package[{{$package->id}}][]" value="{{$package->name}}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="number" class="form-control order-qty" value="{{ $autosetupqty ? $autosetupqty->qty : 0 }}" name="order_package[{{$package->id}}][]" placeholder="Quantity">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="row">
                                <div class="col-md-12 alert alert-danger" style="display: none" id="show_popup_error"></div>
                            </div>
                            <div class="modal-footer justify-content-center">

                                <button type="submit" class="btn btn-primary" id="auto">Order</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <script>
        jQuery(document).ready(function($) {
            $('#txtfuturedate').datepicker({
                minDate: 0,
            });

            $('#txtfuturedate2').datepicker({
                minDate: 0,
            });

            $("#auto_order_form").submit(function () {
                var valid  = false;
                $('.order-qty').each(function () {
                    if($(this).val() > 0 ){
                        console.log($(this).val());
                        valid = true;
                    }
                });
                if(valid==false){
                    $("#show_popup_error").html('Please select at least one package').show();
                    return false;
                }else{
                    $("#show_popup_error").html('').hide();
                    return true;
                }
            })

        });
        //alert function for delay 3 seconds
        function alertTimeout(wait){
            setTimeout(function(){
                $('.warning-msg .alert').remove();
            }, wait);
        }
        var flag = 0;
        var product_flag = 0;
        var business_id = '{{$business_id}}';
        //order button
        $('.order-now').click(function () {

            if ($('#txtfuturedate').val()==''){
                var myvar = '<div class="alert alert-danger">Please select a date!</div>';
                $('.warning-msg').html(myvar);
                alertTimeout(3000);

            }else{
                //package part
                $(".package input[type='number']").each(function() {
                    if ($(this).val() == "0") {
                        // console.log("empty qty!");
                    }else {
                        flag = 1;
                        // console.log($(this).val());
                        // console.log(business_id);
                        // console.log($(this).attr('package-price'));
                        // console.log($('#txtfuturedate').val());
                        // console.log($('#time-range').val());
                        var b_id = business_id;
                        var date = $('#txtfuturedate').val();
                        var time_range = $('#time-range').val();
                        var total = $(this).attr('package-price');
                        //order detail fields
                        var package_id = $(this).attr('package-id');
                        var qty = $(this).val();
                        $.post("{{route('product-order', $business_id)}}",{
                            _token:'{{csrf_token()}}',
                            b_id:b_id, date:date, time_range:time_range,
                            total_price:total, package_id: package_id, qty:qty
                        }).done(
                            function (data) {
                                console.log(data);
                                {{--document.location.href = '{{route('package')}}'--}}
                            }
                        );
                    }
                });
            //    product part
                $(".product input[type='number']").each(function() {
                    if ($(this).val() == "0") {
                        // console.log("empty qty!");
                    }else {
                        flag = 1;
                        var b_id = business_id;
                        var date = $('#txtfuturedate').val();
                        var time_range = $('#time-range').val();
                        var total = $(this).attr('product-price')*$(this).val();
                        //order detail fields
                        var product_id = $(this).attr('product-id');
                        var qty = $(this).val();
                        $.post("{{route('product-order', $business_id)}}",{
                            _token:'{{csrf_token()}}',
                            b_id:b_id, date:date, time_range:time_range,
                            total_price:total, product_id: product_id, qty:qty
                        }).done(
                            function (data) {
                                console.log(data);
                                {{--document.location.href = '{{route('package')}}'--}}
                                product_flag = 1;
                            }
                        );
                    }
                });
            }
            //if product not select
            if (flag == 0){
                var myvar = '<div class="alert alert-danger">Please select a product!</div>';
                $('.warning-msg').html(myvar);
                alertTimeout(3000);
            }
            if (flag==1 || product_flag == 1){
                document.location.href = '{{route('client-dashboard')}}';
            }

        });


        $('#setup_auto_order').click(function () {

                flag == 0;
                //package part
                $(".package input[type='number']").each(function() {

                        flag = 1;

                        var b_id = business_id;
                        var package_id = $(this).attr('package-id');
                        var qty = $(this).val();
                        $.post("{{route('setup-auto-order', $business_id)}}",{
                            _token:'{{csrf_token()}}',
                            b_id:b_id, package_id: package_id,qty:qty
                        }).done(
                            function (data) {
                                console.log(data);
                                {{--document.location.href = '{{route('package')}}'--}}
                            }
                        );

                });


            //if product not select
            if (flag == 0){
                var myvar = '<div class="alert alert-danger">Please select a product!</div>';
                $('.warning-msg').html(myvar);
                alertTimeout(3000);
            }
            if (flag==1){
                document.location.href = '{{route('client-dashboard')}}';
            }

        })



    </script>
@endsection
