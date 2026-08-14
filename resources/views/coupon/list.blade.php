@extends('common.header')
@extends('common.nav')
@section('content')
<ol class="breadcrumb breadcrumb-quirk">
    <li><a href="{!! url('dashboard') !!}"><i class="fa fa-home mr5"></i> Home</a></li>
    <li class="active">Coupon List</li>
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
        <div class="panel coupon_container">
            <div class="panel-heading browsebtn-sec">
                <h2>Coupon List</h2>
                <div class="browsebtn">
                    <a href="{!! url('add-coupon') !!}">Add coupon</a>
                </div>
                <div class="browsebtn mr5">
                    <a class="a_danger" href="javascript:void(0);" onclick="javascript:deleteSelectedCoupon();">Delete Coupons</a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped-col data_table_list" data-url="{{ url('coupon-list') }}" data-page-size="{{ config('constants.pagination_limit') }}" data-ordering="true">
                        <thead>
                            <tr>
                                <th data-column="checkbox" data-order="false"><input type="checkbox" id="delete_checkall" /></th>
                                <th data-column="name" data-order="true">Name</th>
                                <th data-column="heading" data-order="true" width="20%">Heading</th>
                                <th data-column="category" data-order="false">Category</th>
                                <th data-column="company_name" data-order="true">Company Name</th>
                                <th data-column="image" data-order="false">Image</th>
                                <th data-column="publish_date" data-order="true">Publish Date</th>
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
<link rel="stylesheet" href="{{ asset('css/couponzone.css').'?t='.Carbon\Carbon::now()->timestamp }}">
<script type="text/javascript">
    $(document).ready(function() {
        applyDataTable(true);
    });

    function deleteCoupon(coupon_id) {
        if (coupon_id != "") {
            if (confirm("Are you sure want to delete coupon?")) {
                window.location = '{!! url("delete-coupon") !!}' + "/" + coupon_id;
            }
        } else {
            alert("Coupon id not found");
        }
    }

    function deleteSelectedCoupon() {
        var coupon_ids = "";
        var checkedVals = $(".delete_entity:checkbox:checked").map(function() {
            return this.value;
        }).get();
        coupon_ids = checkedVals.join(",");
        if (coupon_ids == "") {
            alert("Please select coupon(s)");
            return false;
        } else {
            if (confirm("Are you sure want to delete coupon(s)?")) {
                $.ajax({
                    type: 'POST',
                    url: '{!! url("delete-all-coupons") !!}',
                    data: {
                        'coupon_ids': coupon_ids
                    },
                    success: function(data) {
                        if (data == "success") {
                            $(".coupon_container").before('<div class="alert alert-success" id="successMessage">Coupon(s) deleted successfully</div>');
                            $("#successMessage").delay(5000).slideUp(300);
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