@extends("layouts.backend")
@section("content")
    <div class="content-body">
        <div class="container-fluid">
            <div class="row page-titles">
                <div class="col p-md-0">
                    <h4>Order History</h4>
                </div>
                <div class="col p-md-0">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">
                            <a href=""></a>
                        </li>
                        <li class="ml-5">
                            <form action="" method="get" id="filter_order">
                                <input id="txtfuturedate" class="form-control" value="{{ request('order_date','') }}" name="order_date" type="text" style="width: 200px;"
                                       placeholder="From "/>
                            </form>
                        </li>
                        <li class="ml-2">
                            <a href="{{route('order-history')}}" class="btn btn-primary btn-ft">Reset</a>
                        </li>
                        <li class="ml-5">
                            <form action="{{route('pdf-export')}}" method="post" id="order">
                                @csrf
                                <input type="hidden" name="client_id" value="{{ auth()->id() }}">
                            </form>
                            <a href="javascript:void(0)" class="pdf btn
                            btn-success btn-ft">Export To PDF</a>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="warning-msg"></div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive" id="r-order">
                                <table id="example2" class="table header-border" style="min-width: 500px;">
                                    <thead>
                                    <tr>
                                        <th>Hour</th>
                                        <th>Date</th>
                                        <th>Address</th>
                                        {{--<th width="40%">Products</th>--}}
                                        <th>Packages</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>{{ $order->time_range }}</td>
                                            <td>{{ $order->date }}</td>
                                            <td>{{ $order->order_address->address }}</td>

                                            <td>
                                                <?php $packages = []; ?>
                                                @foreach($order->details as $detail)
                                                    @if($detail->package)
                                                        <?php $packages[$detail->package_id]['name'] = $detail->package->name; ?>
                                                        <?php $packages[$detail->package_id]['qty'] = $detail->package_qty; ?>
                                                    @endif
                                                @endforeach
                                                @foreach($packages as $package )
                                                    {{ $package['name'] }} - {{ $package['qty'] }}<br>
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
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script>
        $(document).ready(function ($) {
            $('#txtfuturedate').daterangepicker({});
            $('#txtfuturedate').on('apply.daterangepicker', function(ev, picker) {
                //console.log(picker.startDate.format('YYYY-MM-DD'));
                //console.log(picker.endDate.format('YYYY-MM-DD'));
                $("#start_date").val(picker.endDate.format('YYYY-MM-DD'));
                $("#end_date").val(picker.endDate.format('YYYY-MM-DD'));


            });
            /*$('.order-delete').click( function () {
                var myvar = '<div class="alert alert-danger">Can not delete because of time out.!</div>';
                $('.warning-msg').html(myvar);
                alertTimeout(3000);
            });*/

            $("#txtfuturedate").change(function () {
                $("#order_date").val($('#txtfuturedate').val());
                $("#filter_order").submit();

            });

            $('.pdf').click(function (e) {
                e.preventDefault();
                if ($('#txtfuturedate').val() == '') {
                    var myvar = '<div class="alert alert-danger">Please select a date!</div>';
                    $('.warning-msg').html(myvar);
                    alertTimeout(3000);
                } else {
                    $('#order').submit();
                }
            });

        });
        function alertTimeout(wait){
            setTimeout(function(){
                $('.warning-msg .alert').remove();
            }, wait);
        }

        jQuery(document).ready(function () {
            $('#example2').dataTable({
                "bProcessing": true,
                "sAutoWidth": false,
                "bDestroy":true,
                "sPaginationType": "bootstrap", // full_numbers
                "iDisplayStart ": 10,
                "iDisplayLength": 10,
                "bPaginate": false, //hide pagination
                "bFilter": true, //hide Search bar
                "bInfo": false, // hide showing entries
            })
        });
    </script>
@endsection
