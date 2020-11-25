@extends('layouts.backend')
@section('content')
    <style>
        .dataTables_filter{
            display: flex;
            justify-content: center;
            float: none !important;
            margin-top: 0px !important;
            height: 100px !important;
        }
    </style>
    <div class="content-body">
        <div class="container-fluid">
            <div class="row page-titles">
                <div class="col p-md-0">
                    <h4>Orders</h4>
                </div>
                <div class="col-md-6 p-md-0 d-flex">
                    <form id="filter_form" method="get" style="width: 100%;">
                        <div class="row">
                        <div class="col-md-6">
                            <label>Date</label>
                            <input id="txtfuturedate" class="form-control" value="{{ request('date','') }}" name="order_date" type="text" style="width: 200px;" placeholder="Date"/>
                        </div>
                        <div class="col-md-6">
                        <label>Area</label>
                        <select name="area" class="form-control" onchange="this.form.submit();">
                            @foreach($areas as $area)
                                <option>{{$area->area_name}}</option>
                            @endforeach
                        </select>
                        </div>
                        </div>
                    </form>
                    <form action="{{route('pdf-export')}}" method="post" id="order" style="margin-top: 26px;margin-left: 15px;">
                        @csrf
                        <input type="hidden" value="{{ request('order_date','') }}" name="order_date" id="filter-date">
                        <input type="hidden" name="client_id" id="filter-client">
                        <input type="submit" value="Export To PDF" class="pdf btn btn-success btn-ft">
                    </form>

                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example2" class="display" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Hour</th>
                                        <th>Customer</th>
                                        <th>Address</th>
                                        <th>Code</th>
                                        <th>Floor</th>
                                        <th>Number of House</th>
                                        <th>Where to put</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($no=0)
                                    @for($i=0;$i<count($final_orders);$i++)
                                    <tr>
                                        <td>{{$final_orders[$i][0]['date']}}</td>
                                        <td>{{$final_orders[$i][0]['time']}}</td>
                                        <td>{{$final_orders[$i][0]['customer']}}</td>
                                        <td>{{$final_orders[$i][0]['address']}}</td>
                                        <td> {{$final_orders[$i][0]['code']}}</td>
                                        <td>{{$final_orders[$i][0]['floor']}}</td>
                                        <td>{{$final_orders[$i][0]['house_cnt']}}</td>
                                       <td> {{$final_orders[$i][0]['where_to_put']}}</td>
{{--                                        <td><a href="{{route('order-detail', $final_orders[$i][0]['order_id'])}}" class="btn--}}
{{--                                        btn-info--}}
{{--                                        btn-ft">View</a>--}}
{{--                                        </td>--}}
                                    </tr>
                                    @endfor
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

            $("#txtfuturedate").change(function () {
                    $("#filter_form").submit();
            });
        });

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
