@extends("layouts.backend")
@section("content")
    <div class="content-body">
        <div class="container-fluid">
            <div class="row page-titles">
                <div class="col p-md-0">
                    <h4>Time Ranges</h4>
                </div>
                <div class="col p-md-0">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal"
                               data-whatever="@fat"><span class="btn-icon-left text-info">
                                    <i class="fa fa-plus color-info"></i> </span> Add Time Range</a>
                            {{--<a href="{{route('add-areas')}}"><span class="btn-icon-left text-info">--}}
                                    {{--<i class="fa fa-plus color-info"></i> </span> Add Areas</a>--}}
                        </li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h4 class="card-title">Time Ranges</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table header-border" style="min-width: 500px;">
                                    <thead>
                                    <tr>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Ordering</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($times as $time)
                                    <tr>
                                        <td>{{$time->from}}</td>
                                        <td>{{$time->to}}</td>
                                        <td>{{$time->order}}</td>
                                        <td><a href="javascript:void(0)" onclick="updateTime({{$time->id}})"
                                               class="btn btn-ft btn-rounded btn-outline-info">
                                            Edit</a>
                                        </td>
                                        <td><a href="{{route('delete-time', $time->id)}}" class="btn btn-ft btn-rounded btn-outline-info">
                                            Delete</a>
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
    {{--add aread modal--}}
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="{{route('add-time')}}" method="post">
                        @csrf
                        <h2>Time Range</h2>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">From</label>
                            <input type="text" required class="form-control" name="from">
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">To</label>
                            <input type="text" required class="form-control" name="to">
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Ordering</label>
                            <input type="text" required class="form-control" value="{{ \App\TimeRange::count()+1 }}" name="order">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">ADD</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    {{--update arear modal--}}
    <div class="area-update"></div>
    <script>
        function updateTime(id) {

            $.post("{{route('get-time')}}",{
                _token:'{{csrf_token()}}',
                id: id,
            }).done(
                function (data) {
                    var area =JSON.parse(data);
                    console.log(area);
                    console.log(area[0]['id']);
                    var myvar = '<div class="modal fade" id="exampleModal-update" tabindex="-1" role="dialog"'+
                        '         aria-labelledby="exampleModalLabel" aria-hidden="true">'+
                        '        <div class="modal-dialog" role="document">'+
                        '            <div class="modal-content">'+
                        '                <div class="modal-body">'+
                        '                    <form action="time-ranges/update/'+area[0]['id']+'" method="post">'+
                        '                        @csrf'+
                        '                        <h2>Time Range</h2><div class="form-group">'+
                        '                            <label for="recipient-name" class="col-form-label">From</label>'+
                        '                            <input type="text" required value="'+area[0]['from']+'" class="form-control" name="from">'+
                        '                        </div>' +
                        '                        <div class="form-group">'+
                    '                            <label for="recipient-name" class="col-form-label">To</label>'+
                    '                            <input type="text" required value="'+area[0]['to']+'" class="form-control" name="to">'+
                    '                        </div>' +
                        '                       <div class="form-group">'+
                        '                            <label for="recipient-name" class="col-form-label">Ordering</label>'+
                        '                            <input type="text" required value="'+area[0]['order']+'" class="form-control" name="order">'+
                        '                        </div>'+
                        '                        <div class="modal-footer">'+
                        '                            <button type="submit" class="btn btn-primary">UPDATE</button>'+
                        '                            <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>'+
                        '                        </div>'+
                        '                    </form>'+
                        '                </div>'+
                        ''+
                        '            </div>'+
                        '        </div>'+
                        '    </div>';
                    $('.area-update').html(myvar);
                    $('#exampleModal-update').modal('show');
                }
            );
        }
    </script>
@endsection
