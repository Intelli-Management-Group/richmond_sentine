@extends('common.header')
@extends('common.nav')
@section('content')
<ol class="breadcrumb breadcrumb-quirk">
    <li><a href="{!! url('dashboard') !!}"><i class="fa fa-home mr5"></i> Home</a></li>
    <li class="active">Article List</li>
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
        <div class="panel article_container">

            <div class="panel-heading browsebtn-sec">
                <h2>Article List</h2>
                <div class="browsebtn">
                    <a href="{!! url('add-article/0') !!}">Add Article</a>
                </div>
                <div class="browsebtn mr5">
                    <a href="{!! url('add-article/1') !!}">Add Video</a>
                </div>
                <div class="browsebtn mr5">
                    <a class="a_danger" href="javascript:void(0);" onclick="javascript:deleteSelectedArticle();">Delete Articles</a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped-col data_table_list" data-url="{{ url('article-list') }}" data-page-size="{{ config('constants.pagination_limit') }}" data-ordering="true">
                        <thead>
                            <tr>
                                <th data-column="checkbox" data-order="false"><input type="checkbox" id="delete_checkall" /></th>
                                <th data-column="heading" data-order="true" width="25%">Heading</th>
                                <th data-column="category" data-order="true">Category</th>
                                <th data-column="image" data-order="false">Image</th>
                                <th data-column="publish_date" data-order="true">Publish Date</th>
                                <th data-column="view_count" data-order="true">View count</th>
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
<link rel="stylesheet" href="{{ asset('css/article.css').'?t='.Carbon\Carbon::now()->timestamp }}">
<script type="text/javascript">
    $(document).ready(function() {
        applyDataTable(true);
    });

    function deleteArticle(article_id) {
        if (article_id != "") {
            if (confirm("Are you sure want to delete article?")) {
                window.location = '{!! url("delete-article") !!}' + "/" + article_id;
            }
        } else {
            alert("article id not found");
        }
    }

    function deleteSelectedArticle() {
        var article_ids = "";
        var checkedVals = $(".delete_entity:checkbox:checked").map(function() {
            return this.value;
        }).get();
        article_ids = checkedVals.join(",");
        if (article_ids == "") {
            alert("Please select article(s)");
            return false;
        } else {
            if (confirm("Are you sure want to delete article(s)?")) {
                $.ajax({
                    type: 'POST',
                    url: '{!! url("delete-all-articles") !!}',
                    data: {
                        'article_ids': article_ids
                    },
                    success: function(data) {
                        if (data == "success") {
                            $(".article_container").before('<div class="alert alert-success" id="successMessage">Article(s) deleted successfully</div>');
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