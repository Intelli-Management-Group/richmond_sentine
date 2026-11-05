var event_image_width = 825;
var event_image_height = 413;
var banner_crop_tool = "",  bannerImage = "";
$(document).ready(function () {
    $("#event_description").summernote({
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
    $("#eventDate").datepicker({
        format: "yyyy-mm-dd",
        startDate: new Date()
    }).on('changeDate', function (e) {
        $(this).datepicker('hide');
    });
    $("#price_selection").change(function () {
        if ($(this).val() == "price") {
            $("#cost_outer").removeClass("hidden");
        } else {
            $("#cost_outer").addClass("hidden");
            $("#cost_outer").find("input").val("");
        }
    });
    $("#price_selection").change();
    $("#bannerImage").change(function (e) {
        bannerImage = "";
        $("#bannerImageError").text("");
        var _URL = window.URL || window.webkitURL;
        var file, img;
        var fileUpload = $(this);
        if ((file = this.files[0])) {
            img = new Image();
            img.onload = function () {
                var img_data = this;
                if (img_data.width < event_image_width || img_data.height < event_image_height) {
                    $("#bannerImageError").text("Please upload image having width " + event_image_width + "px and height " + event_image_height + "px.");
                    $(fileUpload).val("");
                } else if (img_data.width > event_image_width || img_data.height > event_image_height) {
                    $("#selected_file").attr("src", _URL.createObjectURL(file));
                    $("#fileCropModal").modal('show');
                    setTimeout(function () {
                        banner_crop_tool = $('#upload-demo').croppie({
                            enableExif: true,
                            enableOrientation: true,
                            showZoomer: true,
                            enableResize: false,
                            viewport: {
                                width: event_image_width,
                                height: event_image_height
                            },
                            boundary: {
                                width: event_image_width,
                                height: event_image_height
                            }
                        });
                        setTimeout(function () {
                            banner_crop_tool.croppie('bind', {url: _URL.createObjectURL(file)});
                        }, 100)
                    }, 100)

                } else {
                    $("#bannerThumbnail").attr("src", _URL.createObjectURL(file));
                    bannerImage = file;
                }
            };
            img.src = _URL.createObjectURL(file);
            img.file = file;
        }
    });
    $("#fileCropModal").on("hide.bs.modal", function () {
        banner_crop_tool.croppie("destroy");
        $("#bannerImage").val();
    });
});

function saveEvent() {
    var data = new FormData();
    var formdata = $("#event_form").serializeArray();
    for (var f in formdata) {
        data.append(formdata[f].name, formdata[f].value);
    }
    data.append("bannerImage", bannerImage);
    $.ajax({
        url: $("#event_form").attr("action"),
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        beforeSend: function (data) {
            $("#save_event").text($("input[name='event_id']").val() == "" ? "Saving.." : "Updating..");
        },
        success: function (data) {
            $(".error").text("");
            if (data.errors != undefined && Object.keys(data.errors).length > 0) {
                $("#save_event").text($("input[name='event_id']").val() == "" ? "Save" : "Update");
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

function saveCroppedImage() {
    var _URL = window.URL || window.webkitURL;
    banner_crop_tool.croppie('result', 'blob').then(function (blob) {
        $("#bannerThumbnail").attr("src", _URL.createObjectURL(blob));
        bannerImage = blob;
        $("#fileCropModal").modal('hide');
    });
}




