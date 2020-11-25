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
                <div class="col p-md-0">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">
                            <a href=""></a>
                        </li>
                    </ol>
                </div>
                <div class="col p-md-0 d-flex">
                    <li style="padding: 15px;">
                        <label>Date</label>
                        <select  tabindex="-98" id="time-range">
                            <option>Select a date</option>
                            <option>Today</option>
                            <option>Tomorrow</option>
                            <option>Next Week</option>
                        </select>
                    </li>
                    <li style="padding: 15px;">
                        <label>Area</label>
                        <select  tabindex="-98" id="time-range">
                            @foreach($areas as $area)
                                <option>{{$area->area_name}}</option>
                            @endforeach
                        </select>
                    </li>
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
                                        <th>Time</th>
                                        <th>Customer</th>
                                        <th>Address</th>
                                        <th>Number of House</th>
                                        <th>Floor</th>
                                        <th>Code</th>
                                        <th>Area</th>
                                        <th>Detail</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($no=0)
                                    @for($i=0;$i<count($final_orders);$i++)
                                    <tr>
                                        <td>{{$final_orders[$i][0]['time']}}</td>
                                        <td>{{$final_orders[$i][0]['customer']}}</td>
                                        <td>{{$final_orders[$i][0]['address']}}</td>
                                        <td>{{$final_orders[$i][0]['house_cnt']}}</td>
                                        <td>{{$final_orders[$i][0]['floor']}}</td>
                                        <td> {{$final_orders[$i][0]['code']}}</td>
                                        <td> {{$final_orders[$i][0]['area']}}</td>
                                        <td><a href="{{route('order-detail', $final_orders[$i][0]['order_id'])}}" class="btn
                                        btn-info
                                        btn-ft">View</a>
                                        </td>
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
    <script>
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