@extends('common.header')
@extends('common.nav')
@section('content')
<ol class="breadcrumb breadcrumb-quirk">
    <li><a href="<?php url('admin/home'); ?>"><i class="fa fa-home mr5"></i> Home</a></li>
    <li class="active">Author List </li>
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
        <div class="panel author_outer_container">
            <div class="panel-heading browsebtn-sec">
                <h2>Author List</h2>
                <div class="browsebtn ">
                    <a href="{{ url('add-author') }}">Add Author</a>
                </div>
                <div class="browsebtn mr5">
                    <a class="a_danger" href="javascript:void(0);" onclick="javascript:deleteSelectedAuthor();">Delete Authors</a>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="panel-body author_container">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped-col data_table_list" data-url="{{ url('author-list') }}" data-page-size="{{ config('constants.pagination_limit') }}" data-ordering="true">
                        <thead>
                            <tr>
                                <th data-column="checkbox" data-order="false"><input type="checkbox" id="delete_checkall" /></th>
                                <th data-column="name" data-order="false">Name</th>
                                <th data-column="email" data-order="false">E-mail</th>
                                <th data-column="twitterHandle" data-order="false">Twitter Handle</th>
                                <th data-column="facebookHandle" data-order="false">Facebook Handle</th>
                                <th data-column="profileImage" data-order="false">Profile Image</th>
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
<link rel="stylesheet" href="{{ asset('ui/css/author-info.css').'?t='.Carbon\Carbon::now()->timestamp }}">
<script type="text/javascript">
    $(document).ready(function() {
        applyDataTable(length);
    });

    function deleteAuthor(author_id) {
        if (author_id != "") {
            if (confirm("Are you sure want to delete ?")) {
                window.location = '{!! url("delete-author") !!}' + "/" + author_id;
            }
        } else {
            alert("author id not found");
        }
    }

    function deleteSelectedAuthor() {
        var author_ids = "";
        var checkedVals = $(".delete_entity:checkbox:checked").map(function() {
            return this.value;
        }).get();
        author_ids = checkedVals.join(",");
        if (author_ids == "") {
            alert("Please select author(s)");
            return false;
        } else {
            if (confirm("Are you sure want to delete author(s)?")) {
                $.ajax({
                    type: 'POST',
                    url: '{!! url("delete-all-authors") !!}',
                    data: {
                        'author_ids': author_ids
                    },
                    success: function(data) {
                        if (data == "success") {
                            $(".author_container").before('<div class="alert alert-success" id="successMessage">Author(s) deleted successfully</div>');
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