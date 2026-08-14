@extends('frontend.layout')
@section('content')
    <div class="container">
        <div class="row mx-0">
            <h1 class="page-title"><span>Store directory</span></h1>
        </div>
        <div class="row mt-5">
            <div class="col-lg-11 mb-2">
                <input type="text" class="form-control" required placeholder="Search by store name" id="search_store"
                       autocomplete="off"/>
            </div>
            <div class="col-lg-1 text-right">
                <button class="btn btn-primary" type="button" onclick="javascript:searchBusinessData()">Apply</button>
            </div>

        </div>
        <div class="row mt-5">
            <div class="col-lg-12" id="tab_outer">
                {!! $all_listing !!}
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.1.12.css') }}">
    <script src="{{ asset('js/jquery-ui.1.12.js') }}" defer></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".single-alphabet a").off("click");
            $(".single-alphabet a").on("click", function () {
                var target = $(this).data("scroll");
                $('html,body').animate({
                    scrollTop: $("#alphabetical_business").find(target).offset().top
                }, 1000);
            });
            $("#search_store").autocomplete({
                source: '{!! url("/business-names") !!}',
                minLength: 1,
                select: function (event, ui) {
                    if (ui.item.shortCode != "") {
                        window.location = "{!! url("stores") !!}" + "/" + ui.item.shortCode;
                    }
                }
            }).data("ui-autocomplete")._renderItem = function (ul, item) {
                return $("<li class='ui-autocomplete-row'></li>")
                    .data("item.autocomplete", item)
                    .append(item.label)
                    .appendTo(ul);
            };
        })

        function searchBusinessData() {
            var search_string = $.trim($("#search_store").val());
            $("#tab_outer").html("")
            $.ajax({
                method: "post",
                url: '{!! url("/search-business") !!}',
                data: {
                    search_string: search_string
                },
                success: function (data) {
                    $("#tab_outer").html(data);
                }
            });
        }
    </script>
@endsection
