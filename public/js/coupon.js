 var selected_files_array = {};
 var existing_id_removed = new Array();
 var thumbnail_files = "";
 $(document).ready(function () {
     $("#coupon_highlights,#coupon_finePrints,#coupon_detail").summernote({
          toolbar: [
              ['style', ['bold', 'italic', 'underline', 'clear']],
              ['font', ['strikethrough', 'superscript', 'subscript']],
              ['color', ['color']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['table', ['table']],
              ['insert', ['link']],
              ['view', ['codeview', 'help', 'undo', 'redo']],
          ],
         height: 150,
         disableDragAndDrop: true
     });
     $('.clockpicker').clockpicker({
         autoclose: 'true'
     });
     $("#bannerImage").unbind("change");
     $("#bannerImage").change(function (e) {
         $("#bannerImage_error").text("");
         var selected_files = this.files;

         var counter = 0;
         if($("#banner_image_container .file-preview").length > 0){
            counter = parseInt($("#banner_image_container .file-preview:last").attr("id").split("_")[1]);
           
         }
         counter = counter + 1;
         for (var s = 0; s < selected_files.length; s++) {
             var file, img;
             var _URL = window.URL || window.webkitURL;
             file = selected_files[s];

             img = new Image();
             img.onload = function () {
                 var div = $("#file_preview_clone").clone();
                 div.removeClass("hidden");
                 div.find(".video_element").remove();
                 div.removeAttr("id");
                 if (this.width == 800 && this.height == 500) {
                     var id = "div_" + counter;
                     div.find(".file-preview-image").attr("src", this.src);
                     div.attr("id", id);
                     div.find(".remove-file").attr("onclick", "javascript:deleteFile('" + id + "')");
                     $("#banner_image_container").append(div);

                     selected_files_array[counter] = this.file;
                     counter++;
                 } else {
                     $("#bannerImage_error").text("Please upload image having width 800px and height 500px.");
                 }
             };
             img.src = _URL.createObjectURL(file);
             img.file = file;
         }
     });
     $("#thumbnailImage").change(function (e) {
         var _URL = window.URL || window.webkitURL;
         var file, img;
         var fileUpload = $(this);
         if ((file = this.files[0])) {
             img = new Image();
             img.onload = function () {
                 if (this.width != 500 && this.height != 500) {
                     $("#thumbnailImage_error").text("Please upload image having width 500px and height 500px.");
                     $(fileUpload).val("");
                 } else {
                     $("#thumbnailImage_error").text("");
                     $("#thumbnailImage_preview").attr("src", _URL.createObjectURL(file));
                     thumbnail_files = this.file;
                 }
             };
             img.src = _URL.createObjectURL(file);
             img.file = file;
         }
     });
     $("#save_coupon").off("click");
     $("#save_coupon").on("click", function () {
         var data = new FormData();
         var formdata = $("#coupon_form").serializeArray();
         for (var f in formdata) {
             data.append(formdata[f].name, formdata[f].value);
         }
         for (var s in selected_files_array) {
             data.append("bannerImage[" + s + "]", selected_files_array[s]);
         }        
         data.append("deleted_ids", existing_id_removed);
         data.append("thumbnailImage", thumbnail_files);
         $.ajax({
             url: $("#coupon_form").data("url"),
             type: 'POST',
             data: data,
             processData: false,
             contentType: false,
             beforeSend: function (data) {
                 $("#save_coupon").text("Saving..");
             },
             success: function (data) {
                 $(".error").text("");
                 if (data.errors != undefined && Object.keys(data.errors).length > 0) {
                     $("#save_coupon").text("Save");
                 }
                 for (var e in data.errors) {
                     $(".error[for=" + e + "]").text(data.errors[e]);
                 }
                 if (data.list != undefined) {
                     window.location.href = data.list;
                 }
             }
         });
     });
 });

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
