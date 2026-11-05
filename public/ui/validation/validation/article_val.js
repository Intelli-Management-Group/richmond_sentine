$(function() {


    // Setup form validation on the #register-form element
    $("#add_article").validate({
    
        // Specify the validation rules
        rules: {
            heading: {

                required: true
            },
            title: {

                required: true
            },                                          
            subtitle: {

                required: true
            },
            description: {

                required: true
            }
        },
        
        // Specify the validation error messages
        messages: {
            heading: "Please enter Heading",
            title: "Please enter title",
            subtitle: "Please enter subtitle",
            ad_text: "Please enter description",
            
            
        },
        
        submitHandler: function() 
        {

                var current = 0;
			    var URl= $('#producturl').val();
               
                var formData = new FormData();
                //formData.append('imageupload', $('#imageupload')[0].files[0]);
                $.each($("input[name^='imageupload']")[0].files, function(i, file) {
                formData.append('imageupload['+i+']', file);
            //    alert('it_image['+i+']');
                current++;
                });
                $.each($("input[name^='videoupload']")[0].files, function(i, file) {
                formData.append('videoupload['+i+']', file);
            //    alert('it_image['+i+']');
                current++;
                });


                var frmdata = $(add_article).serializeArray();
                $.each(frmdata, function (key, input) {
                formData.append(input.name, input.value);
                });

        		    jQuery.ajax({

                        type: "POST",

                        url : URl + "admin/addArticle",

                        data: formData,

                        dataType: "JSON",
                         
                        contentType: false,

                        processData: false,

                        success: function (data) {

        			   //alert(data);
                            if(data.msg_status == 'msg_success' ){

                               $('#msg').html('<div class="alert alert-success">'+data.msg_message+'</div>').show().fadeOut(1000);
                               setTimeout(function(){ //window.location.href=URl + "admin/addArticle";}, 5000);

                            }else{
                                
                               $('#msg').html('<div class="alert alert-success">'+data.msg_message+'</div>').show().fadeOut(5000);

                            }
                            $('.loader').hide();

        			   }

                    });
               
               
            
        }
        


    });

  });
  
  
 