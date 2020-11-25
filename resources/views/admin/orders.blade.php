@extends("layouts.backend")
@section('content')
    <div class="content-body">
        <div class="container-fluid">
            <div class="row page-titles">
                <div class="col-md-3 p-md-0">
                    <h4>Orders</h4>
                </div>
                <div class="col-md-9 p-md-0">

                    <ol class="breadcrumb d-flex align-items-center">
                        <li class="ml-2">
                            <label>Client name</label>
                        </li>
                        <li class="ml-2">
                            <select class="form-control" id="client-select">
                                <option value="">All</option>
                                @foreach($clients as $client)
                                    <option @if(request('client_id','')==$client->id) selected @endif value="{{$client->id}}">{{$client->name}}</option>
                                @endforeach
                            </select>
                            <form action="{{route('orders')}}" method="get" id="filter_order">
                                <input type="hidden" name="order_date" id="order_date">
                                <input type="hidden" name="client_id" id="client_id">
                            </form>
                        </li>
                        <li class="ml-5">
                            <input id="txtfuturedate" class="form-control" value="{{ request('order_date','') }}" name="order_date" type="text" style="width: 200px;"
                                   placeholder="From "/>
                            <input type="hidden" value="" name="start_date" id="start_date">
                            <input type="hidden" value="" name="end_date" id="end_date">
                        </li>
                        <li class="ml-2">
                            <a href="{{route('orders')}}" class="btn btn-primary btn-ft">Reset</a>
                        </li>
                        <li class="ml-5">
                            <form action="{{route('pdf-export')}}" method="post" id="order">
                                @csrf
                                <input type="hidden" name="order_date" id="filter-date">
                                <input type="hidden" name="client_id" id="filter-client">
                            </form>
                            <a href="javascript:void(0)" class="pdf btn
                            btn-success btn-ft">Export To PDF</a>
                            {{--<a href="#" class=" btn--}}
                            {{--btn-success btn-ft" style="display: none;">Export To  PDF</a>--}}
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
                                        <th>Client Name</th>
                                        <th>Adress</th>
                                        {{--<th width="40%">Products</th>--}}
                                        <th>Packages</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>{{ $order->time_range }}</td>
                                            <td>{{ $order->date }}</td>
                                            <td>{{ $order->client->name }}</td>
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
                                            <td align="center">
                                                <a href="{{ route('order-delete',
                                                $order->id) }}"
                                                   data-toggle="tooltip"
                                                   data-placement="top" title="" class="delete_order" data-original-title="delete"><i
                                                        class="fa fa-trash" style="color: red;"></i></a></td>
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
        jQuery(document).ready(function ($) {
            $('#txtfuturedate').daterangepicker({});
            $('#txtfuturedate').on('apply.daterangepicker', function(ev, picker) {
                //console.log(picker.startDate.format('YYYY-MM-DD'));
                //console.log(picker.endDate.format('YYYY-MM-DD'));
                $("#start_date").val(picker.endDate.format('YYYY-MM-DD'));
                $("#end_date").val(picker.endDate.format('YYYY-MM-DD'));


            });

            $('.delete_order').click(function () {
                if(!confirm('Delete order ? ')){
                    return false;
                }
            })

            $('.pdf').click(function (e) {
                e.preventDefault();
                if ($('#txtfuturedate').val() == '') {
                    var myvar = '<div class="alert alert-danger">Please select a date!</div>';
                    $('.warning-msg').html(myvar);
                    alertTimeout(3000);
                } else {
                    var order_date = $('#txtfuturedate').val();
                    var client_id = $('#client-select').val();
                    $('#filter-date').val(order_date);
                    $('#filter-client').val(client_id);
                    $('#order').submit();
                    // $('.test').attr('href', 'pdf-export/'+order_date);
                    // $('.test')[0].click();

                }
            });
            //according to client
            $('#client-select').change(function () {
                $("#order_date").val($('#txtfuturedate').val());
                $("#client_id").val($('#client-select').val());

                $("#filter_order").submit();



            });
            // searching according to date
            $("#txtfuturedate").change(function () {
                $("#order_date").val($('#txtfuturedate').val());
                $("#client_id").val($('#client-select').val());

                $("#filter_order").submit();

            });
        });

        function alertTimeout(wait) {
            setTimeout(function () {
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
