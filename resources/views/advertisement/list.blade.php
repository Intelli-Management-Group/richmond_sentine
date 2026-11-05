@extends('common.header')

@extends('common.nav')

@section('content')
<ol class="breadcrumb breadcrumb-quirk">
    <li><a href="{!! url('dashboard') !!}"><i class="fa fa-home mr5"></i> Home</a></li>
    <li class="active">Advertisement List</li>
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
        <div class="panel advertisement_container">
            <div class="panel-heading browsebtn-sec">
                <h2>Advertisement List</h2>
                <div class="browsebtn">
                    <a href="{!! url('add-advertisement') !!}">Add Advertisement</a>
                </div>
                <div class="browsebtn mr5" style="width:180px;">
                    <a class="a_danger" href="javascript:void(0);" onclick="javascript:deleteSelectedAdvertisement();">Delete Advertisements</a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped-col data_table_list" data-url="{{ url('advertisement-list') }}" data-page-size="{{ config('constants.pagination_limit') }}" data-ordering="true">
                        <thead>
                            <tr>
                                <th data-column="checkbox" data-order="false"><input type="checkbox" id="delete_checkall" /></th>
                                <th data-column="name" data-order="false">Name</th>
                                <th data-column="link" data-order="false">Link</th>
                                <th data-column="type" data-order="false">Type</th>
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
<link rel="stylesheet" href="{{ asset('css/advertisement.css').'?t='.Carbon\Carbon::now()->timestamp }}">
<script type="text/javascript">
    $(document).ready(function() {
        applyDataTable(true);
    });

    function deleteAdvertisement(advertisement_id) {
        if (advertisement_id != "") {
            if (confirm("Are you sure want to delete advertisement?")) {
                window.location = '{!! url("delete-advertisement") !!}' + "/" + advertisement_id;
            }
        } else {
            alert("Advertisement id not found");
        }
    }

    function deleteSelectedAdvertisement() {
        var ad_ids = "";
        var checkedVals = $(".delete_entity:checkbox:checked").map(function() {
            return this.value;
        }).get();
        ad_ids = checkedVals.join(",");
        if (ad_ids == "") {
            alert("Please select advertisement(s)");
            return false;
        } else {
            if (confirm("Are you sure want to delete advertisement(s)?")) {
                $.ajax({
                    type: 'POST',
                    url: '{!! url("delete-all-advertisements") !!}',
                    data: {
                        'ad_ids': ad_ids
                    },
                    success: function(data) {
                        if (data == "success") {
                            $(".advertisement_container").before('<div class="alert alert-success" id="successMessage">Advertisement(s) deleted successfully</div>');
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