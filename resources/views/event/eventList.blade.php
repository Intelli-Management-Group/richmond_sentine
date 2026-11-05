@extends('common.header')
@extends('common.nav')
@section('content')
    <ol class="breadcrumb breadcrumb-quirk">
        <li><a href="{!! url('dashboard') !!}"><i class="fa fa-home mr5"></i> Home</a></li>
        <li class="active">Event List</li>
    </ol>
    <div class="row">
        <div class="col-md-12">
            @if(session()->has('success'))
                <div class="alert alert-success" id="successMessage">
                    {!! session('success') !!}
                </div>
            @endif @if(session()->has('error'))
                <div class="alert alert-danger" id="errorMessage">
                    {!! session('error') !!}
                </div>
            @endif
            <div class="panel event_container">
                <div class="panel-heading browsebtn-sec">
                    <h2>Event List</h2>
                    <div class="browsebtn">
                        <a href="{!! url('add-event') !!}">Add Event</a>
                    </div>
                    <div class="browsebtn mr5">
                        <a class="a_danger" href="javascript:void(0);"  onclick="javascript:deleteEvent();">Delete events</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" id="event_list_table">
                        <table class="table table-bordered table-striped-col data_table_list" data-url="{{ url('event-list') }}" data-page-size="{{ config('constants.pagination_limit') }}" data-ordering="true">
                            <thead>
                            <tr>
                                <th data-column="checkbox" data-order="false"><input type="checkbox" id="delete_checkall" /></th>
                                <th data-column="name" data-order="true">Name</th>
                                <th data-column="date" data-order="true">Date/Time</th>
                                <th data-column="category" data-order="false">Category</th>
                                <th data-column="venue" data-order="false">Venue</th>
                                <th data-column="bannerImage" data-order="false">Banner</th>
                                <th data-column="action" data-order="false">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @extends('common.footer')
@section('jquerysection')
    <link rel="stylesheet" href="{{ asset('css/business.css').'?t='.Carbon\Carbon::now()->timestamp }}">
    <script type="text/javascript">
        $(document).ready(function() {
            applyDataTable(true);
        });
        function deleteEvent(event_id) {
            var checkedVals = $(".delete_entity:checkbox:checked").map(function () {
                return this.value;
            }).get();
            if (event_id == undefined) {
                event_id = checkedVals.join(",");
            }
            if (event_id == "") {
                alert("Please select event(s)");
                return false;
            } else {
                if (confirm("Are you sure want to delete event(s)?")) {
                    $.ajax({
                        type: 'POST',
                        url: '{!! url("delete-event") !!}',
                        data: {
                            'event_ids': event_id
                        },
                        success: function (data) {
                            if (data == "success") {
                                $(".event_container").before('<div class="alert alert-success deleteMessage">Event(s) deleted successfully</div>');
                                $(".deleteMessage").delay(5000).slideUp(300);
                                $('.data_table_list').DataTable().draw();
                            }
                        }
                    });
                }
            }
        }
    </script>
@endsection
@endsection
