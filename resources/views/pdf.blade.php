<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title></title>
    <style type="text/css">
        body{
            font-family: firefly, DejaVu Sans, sans-serif;
        }
    </style>
</head>
<body>
<div style="text-align: center">
    <img src="{{url('')}}/logo.png" alt="" style="width: 120px">
    <h4 style="text-align: center">Date of Export: {{ $order_date ? $order_date : "All"}}</h4>
</div>
<table class="table header-border" style="width: 100%; border-spacing: 0px; text-align: center">
    <thead>
    <tr>
        <th style="font-weight: normal">#</th>
        <th style="font-weight: normal">Hour</th>
        <th style="font-weight: normal">Date</th>
        <th style="font-weight: normal">Client Name</th>
        <th style="font-weight: normal">Address</th>
        {{--<th style="font-weight: normal">Products</th>--}}
        <th style="font-weight: normal">packages</th>
    </tr>
    </thead>
    <tbody>
    @php
    $ttlQty = 0;
    @endphp
    @foreach($orders as $key => $order)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $order->time_range }}</td>
            <td>{{ $order->date }}</td>
            <td>{{ $order->client->name }}</td>
            <td>{{ $order->order_address->address }}</td>
            <td>
                <?php $packages = []; ?>
                @foreach($order->details as $detail)
                    <?php
                    if($detail->package){
                        $packages[$detail->package_id]['name'] = $detail->package->name;
                        $packages[$detail->package_id]['qty'] = $detail->package_qty;
                    }
                    ?>

                @endforeach
                @foreach($packages as $package )
                    @php
                        $ttlQty  = $ttlQty + $package['qty'];
                    @endphp
                    {{ $package['name'] }} - {{ $package['qty'] }}<br>
                @endforeach
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<table class="table header-border" style="width: 100%; border-spacing: 0px; text-align: center">
    <tbody>
    <tr>
        <td ><strong> </strong></td>
        <td><strong>Total Packages - {{@$ttlQty}}</strong></td>
    </tr>
    </tbody>
</table>
</body>
</html>
