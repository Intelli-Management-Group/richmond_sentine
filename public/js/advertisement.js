 $(document).ready(function () {
    )
     var date = new Date();
     $("#expiryDate").datepicker({
         format: "yyyy-mm-dd",
         startDate: date
     }).on('changeDate', function (e) {
         $(this).datepicker('hide');
     });
     $("input[name='advertisementFor[]']").change(function () {
         var selected_val = $(this).val();
         if ($(this).is(":checked")) {
             $("#" + (selected_val + "_container")).removeClass("hidden");
         } else {
             $("#" + (selected_val + "_container")).addClass("hidden");
         }
     });
     $("input[name='advertisementFor[]']").change();
     $("input[name='web_position[]'],input[name='app_position[]']").change(function () {
         var selected_val = $(this).val();         
         if ($(this).is(":checked")) {
             $("#" + (selected_val + "_container")).removeClass("hidden");
             if (selected_val == "sidebar") {
                 $("#" + (selected_val + "_responsive_container")).removeClass("hidden");
                 $("input[value='sidebar_responsive']").prop("checked", true);
             }
         } else {
             $("#" + (selected_val + "_container")).addClass("hidden");
             if (selected_val == "sidebar") {
                 $("#" + (selected_val + "_responsive_container")).addClass("hidden");
                 $("input[value='sidebar_responsive']").prop("checked", false);
             }
         }
     });
     $("input[name='web_position[]'],input[name='app_position[]']").change();
     $(".position_image").change(function () {
         var fileInput = $(this);
         var _URL = window.URL || window.webkitURL;
         var file, img;
         var input_name = $(fileInput).attr("name");
         if ((file = this.files[0])) {
             img = new Image();
             img.onload = function () {
                 if (input_name == "position_image[sidebar]") {
                     if (this.width == advertisement_images_resolution.web_sidebar.width && this.height == advertisement_images_resolution.web_sidebar.height) {
                         $("#sidebar_preview").attr("src", _URL.createObjectURL(file));
                         $("#sidebar_error").text("");
                     } else {
                         $("#sidebar_error").text("Please upload image having width " + advertisement_images_resolution.web_sidebar.width + "px and height " + advertisement_images_resolution.web_sidebar.height + "px.");
                         $(fileInput).val("");
                     }
                 }
                 if (input_name == "position_image[sidebar_responsive]") {
                     if (this.width == advertisement_images_resolution.web_sidebar_responsive.width && this.height == advertisement_images_resolution.web_sidebar_responsive.height) {
                         $("#sidebar_responsive_preview").attr("src", _URL.createObjectURL(file));
                         $("#sidebar_responsive_error").text("");
                     } else {
                         $("#sidebar_responsive_error").text("Please upload image having width " + advertisement_images_resolution.web_sidebar_responsive.width + "px and height " + advertisement_images_resolution.web_sidebar_responsive.height + "px.");
                         $(fileInput).val("");
                     }
                 }
                 if (input_name == "position_image[middle]") {
                     if (this.width == advertisement_images_resolution.web_middle.width && this.height == advertisement_images_resolution.web_middle.height) {
                         $("#middle_image_preview").attr("src", _URL.createObjectURL(file));
                         $("#middle_error").text("");
                     } else {
                         $("#middle_error").text("Please upload image having width " + advertisement_images_resolution.web_middle.width + "px and height " + advertisement_images_resolution.web_middle.height + "px.");
                         $(fileInput).val("");
                     }
                 }
                 if (input_name == "position_image[bottom]") {
                     if (this.width == advertisement_images_resolution.web_bottom.width && this.height == advertisement_images_resolution.web_bottom.height) {
                         $("#bottom_image_preview").attr("src", _URL.createObjectURL(file));
                         $("#bottom_error").text("");
                     } else {
                         $("#bottom_error").text("Please upload image having width " + advertisement_images_resolution.web_bottom.width + "px and height " + advertisement_images_resolution.web_bottom.height + "px.");
                         $(fileInput).val("");
                     }
                 }
                 if (input_name == "position_image[square]") {
                     if (this.width == advertisement_images_resolution.app_square.width && this.height == advertisement_images_resolution.app_square.height) {
                         $("#square_preview").attr("src", _URL.createObjectURL(file));
                         $("#square_error").text("");
                     } else {
                         $("#square_error").text("Please upload image having width " + advertisement_images_resolution.app_square.width + "px and height " + advertisement_images_resolution.app_square.height + "px.");
                         $(fileInput).val("");
                     }
                 }
                 if (input_name == "position_image[horizontal]") {
                     if (this.width == advertisement_images_resolution.app_horizontal.width && this.height == advertisement_images_resolution.app_horizontal.height) {
                         $("#horizontal_preview").attr("src", _URL.createObjectURL(file));
                         $("#horizontal_error").text("");
                     } else {
                         $("#horizontal_error").text("Please upload image having width " + advertisement_images_resolution.app_horizontal.width + "px and height " + advertisement_images_resolution.app_horizontal.height + "px.");
                         $(fileInput).val("");
                     }
                 }
                 if (input_name == "position_image[tablet_square]") {
                     if (this.width == advertisement_images_resolution.tablet_square.width && this.height == advertisement_images_resolution.tablet_square.height) {
                         $("#tablet_square_preview").attr("src", _URL.createObjectURL(file));
                         $("#tablet_square_error").text("");
                     } else {
                         $("#tablet_square_error").text("Please upload image having width " + advertisement_images_resolution.tablet_square.width + "px and height " + advertisement_images_resolution.tablet_square.height + "px.");
                         $(fileInput).val("");
                     }
                 }
             };
             img.src = _URL.createObjectURL(file);
         }
     });
     $(document).on('change', '.is_default', function (e) {
         if ($(this).is(":checked")) {
             $(this).parents(".tree").find("input[type='checkbox']").not(this).prop("checked", true).attr("disabled", "disabled");
         } else {
             $(this).parents(".tree").find("input[type='checkbox']").not(this).prop("checked", false).removeAttr("disabled");
         }
     });
     $(document).on('change', '#isLocationDetection', function (e) {
         if ($(this).is(":checked")) {
             $(".address_container").removeClass("hidden");
         } else {
             $(".address_container").addClass("hidden");
         }
     });
     $("#isLocationDetection").change();
     $(".is_default:checked").change();
 });

 function saveAsDraft() {
     var hidden_input = $("<input/>", {
         type: "hidden",
         name: "isSaveDraft",
         value: 0
     });
     $("#advertisement_form").append(hidden_input);
     $("#advertisement_form").submit();
 }
