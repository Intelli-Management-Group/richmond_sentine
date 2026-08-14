$(document).ready(function () {
    $("#successMessage,#errorMessage").delay(5000).slideUp(300);
    $(document).on("change", "#delete_checkall", function () {
        if ($(this).is(":checked")) {
            $(".delete_entity").prop("checked", true);
        } else {
            $(".delete_entity").prop("checked", false);
        }
    });
    $(document).on("change", ".delete_entity", function () {
        if ($(".delete_entity:checkbox:checked").length == $(".delete_entity").length) {
            $("#delete_checkall").prop("checked", true);
        } else {
            $("#delete_checkall").prop("checked", false);
        }
    });
    $(document).mouseup(function (e) {
        var container = $("#notification_list_outer");
        var bell_icon = $(".bell-icon i");
        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0 && !bell_icon.is(e.target) && bell_icon.has(e.target).length === 0) {
            container.fadeOut();
        }
    });
    $(document).on("click",".bell-icon i",function () {
        $("#notification_list_outer").fadeToggle();
    });
});

function applyDataTable(length) {
    var column_array = new Array();
    $('.data_table_list').find('thead tr th').each(function () {
        column_array.push({
            "data": $(this).data("column"),
            "orderable": $(this).data("order")
        });
    });

    var redirect_url = $('.data_table_list').data("url");
    var status = $('select[name=sort]').val();
    var table_ordering = $('.data_table_list').data("ordering");
    var page_length = $(".data_table_list").data("page-size");
    var is_display_length = (length == undefined) ? false : true;

    $('.data_table_list').DataTable({
        columnDefs: [{
            "targets": -1,
            "data": null,
        }],
        "lengthChange": is_display_length,
        "processing": true,
        "serverSide": true,
        "lengthMenu": [
            [10, 25, 50, 200],
            [10, 25, 50, 200]
        ],
        "ajax": {
            "type": "POST",
            "url": redirect_url,
            "data": {
                status: status
            }
        },
        "columns": column_array,
        "paging": true,
        "ordering": table_ordering,
        "order": [],
        "pageLength": page_length,
        "pagingType": "full"
    });

}
