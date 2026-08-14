// JavaScript Document
function change_status(id){
	$('#reg-tab').removeAttr('class','active');
	$('#home-tab').removeAttr('class','active');
	$('#profile-tab').removeAttr('class','active');
  $('.alltab').removeAttr('class','active');
  $('#'+id).attr('class','alltab active');
}

function save_profile_info(){


    var fname = document.getElementById('fname').value;
	var lname = document.getElementById('lname').value;
	var spfname = document.getElementById('spfname').value;
	var splname = document.getElementById('splname').value;
	var wedding_date = document.getElementById('wedding_date').value;
	var custurl = document.getElementById('custurl').value;
	var registitle = document.getElementById('registitle').value;
	var greeting = document.getElementById('greeting').value;
	if(document.getElementById('is_date_picked').checked == true){
	 var is_date_picked = document.getElementById('is_date_picked').value;
	}else{
	 var is_date_picked = 0;
	}
	if(document.getElementById('fname').value == ''){
       $('#fname').css('border-color','red');
    }else if(document.getElementById('spfname').value == ''){
       $('#spfname').css('border-color','red');
    }
    else{

	var url = document.getElementById('url').value;
	var path = url+'ajax/save_profile_info';
	
	$.ajax({
	      	url:path,
			data:{'fname':fname,'lname':lname,'spfname':spfname,'splname':splname,'wedding_date':wedding_date,'custurl':custurl,'registitle':registitle,'greeting':greeting,'is_date_picked':is_date_picked,'submit':1},
			type:'POST',
			success:function(data)
                {
                    if(data==1){
					 $('#msg-refresh').html('Successfully Updated');
                     jQuery("#msg-refresh").delay(1000).fadeOut("slow");
					 $('#personalInfo-refresh').fadeOut('slow');
					 $('#personalInfo-refresh').fadeIn('slow');
					}
                }
	});
	}
	
}

function changeTitle(url){

   $full_name = $('#fname').val()+' & '+ $('#spfname').val();
   $('#registitle').val($full_name);
   var spouse_name = '';
   var name = '';
   if($('#fname').val()!=''){
     var name = $('#fname').val(); 
   }
   if($('#spfname').val()!=''){
     var spouse_name = $('#spfname').val(); 
   }
   var path = url+'ajax/create_custom_url';
   $.ajax({
      url:path,
	  data:{'name':name,'spouse_name':spouse_name},
	  type:"POST",
	  success:function(data){
	   $('#custurl').val(data);
	  }
   })
   
  
}


function checkPass(url)
{
	
	var pass = document.getElementById('oldpass').value;
	var path = url+"ajax/check_password";
	$.ajax({
		
		data:{'pass':pass},
		url:path,
		type:'POST',
		success: function(data)
		{
		 if(data==0)
		  {
		  	 $('#oldpass-error').html('Password is incorrect.');
			 $('#oldpass-error').css('color','red');
		  	 document.getElementById('oldpass').value='';
		  	 document.getElementById('oldpass').focus();
		  }else{
		    $('#oldpass-error').html('');
		  }
		}
	});
}

function checkconfirmacc(){
	var acc = document.getElementById('acc_no').value;
	var confimAcc = document.getElementById('acc_no_retype').value;
	if(acc!=confimAcc)
	{
		$('#accconf-error').html('Account number do not match.');
		$('#accconf-error').css('color','red');
		document.getElementById('acc_no_retype').value='';
	    document.getElementById('acc_no_retype').focus();
	}else{
		$('#accconf-error').html('');
	}
}

function checkconfirmpass()
{
	var password = document.getElementById('newpass').value;
	var confimPass = document.getElementById('confPass').value;
	if(password!=confimPass)
	{
		$('#conf-error').html('Passwords do not match.');
		$('#conf-error').css('color','red');
		document.getElementById('confPass').value='';
	    document.getElementById('confPass').focus();
	}else{
		$('#conf-error').html('');
	}
}

function change_pass(url){
	
    var newpassword = document.getElementById('newpass').value;
	var confimPass = document.getElementById('confPass').value;
	var pass = document.getElementById('oldpass').value;
	
	if(document.getElementById('oldpass').value == ''){
	   $('#oldpass').css('border-color','red');
	}
	else if(document.getElementById('newpass').value == ''){
	   $('#newpass').css('border-color','red');
	}
	else if(document.getElementById('confPass').value == ''){
	   $('#confPass').css('border-color','red');
	}
	else{
	   var path = url+"ajax/reset_password";
	$.ajax({
		
		url:path,
		data:{'pass':newpassword,'submit':1},
		type:'POST',
		success: function(data)
		{
		 if(data==1){
			   $('#oldpass').val('');
			   $('#newpass').val('');
			   $('#confPass').val('');
			   $('#passmsg-refresh').html('Successfully Updated');
			   jQuery("#passmsg-refresh").delay(1000).fadeOut("slow");
			   
			   $('#pass-refresh').fadeOut('slow');
			   $('#pass-refresh').fadeIn('slow');
			}
		}
	});
	}
}


function add_bank_detail(url){
	
    var account_holder_name = document.getElementById('account_holder_name').value;
	var bank_name = document.getElementById('bank_name').value;
	var ifsc_code = document.getElementById('ifsc_code').value;
	
	var swift_bic = document.getElementById('swift_bic').value;
	var acc_no = document.getElementById('acc_no').value;
	var acc_no_retype = document.getElementById('acc_no_retype').value;
	
	if(document.getElementById('account_holder_name').value == ''){
	   $('#account_holder_name').css('border-color','red');
	}
	else if(document.getElementById('bank_name').value == ''){
	   $('#bank_name').css('border-color','red');
	}
	else if(document.getElementById('ifsc_code').value == ''){
	   $('#ifsc_code').css('border-color','red');
	}
	else if(document.getElementById('swift_bic').value == ''){
	   $('#swift_bic').css('border-color','red');
	}
	
	else if(document.getElementById('acc_no').value == ''){
	   $('#acc_no').css('border-color','red');
	}
	else if(document.getElementById('acc_no_retype').value == ''){
	   $('#acc_no_retype').css('border-color','red');
	}
	
	else{
	   var path = url+"ajax/add_bank_detail";
	$.ajax({
		
		url:path,
		data:{'acc_no':acc_no,'account_holder_name':account_holder_name,
		'bank_name':bank_name,'ifsc_code':ifsc_code,'swift_bic':swift_bic,
		'submit':1},
		type:'POST',
		success: function(data)
		{
		 if(data==1){
			   
			   $('#bankmsg-refresh').html('Successfully Inserted');
			   jQuery("#bankmsg-refresh").delay(1000).fadeOut("slow");
			   
			   $('#bank-refresh').fadeOut('slow');
			   $('#bank-refresh').fadeIn('slow');
			}
			else if(data==2){
			   
			   $('#bankmsg-refresh').html('Successfully Updated');
			   jQuery("#bankmsg-refresh").delay(1000).fadeOut("slow");
			   
			   $('#bank-refresh').fadeOut('slow');
			   $('#bank-refresh').fadeIn('slow');
			}
		}
	});
	}
}

function AllowIFSC() {
           var ifsc = document.getElementById('ifsc_code').value;
           var reg = /[A-Z|a-z]{4}[0][a-zA-Z0-9]{6}$/;
           if (ifsc.match(reg)) {
			   $('#ifsc_code-error').hide();
               $('#ifsc_code-error').html('');
			   $('#ifsc_code-error').css('color','');
			   return true;
           }
           else {
			    $('#ifsc_code-error').show();
			   $('#ifsc_code-error').html('IFSC code is not valid');
			   $('#ifsc_code-error').css('color','red');
               //alert("You Entered Wrong IFSC Code \n\n ------ or------ \n\n IFSC code should be count 11 \n\n-> Starting 4 should be only alphabets[A-Z] \n\n-> Remaining 7 should be accepting only alphanumeric");
			   document.getElementById("ifsc_code").value="";
               document.getElementById("ifsc_code").focus();
			   
               return false;
           }
       }
	   
	   

	   
function readBackgorundImage(file,preview,url) {
      
    var reader = new FileReader();
    var image  = new Image();

    reader.readAsDataURL(file);  
    reader.onload = function(_file) {
        image.src    = _file.target.result;              // url.createObjectURL(file);
        image.onload = function() {
            var w = this.width,
                h = this.height,
                t = file.type,                           // ext only: // file.type.split('/')[1],
                n = file.name,
                s = ~~(file.size/1024) +'KB';
				//$('#'+preview+''+cnt).attr('src', this.src);
				//$('#'+preview+''+cnt).attr('height', '280');
				//$('#'+preview+''+cnt).attr('width', '200');
				var path = url+'ajax/add_background_image';
				
	    var formData = new FormData();
	    formData.append( 'photo', file );
	    formData.append("change", 1);
	
	 $.ajax({
			  url:path,
			 // data:{'itemname':itemname,'cost':cost,'weburl':weburl,'item_specification':item_specification,'file':file,'itemset':1},
			  data:formData,
			  type:'POST',
			  mimeType:"multipart/form-data",
			  contentType: false,
			  cache: false,
			  processData:false,
			  dataType: "json",
			  success:function(data){
				  
			  }
		});
	   };
        image.onerror= function() {
            alert('Invalid file type: '+ file.type);
        };      
    };

}


function change_background(id,preview,url){

  // readURL(id);
  
   if(id.disabled) return alert('File upload not supported!');
var F = id.files;
if(F && F[0]) for(var i=0; i<F.length; i++) readBackgorundImage( F[i],preview,url );
}	   
	   
	   
function image_change_groom(id,preview,url){

  // readURL(id);
   if(id.disabled) return alert('File upload not supported!');
var F = id.files;
if(F && F[0]) for(var i=0; i<F.length; i++)image_change_g( F[i],preview,url);

}


function image_change_g(file,preview,url)
{
    
     var reader = new FileReader();
    var image  = new Image();

    reader.readAsDataURL(file);  
    reader.onload = function(_file) {
        image.src    = _file.target.result;              // url.createObjectURL(file);
        image.onload = function() {
            var w = this.width,
                h = this.height,
                t = file.type,                           // ext only: // file.type.split('/')[1],
                n = file.name,
                s = ~~(file.size/1024) +'KB';
				$('#'+preview).attr('src', this.src);
				$('#'+preview).attr('height', '503');
				$('#'+preview).attr('width', '503');
				var path = url+'ajax/add_img_reg_center';
				
	var formData = new FormData();
	formData.append( 'photo', file );
    formData.append("imgset", 1);
       $.ajax({
              url:path,
             // data:{'itemname':itemname,'cost':cost,'weburl':weburl,'item_specification':item_specification,'file':file,'itemset':1},
              data:formData,
              type:'POST',
              mimeType:"multipart/form-data",
              contentType: false,
              cache: false,
              processData:false,
              dataType: "json",
              success:function(data){
                  if(data == 1){
					  $('#user_img-refresh').load(url+'user/index #user_img-refresh');
                  }
              }
        });
       };
	    image.onerror= function() {
            alert('Invalid file type: '+ file.type);
        };  
    }; 
  
}

function reset_form(url){
	
$('.loader').hide();
$('.error').html('');
	$('.error').css('color','');
	$('.border-color').css('border-color','');
	$('#gift-registry-form').find("input[type=text], textarea ").val("");
	
	 var $el = $('#gift-registry-form');
        $el.wrap('<form>').closest('form').get(0).reset();
        $el.unwrap();
		$('#item_image').attr('src', url+'ui/user/img/default.png');
}

function delete_item(url,id,image){
  var result = confirm('Do you really want to delete this item');
  if(result == true){
	 var path = url+'ajax/delete_item';
	 $('.loader').show();
	  $.ajax({
              url:path,
              data:{'itemid':id,'image':image,'delete':1},
              type:'POST',
              success:function(data){
                  if(data == 1){
					  $('#gift-refresh').load(url+'user/index #gift-refresh');
					  $('.loader').hide();
			          
                  }
              }
        });
  }
}




