@extends("layouts.backend")
@section("content")
    <div class="content-body">
        <div class="container-fluid">
            <div class="row page-titles">
                <div class="col p-md-0">
                    <h4>Reservation</h4>
                </div>
                <div class="col-md-3 p-md-0">
                    <form method="get">
                        <select name="place" class="form-control select2" onchange="this.form.submit();">
                            <option value="">Select Place</option>
                            @foreach($businesses as $business)
                                <option @if(request('place','')==$business->id) selected @endif value="{{ $business->id }}">{{ $business->address }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            <div class="row">
                @foreach (['danger', 'warning', 'success', 'info'] as $key)
                    @if(Session::has($key))
                        <div class="col-md-12 alert alert-{{ $key }}">{{ Session::get($key) }}</div>
                    @endif
                @endforeach
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table header-border" style="min-width: 500px;">
                                    <tbody>
                                    @foreach($businesses as $business)
                                        <?php
                                            if(request()->filled('place')){
                                                if($business->id!=request('place')) continue;
                                            }
                                        ?>
                                    <tr>
                                        <td>{{$business->address}}
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-ft"
                                                    data-toggle="modal"
                                                    data-target="#basicModal_{{ $business->id }}">Auto</button>

                                            <div class="modal fade" id="basicModal_{{ $business->id }}">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body auto-order-popup">
                                                            <form action="{{route('auto-order')}}" class="auto_order_form" method="post">
                                                                @csrf
                                                                <input type="hidden" value="{{ $business->id }}" id="business-id"  name="b_id">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Date</label>
                                                                            <input required type="text" class="form-control txtfuturedate" name="order_date" placeholder="Choose date" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Time</label>
                                                                            <select class="form-control" name="order_time">
                                                                                @foreach(\App\TimeRange::orderby('order')->get() as $time)
                                                                                    <option value="{{ $time->title }}">{{ $time->title }}</option>
                                                                                @endforeach
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
                                                                    @php ($autosetupqty = $package->auto_setup_qty->where('business_id',$business->id)->first())
                                                                    <div class="row packages">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <input type="text" class="form-control" readonly name="order_package[{{$package->id}}][]" value="{{$package->name}}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <input type="number" value="{{ $autosetupqty ? $autosetupqty->qty : 0 }}" class="form-control order-qty" name="order_package[{{$package->id}}][]" placeholder="Quantity">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                                <div class="row">
                                                                    <div class="col-md-12 alert alert-danger show_popup_error" style="display: none"></div>
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
                                        </td>
                                        <td><a href="{{route('product-order', $business->id)}}" class="btn btn-info
                                        btn-ft">Store</a></span>
                                        </td>
                                        <td style="font-size: 18px;">
                                            @foreach($orders as $order)
                                                @if($business->id == $order->business_id)
                                                    <i class="fa fa-check-circle" style="color: green;"></i>
                                                    <span style="padding-right: 10px;">{{date('j F', strtotime
                                                    ($order->date))
                                                    }}</span>
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>

                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->



    <script>
        jQuery(document).ready(function($) {
            $('.txtfuturedate').datepicker({
                minDate: 0,
            });
            //$('#auto').prop('disabled', true);

        //    check date
            $('#txtfuturedate').on('change', function() {
                //$('#auto').prop('disabled', false);
            });
            // $('.btn-ft').attr('data-target', '#basicModal');

            $(".auto_order_form").submit(function () {
                var valid  = false;
                $(this).find('.order-qty').each(function () {
                    if($(this).val() > 0 ){
                        console.log($(this).val());
                        valid = true;
                    }
                });
                if(valid==false){
                    $(this).find(".show_popup_error").html('Please select at least one package').show();
                    return false;
                }else{
                    $(".show_popup_error").html('').hide();
                    return true;
                }
            })



        });
        /*function auto(b_id) {
            $('#business-id').val(b_id);
            $('.btn-ft').attr('data-target', '#basicModal');
        }*/
    </script>
@endsection
