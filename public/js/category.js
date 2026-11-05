$(document).ready(function(){
    $("#successMessage,#errorMessage").delay(5000).slideUp(300);
    $("#isDisplayInMenu").change(function(){
        if($(this).is(":checked")){
            $(".menu_section_index").removeClass("hidden");
        }else{
            $(".menu_section_index").addClass("hidden");
        }
    });
    $("#isDisplayInMore").change(function(){
        if($(this).is(":checked")){
            $(".more_menu_index").removeClass("hidden");
        }else{
            $(".more_menu_index").addClass("hidden");
        }
    });
    $("#isDisplayInFrontend").change(function(){
        if($(this).is(":checked")){
            $(".frontend_index").removeClass("hidden");
        }else{
            $(".frontend_index").addClass("hidden");
        }
    });
    $(document).on('click', '.tree label', function(e) {
        $(this).next('ul').fadeToggle();
        e.stopPropagation();
    });
    $('.tree ul').fadeIn();
    var _URL = window.URL || window.webkitURL;
    $("#iconImage").change(function (e) {
        var file, img;
        var fileUpload = $(this);
        if ((file = this.files[0])) {
            img = new Image();
            img.onload = function () {
                if(this.width != 120 && this.height != 120){                   
                    $("#iconImageError").text("Please upload image having width 120px and height 120px.");
                    $(fileUpload).val("");                    
                }else{
                    $("#iconImageError").text("");
                    $("#thumbnail").attr("src",_URL.createObjectURL(file));
                }
            };
            img.src = _URL.createObjectURL(file);
        }
    });
});
