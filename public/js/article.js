var selected_files_array = {};
var video_data = {};
var validImageTypes = new Array("image/gif", "image/jpeg", "image/jpg", "image/png");
var existing_id_removed = new Array();
$(document).ready(function () {
    $("#article_description").summernote({
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
    $(document).on('click', '.tree label', function (e) {
        $(this).next('ul').fadeToggle();
        e.stopPropagation();
    });
    $('.tree ul').fadeIn();
    var date = new Date();
    $("#expiryDate").datepicker({
        format: "yyyy-mm-dd",
        startDate: date
    }).on('changeDate', function (e) {
        $(this).datepicker('hide');
    });
    $("#mediaFile").unbind("change");
    $("#mediaFile").change(function (e) {
        $("#mediaFile_error").text("");
        var selected_files = this.files;
        var existing_files_count = Object.keys(selected_files_array).length;
        if ((existing_files_count + selected_files.length) > 5) {
            alert("Only 5 media files are allowed");
            return false;
        }
        var counter = 0;
        if ($("#media_container .file-preview").length > 0) {
            counter = parseInt($("#media_container .file-preview:last").attr("id").split("_")[1]);
        }
        counter = counter + 1;
        for (var s = 0; s < selected_files.length; s++) {
            var file, img;
            var _URL = window.URL || window.webkitURL;
            file = selected_files[s];

            if ($.inArray(file.type, validImageTypes) > 0) {
                img = new Image();
                img.onload = function () {
                    var div = $("#file_preview_clone").clone();
                    div.removeClass("hidden");
                    div.removeAttr("id");

                    if (this.width == article_image_width && this.height == article_image_height) {
                        var id = "div_" + counter;
                        div.find(".file-preview-image").attr("src", this.src);
                        div.attr("id", id);
                        div.find(".video_element").remove();
                        div.find(".isMain").val(counter);
                        div.find(".caption").attr("name", "caption[" + counter + "]");
                        div.find(".credit").attr("name", "credit[" + counter + "]");
                        div.find(".remove-file").attr("onclick", "javascript:deleteFile('" + id + "')");
                        $("#media_container").append(div);

                        selected_files_array[counter] = this.file;
                        counter++;
                    } else {
                        $("#mediaFile_error").text("Please upload image having width " + article_image_width + "px and height " + article_image_height + "px.");
                    }
                };
                img.src = _URL.createObjectURL(file);
                img.file = file;
            } else {
                var div = $("#file_preview_clone").clone();
                var id = "div_" + counter;
                div.removeClass("hidden");
                div.removeAttr("id");
                div.find(".isMain").val(counter);
                div.find(".caption").attr("name", "caption[" + counter + "]");
                div.find(".credit").attr("name", "credit[" + counter + "]");
                div.find(".file-preview-image").remove();
                div.find(".video_element").attr("id", "videoElement_" + counter);
                div.find(".video_element").find("source").attr("src", (_URL.createObjectURL(file)+"#t=5"));

                div.attr("id", id);
                div.find(".remove-file").attr("onclick", "javascript:deleteFile('" + id + "')");
                $("#media_container").append(div);

                selected_files_array[counter] = file;
                counter++;
            }
        }

        $("#media_container .video_element").each(function () {
            var id = $(this).attr("id");
            if (id != undefined) {
                var file_index = id.split("_")[1];
                $("#" + id)[0].load();

                document.getElementById(id).addEventListener('loadeddata', function () {
                    var canvas = document.getElementById(id).nextElementSibling;
                    canvas.getContext('2d').drawImage(this, 0, 0, canvas.width, canvas.height);

                    video_data[file_index] = JSON.stringify({
                        "thumbnail": canvas.toDataURL("image/jpg"),
                        "duration": convertDuration(this.duration)
                    });
                }, false);
            }
        });
    });
    $("#videoThumbnail").change(function (e) {
        var _URL = window.URL || window.webkitURL;
        var file, img;
        var fileUpload = $(this);
        if ((file = this.files[0])) {
            img = new Image();
            img.onload = function () {
                if (this.width != article_image_width && this.height != article_image_height) {
                    $("#videoThumbnail_error").text("Please upload image having width " + article_image_width + "px and height " + article_image_height + "px.");
                    $(fileUpload).val("");
                } else {
                    $("#videoThumbnail_error").text("");
                    $("#videoThumbnail_preview").attr("src", _URL.createObjectURL(file));
                }
            };
            img.src = _URL.createObjectURL(file);
        }
    });
    $("#videoFile").change(function (e) {
        var source = $('#videoFile_preview');
        source[0].src = URL.createObjectURL(this.files[0]) + "#t=5";
        source.parent()[0].load();

        document.getElementById('video_element').addEventListener('loadeddata', function () {
            var canvas = document.getElementById("canvas_videothumbnail");
            canvas.getContext('2d').drawImage(this, 0, 0, canvas.width, canvas.height);

            $("#thumbnail_image").text(canvas.toDataURL("image/jpg"));
            $("#video_duration").val(convertDuration(this.duration));
            $("#youtubeUrl,#youtube_duration").val("");
        }, false);
    });
});

function convertDuration(s) {
    s = parseInt(s);
    var h = Math.floor(s / 3600); //Get whole hours
    s -= h * 3600;
    var m = Math.floor(s / 60); //Get remaining minutes
    s -= m * 60;

    var duration = new Array();
    if (h > 0) {
        duration.push(h < 10 ? ("0" + h) : h);
    }
    if (m > 0) {
        duration.push((m < 10 && h > 0) ? ("0" + m) : m);
    } else {
        duration.push(h > 0 ? "00" : "0");
    }
    duration.push(s < 10 ? ("0" + s) : s);
    return duration.join(":");
}

function saveArticle() {

    var data = new FormData();
    var formdata = $("#article_form").serializeArray();
    for (var f in formdata) {
        data.append(formdata[f].name, formdata[f].value);
    }
    for (var s in selected_files_array) {
        data.append("allMediaFiles[" + s + "]", selected_files_array[s]);
    }
    for (var v in video_data) {
        data.append("videoData[" + v + "]", JSON.stringify(video_data[v]));
    }
    data.append("isMainSelected", ($("input[name='isMain[]']:checked").val() != undefined) ? $("input[name='isMain[]']:checked").val() : "");
    data.append("deleted_ids", existing_id_removed);
    $("#media_container .caption").each(function () {
        data.append($(this).attr("name"), $(this).val());
    });
    $("#media_container .credit").each(function () {
        data.append($(this).attr("name"), $(this).val());
    });
    $.ajax({
        url: $("#article_form").data("url"),
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        beforeSend: function (data) {
            $("#save_article").text($("input[name='article_id']").val() == "" ? "Publishing.." : "Updating..");
        },
        success: function (data) {
            $(".error").text("");
            if (data.errors != undefined && Object.keys(data.errors).length > 0) {
                $("#save_article").text($("input[name='article_id']").val() == "" ? "Publish" : "Update");
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
        if (video_data[file_index] != undefined) {
            delete video_data[file_index];
        }
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

function saveAsDraft() {
    var hidden_input = $("<input/>", {
        type: "hidden",
        name: "isSaveDraft",
        value: 0
    });
    $("#article_form").append(hidden_input);
    if ($("input[name=is_video]").val() == 1) {
        if ($.trim($("#youtubeUrl_error").text()) != "") {
            return false;
        }
        $("#article_form").submit();
    } else {
        saveArticle();
    }
}
