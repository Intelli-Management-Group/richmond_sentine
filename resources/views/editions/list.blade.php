@extends('common.header')

@extends('common.nav')

@section('content')
<ol class="breadcrumb breadcrumb-quirk">
    <li><a href="{!! url('dashboard') !!}"><i class="fa fa-home mr5"></i> Home</a></li>
    <li class="active">Edition List</li>
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
        <div class="panel edition_container">
            <div class="panel-heading browsebtn-sec">
                <h2>Edition List</h2>
                <div class="browsebtn">
                    <a href="{!! url('add-edition') !!}">Add edition</a>
                </div>
                <div class="browsebtn mr5">
                    <a class="a_danger" href="javascript:void(0);" onclick="javascript:deleteSelectedEdition();">Delete editions</a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped-col data_table_list" data-url="{{ url('edition-list') }}" data-page-size="{{ config('constants.pagination_limit') }}" data-ordering="true">
                        <thead>
                            <tr>
                                <th data-column="checkbox" data-order="false"><input type="checkbox" id="delete_checkall" /></th>
                                <th data-column="name" data-order="false">Name</th>
                                <th data-column="volume" data-order="false">Volume</th>
                                <th data-column="edition_number" data-order="false">Edition</th>
                                <th data-column="edition_date" data-order="false">Date</th>
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
<link rel="stylesheet" href="{{ asset('css/edition.css').'?t='.Carbon\Carbon::now()->timestamp }}">
<script type="text/javascript">
    $(document).ready(function() {
        applyDataTable(true);
        $("#successMessage,#errorMessage").delay(5000).slideUp(300);
    });

    function deleteEdition(edition_id) {
        if (edition_id != "") {
            if (confirm("Are you sure want to delete edition?")) {
                window.location = '{!! url("delete-edition") !!}' + "/" + edition_id;
            }
        } else {
            alert("Edition id not found");
        }
    }

    function deleteSelectedEdition() {
        var edition_ids = "";
        var checkedVals = $(".delete_entity:checkbox:checked").map(function() {
            return this.value;
        }).get();
        edition_ids = checkedVals.join(",");
        if (edition_ids == "") {
            alert("Please select edition(s)");
            return false;
        } else {
            if (confirm("Are you sure want to delete edition(s)?")) {
                $.ajax({
                    type: 'POST',
                    url: '{!! url("delete-all-editions") !!}',
                    data: {
                        'edition_ids': edition_ids
                    },
                    success: function(data) {
                        if (data == "success") {
                            $(".edition_container").before('<div class="alert alert-success" id="successMessage">Edition(s) deleted successfully</div>');
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