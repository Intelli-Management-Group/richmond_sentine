@extends('common.header')
@extends('common.nav')
@section('content')
    <ol class="breadcrumb breadcrumb-quirk">
        <li><a href="{!! url('dashboard') !!}"><i class="fa fa-home mr5"></i> Home</a></li>
        <li class="active">Unapproved Business List</li>
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
            <div class="panel business_container">
                <div class="panel-heading browsebtn-sec">
                    <h2>Unapproved Business List</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" id="business_list_table">
                        <table class="table table-bordered table-striped-col data_table_list" data-url="{{ url('unapproved-business-list') }}" data-page-size="{{ config('constants.pagination_limit') }}" data-ordering="true">
                            <thead>
                            <tr>
                                <th data-column="logo" data-order="false">Logo</th>
                                <th data-column="name" data-order="true">Name</th>
                                <th data-column="address" data-order="false">Address</th>
                                <th data-column="phone" data-order="true">Phone</th>
                                <th data-column="email" data-order="false">Email</th>
                                <th data-column="website" data-order="false">Website</th>
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
        function deleteBusiness(id) {
            if (id == "") {
                alert("Business not found");
                return false;
            } else {
                if (confirm("Are you sure want to delete business account?")) {
                    $.ajax({
                        type: 'POST',
                        url: '{!! url("delete-unapproved-business") !!}',
                        data: {
                            'business_id': id
                        },
                        success: function (data) {
                            if (data == "success") {
                                $(".business_list_table").before('<div class="alert alert-success" id="successMessage">Business deleted successfully</div>');
                                $("#successMessage").delay(5000).slideUp(300);
                                $('.data_table_list').DataTable().draw();
                            }else{
                                $(".business_list_table").before('<div class="alert alert-danger" id="errorMessage">Something went wrong</div>');
                                $("#errorMessage").delay(5000).slideUp(300);
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
