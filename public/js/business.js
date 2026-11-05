var business_image_width = 825;
var business_image_height = 413;
var logo_image_width = 384;
var logo_image_height = 280;
var selected_files_array = {};
var existing_id_removed = new Array();
var logoImage = "";
var crop_tool = "", logo_crop_tool = "";
$(document).ready(function () {
    $('.clockpicker').clockpicker({
        autoclose: 'true'
    });
    $("#business_category").off("change");
    $("#business_category").on("change", function () {
        $(".subcategory,#otherCategoryName").addClass("hidden");
        var clone_select = $('#subcategoryClone').clone();
        $('.subcategory').find("option:not(:first-child)").remove();
        $('.subcategory').val("");
        if ($(this).val() != "") {
            if ($(this).val() == "other") {
                $("#otherCategoryName").removeClass("hidden");
            } else {
                $('.subcategory').append($(clone_select).find('#subCat-' + $(this).val()).find("option"));
                $(".subcategory").removeClass("hidden");
            }
        }
    });
    $("#business_description").summernote({
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['codeview', 'help', 'undo', 'redo']],
        ],
        minHeight: 150,
        disableDragAndDrop: true
    });

    $("#business_photos").unbind("change");
    $("#business_photos").change(function (e) {
        var img;
        var selected_file = this.files[0];
        $("#mediaFile_error").text("");
        if (selected_file != undefined) {
            var _URL = window.URL || window.webkitURL;
            img = new Image();
            img.onload = function () {
                var img_data = this;
                if (img_data.width < business_image_width || img_data.height < business_image_height) {
                    $("#mediaFile_error").text("Please upload image having width " + business_image_width + "px and height " + business_image_height + "px.");
                } else if (img_data.width > business_image_width || img_data.height > business_image_height) {
                    $("#fileCropModal").modal('show');
                    setTimeout(function () {
                        crop_tool = $('#upload-demo').croppie({
                            enableExif: true,
                            enableOrientation: true,
                            showZoomer: true,
                            enableResize: false,
                            viewport: {
                                width: business_image_width,
                                height: business_image_height
                            },
                            boundary: {
                                width: business_image_width,
                                height: business_image_height
                            }
                        });
                        setTimeout(function () {
                            crop_tool.croppie('bind', {url: _URL.createObjectURL(selected_file)});
                        }, 100)
                    }, 100)
                } else {
                    createPreview(this, 0);
                }
            }
            img.src = _URL.createObjectURL(selected_file);
            img.file = selected_file;
        }
    });
    $("#fileCropModal").on("hide.bs.modal", function () {
        crop_tool.croppie("destroy");
        $("#business_photos").val();
    });
    $("#logoCropModal").on("hide.bs.modal", function () {
        logo_crop_tool.croppie("destroy");
        $("#logoImage").val("");
    });
    $(document).on("change", ".change_time", function () {
        if ($(this).is(":checked")) {
            addTimeselectionDiv($(this));
        } else {
            $(this).parents(".day_outer").find(".time_selection").remove();
            $(this).parents(".day_outer").find(".opening_label").text("Closed");
        }
    });
    $("#hour_error").hide();
    $("#isOpenAll").change(function () {
        $("#startTimeAll,#endTimeAll").val("");
        if ($(this).is(":checked")) {
            $("#timepicker_container").removeClass("hide");
            $(this).parent().next().text("Open");
        } else {
            $("#timepicker_container").addClass("hide");
            $(this).parent().next().text("Closed");
        }
    });
    $("#business_name").off("blur");
    $("#business_name").on("blur", function () {
        var name = $.trim($(this).val());
        if (name != "") {
            $("#shortCode").val(name.replace(/\s+/g, '-').toLowerCase());
        }
    });
    $("#apply_to_all_days").on("change", function () {
        $("#hour_error").hide();
        if ($(this).is(':checked')) {
            $("#apply_to_all_days_outer").removeClass("hide");
        } else {
            $("#isOpenAll").removeAttr("checked").change();
            $("#apply_to_all_days_outer").addClass("hide");
        }
    });
});

$("#logoImage").change(function (e) {
    logoImage = "";
    $("#logoError").text("");
    var _URL = window.URL || window.webkitURL;
    var file, img;
    var fileUpload = $(this);
    if ((file = this.files[0])) {
        img = new Image();
        img.onload = function () {
            var img_data = this;
            if (img_data.width < logo_image_width || img_data.height < logo_image_height) {
                $("#logoError").text("Please upload image having width " + logo_image_width + "px and height " + logo_image_height + "px.");
                $(fileUpload).val("");
            } else if (img_data.width > logo_image_width || img_data.height > logo_image_height) {
                $("#logoCropModal").modal('show');
                setTimeout(function () {
                    logo_crop_tool = $('#upload-logo-demo').croppie({
                        enableExif: true,
                        enableOrientation: true,
                        showZoomer: true,
                        enableResize: true,
                        enableOrientation: true,
                        viewport: {
                            width: logo_image_width,
                            height: logo_image_height
                        },
                        boundary: {
                            width: logo_image_width,
                            height: logo_image_height
                        }
                    });
                    setTimeout(function () {
                        logo_crop_tool.croppie('bind', {url: _URL.createObjectURL(file)});
                    }, 100)

                }, 100)
            } else {
                $("#logoThumbnail").attr("src", _URL.createObjectURL(file));
                logoImage = file;
            }
        };
        img.src = _URL.createObjectURL(file);
        img.file = file;
    }
});

function createPreview(img, is_blob) {
    var _URL = window.URL || window.webkitURL;
    var div = $("#file_preview_clone").clone();
    var counter = getCounter();
    var id = "div_" + counter;
    div.removeClass("hidden").removeClass("col-sm-2").addClass("col-sm-3");
    div.removeAttr("id");
    div.find(".file-preview-image").attr("src", (is_blob ? _URL.createObjectURL(img) : img.src));
    div.attr("id", id);
    div.find(".video_element").remove();
    div.find(".remove-file").attr("onclick", "javascript:deleteFile('" + id + "')");
    $("#business_media_container").append(div);
    selected_files_array[counter] = (is_blob ? img : img.file);
}

function saveCroppedImage() {
    crop_tool.croppie('result', 'blob').then(function (blob) {
        createPreview(blob, 1);
        $("#fileCropModal").modal('hide');
    });
}

function saveCroppedLogo() {
    var _URL = window.URL || window.webkitURL;
    logo_crop_tool.croppie('result', 'blob').then(function (blob) {
        $("#logoThumbnail").attr("src", _URL.createObjectURL(blob));
        logoImage = blob;
        $("#logoCropModal").modal('hide');
    });
}

function getCounter() {
    var counter = 0;
    if ($("#business_media_container .file-preview").length > 0) {
        counter = parseInt($("#business_media_container .file-preview:last").attr("id").split("_")[1]);
    }
    counter = counter + 1;
    return counter;
}

function saveBusiness() {
    var data = new FormData();
    var formdata = $("#business_form").serializeArray();
    for (var f in formdata) {
        data.append(formdata[f].name, formdata[f].value);
    }
    data.append("logo", logoImage);
    for (var s in selected_files_array) {
        data.append("allMediaFiles[" + s + "]", selected_files_array[s]);
    }
    data.append("deleted_ids", existing_id_removed);
    $.ajax({
        url: $("#business_form").attr("action"),
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        beforeSend: function (data) {
            $("#save_business").val($("input[name='business_id']").val() == "" ? "Saving.." : "Updating..");
        },
        success: function (data) {
            $(".error").text("");
            if (data.errors != undefined && Object.keys(data.errors).length > 0) {
                $("#save_business").val($("input[name='business_id']").val() == "" ? "Save" : "Update");
            }
            for (var e in data.errors) {
                $(".error[for=" + e + "]").text(data.errors[e]);
            }
            if (data.list != undefined) {
                window.location.href = data.list;
            }
        }
    });
}

function deleteFile(div_id) {
    var file_index = div_id.split("_")[1];
    if (file_index != undefined) {
        if (selected_files_array[file_index] != undefined) {
            delete selected_files_array[file_index];
        } else {
            var existing_id = $("#" + div_id).find(".remove-file").data("id");
            if (existing_id != undefined) {
                existing_id_removed.push(existing_id);
            }
        }
        $("#" + div_id).remove();
    }
}

function addTimeselectionDiv(div) {
    var timeselection = $("#time_selection").clone();
    var dataDay = $(div).data("day");
    timeselection.removeClass("hidden");
    timeselection.removeAttr("id");
    timeselection.find(".start_input_time").attr("name", "starttime[" + dataDay + "][]");
    timeselection.find(".end_input_time").attr("name", "endtime[" + dataDay + "][]");
    timeselection.find("a:first").attr("data-day", dataDay);
    $(div).parents(".day_outer").append(timeselection);
    $(div).parents(".day_outer").find(".opening_label").text("Open");
    $(timeselection).find('.clockpicker').clockpicker({
        autoclose: 'true'
    });
}

function applyTimeToAll() {
    $("#hour_error").hide();
    if ($("#isOpenAll").is(":checked")) {
        if ($("#startTimeAll").val() == "" || $("#endTimeAll").val() == "") {
            $("#hour_error").show();
        } else {
            $(".day_outer").each(function () {
                if (!$(this).find(".change_time").is(":checked")) {
                    $(this).find(".change_time").prop("checked", true).change();
                }
                $(this).find(".start_input_time").val($("#startTimeAll").val());
                $(this).find(".end_input_time").val($("#endTimeAll").val());
            });
        }

    } else {
        $(".day_outer").each(function () {
            if ($(this).find(".change_time").is(":checked")) {
                $(this).find(".change_time").removeAttr("checked").change();
            }
        });
    }
}

function removeTime(div) {
    if ($(div).parents(".day_outer").find(".time_selection").length == 1) {
        $(div).parents(".day_outer").find(".change_time").removeAttr("checked");
        $(div).parents(".day_outer").find(".opening_label").text("Closed");
    }
    $(div).parents(".time_selection").remove();
}

function getUpdateData(btn) {
    var data = new FormData();
    var formdata = $("#regiration_form").serializeArray();
    for (var f in formdata) {
        if (formdata[f].name == "about") {
            data.append(formdata[f].name, $('#business_description').summernote('isEmpty') ? '' : $('#business_description').summernote('code'));

        } else {
            data.append(formdata[f].name, formdata[f].value);
        }
    }
    data.append("logo", logoImage);
    for (var s in selected_files_array) {
        data.append("allMediaFiles[" + s + "]", selected_files_array[s]);
    }
    data.append("deleted_ids", existing_id_removed);
    data.append("_token", $('meta[name="csrf-token"]').attr('content'));
    data.append("current_step", $(btn).parent().parent().index());
    data.append("is_business_url", 1);
    return data;
}

function updateBusiness(btn_obj) {
    $("#successMessage,#errorMessage").addClass("hidden");
    $.ajax({
        url: $("#regiration_form").attr("action"),
        type: 'POST',
        data: getUpdateData(btn_obj),
        processData: false,
        contentType: false,
        beforeSend: function (data) {
            $(btn_obj).val("Saving..");
        },
        success: function (data) {
            $(".error").text("");
            for (var e in data.errors) {
                $(".error[for=" + e + "]").text(data.errors[e]);
            }
            if (data.list != undefined) {
                if (data.type == "success") {
                    $("#successMessage").html(data.msg);
                    $("#successMessage").removeClass("hidden").slideDown();
                } else {
                    $("#errorMessage").html(data.msg);
                    $("#errorMessage").removeClass("hidden").slideDown();
                }
                if (data.errors != undefined && Object.keys(data.errors).length > 0) {
                    $(btn_obj).val("Save");
                }
                setTimeout(function () {
                    window.location.href = data.list;
                }, 3000);
            } else {
                if (data.errors != undefined && Object.keys(data.errors).length > 0) {
                    $(btn_obj).val("Save");
                }
            }
        }
    });
}
