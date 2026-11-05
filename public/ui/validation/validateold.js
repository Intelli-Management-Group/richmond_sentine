// JavaScript Document

function validatelogin(path)
{
	
	var username=$("#username").val();
	var pass=$("#password").val();
	
	var link_url = path+"index.php/Logincheck/adminlogin?useremail="+username+"&password="+pass;
	//alert(link_url);
	$.ajax({
		url: link_url,
		success: function(data) {
		           
					 var myobj = eval('('+data+')');
					 $("#error").html(myobj.a);
					 $(".form-control").addClass( "has-error" );
					 $(".input-group-addon").addClass( "has-error" );
					 if(myobj.b==1)
					 {
						  $('.form-control').addClass('has-success').removeClass('has-error');
						  $('.input-group-addon').addClass('has-success').removeClass('has-error');
						 window.location= path+"admin/home";
						
					 }
				
			
			}
		
		
		});
	return false;
	
}






function signup_validation()
{
	var username = document.getElementById('username').value;
	var firstname = document.getElementById('firstname').value;
	var lastname = document.getElementById('lastname').value;
	var emailid = document.getElementById('emailid').value;
	var password = document.getElementById('password').value;
	var confimPass = document.getElementById('confimPass').value;
	var url = document.getElementById('url').value;
	
	if(document.getElementById('username').value=='')
	{
		document.getElementById('username').style.borderColor='red';
		return false;
	}
	
	else if(document.getElementById('firstname').value=='')
	{
		document.getElementById('firstname').style.borderColor='red';
		return false;
	}
	else if(document.getElementById('lastname').value=='')
	{
		document.getElementById('lastname').style.borderColor='red';
		return false;
	}
	else if(document.getElementById('emailid').value=='')
	{
		document.getElementById('emailid').style.borderColor='red';
		return false;
	}
	else if(document.getElementById('password').value=='')
	{
		document.getElementById('password').style.borderColor='red';
		return false;
	}
	else if(document.getElementById('confimPass').value=='')
	{
		document.getElementById('confimPass').style.borderColor='red';
		return false;
	}
	else{
		
		var path =url+'ajax/signup';
	  $.ajax({
		   url:path,
		   data:{'username':username,'firstname':firstname,'lastname':lastname,'emailid':emailid,'password':password,'submit':1},
		   type:'POST',
		   success:function(data){
			   
			if(data == 1){
			  $('#regis-div').hide();
			  $('#after-regis-msg').html('Thanks for registering with MyTofah. We just sent you an email to <span style="color:blue">'+emailid+ '</span> to confirm your email address. Click the link in the email to finish signing up.');
			}else{
			  $('#regis').find("input[type=text],input[type=password], textarea").val("");
			  $('#form-error').html('Service failed');
			}


			   }
		});
	}
	
		
}


function check_username(url)
{
	
	var username = document.getElementById('user_name').value;
	var path = url+"ajax/checkvalid_username";
	$.ajax({
		
		data:{'name':username},
		url:path,
		type:'POST',
		success: function(data)
		{
		  if(data==0)
		  {
		  	$('#user_name-error').show();
			$('#user_name-error').html('Please enter correct Username.');
		  	document.getElementById('user_name').value='';
		  	document.getElementById('user_name').focus();
		  }else{
			$('#email').attr('readonly','readonly');  
			$('#user_name-error').hide();
		    $('#user_name-error').html('');
		  }
		}
	});
}

function valid_username(url)
{
	
	var username = document.getElementById('username').value;
	var path = url+"ajax/checkvalid_username";
	$.ajax({
		
		data:{'name':username},
		url:path,
		type:'POST',
		success: function(data)
		{
		  if(data==1)
		  {
		  	$('#username-error').show();
			$('#username-error').html('Someone already have that username. Try another?');
		  	document.getElementById('username').value='';
		  	document.getElementById('username').focus();
		  }else{
		    $('#username-error').html('');
			$('#username-error').hide();
		  }
		}
	});
}

function validate_email(url)
{
	
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	
   

	var email = document.getElementById('emailid').value;
	if(!re.test(email)){
	  $('#emailid-error').show();
	  $('#emailid-error').html('Please enter valid email id');
	  document.getElementById('emailid').value='';
	  document.getElementById('emailid').focus();
	}else{
	var path = url+"ajax/checkvalid_email";
	   
	   $('#emailid-error').html('');
	   $('#emailid-error').hide();
	$.ajax({
		
		data:{'email':email},
		url:path,
		type:'POST',
		success: function(data)
		{
			
		  if(data==1)
		  {
		  	 $('#emailid-error').show();
			 $('#emailid-error').html('This email is already registerd with us. Try Another?');
		  	 document.getElementById('emailid').value='';
		  	 document.getElementById('emailid').focus();
		  }else{
			  $('#emailid-error').html('');
			  $('#emailid-error').hide();
		  }
		}
	});
	}
}

function checkpass()
{
	var password = document.getElementById('password').value;
	var confimPass = document.getElementById('confimPass').value;
	if(password!=confimPass)
	{
		$('#conf-error').show();
		$('#conf-error').html('Passwords do not match.');
		document.getElementById('confimPass').value='';
	    document.getElementById('confimPass').focus();
	}else{
		$('#conf-error').html('');
		$('#conf-error').hide();
	}
}

function login_checkemail(url)
{
	
	 email = document.getElementById('email').value;
	var path = url+"ajax/login_email";
	$.ajax({
		
		data:{'email':email},
		url:path,
		type:'POST',
		success: function(data)
		{
			
		  if(data==0)
		  {
		     $('#email-error').show();
			 $('#email-error').html('Please enter correct Email.');
			 document.getElementById('email').value='';
		  	 document.getElementById('email').focus();
		  }else{
		    $('#email-error').html('');
			$('#email-error').hide();
			$('#user_name').attr('readonly','readonly');
		  }
		}
	});
	
}

function login_checkepass(url)
{
	
	var email = '';
    var user_name = '';
	if($('#email').val()!=''){
	 email = document.getElementById('email').value;
	}
	if($('#user_name').val()!=''){
	 user_name = document.getElementById('user_name').value;
	}
	var path = url+"ajax/login_pass";
	$.ajax({
		
		data:{'email':email,'pass':pass,'user_name':user_name},
		url:path,
		type:'POST',
		success: function(data)
		{
			
		  if(data==0)
		  {
		  	 $('#pass-error').show();
			 $('#pass-error').html('Password is incorrect.');
		  	 document.getElementById('pass').value='';
		  	 document.getElementById('pass').focus();
		  }else{
		    $('#pass-error').html('');
			$('#email-error').hide();
		  }
		}
	});
}

function login_check(url){
    
    var email = '';
	 var user_name = '';
	if($('#email').val()!=''){
	 email = document.getElementById('email').value;
	}
	if($('#user_name').val()!=''){
	 user_name = document.getElementById('user_name').value;
	}
	
    var pass = $('#pass').val();
    if(email=='')
    {
        document.getElementById('email').style.borderColor='red';
    }
    else if(email !='' && pass=='')
    {
        document.getElementById('pass').style.borderColor='red';
    }
    else
    {
        var path = url+'ajax/logincheck';
        $.ajax({
        url:path,
        data:{'Email':email,'Pass':pass,'User_name':user_name,'Login':1},
        type:'POST',
        success:function(data){
            if(data==0)
            {
               if($('#user_name').val()!=''){
			    $('#user_name-error').html('This username is not verified.');
			   }
			   if($('#email').val()!=''){
	            $('#email-error').html('This email is not verified.');
	           }
				
                document.getElementById('email').value='';
                document.getElementById('email').focus();
                document.getElementById('pass').value='';
                  
            }else{
			   window.location= url+"user";
			}
        }
      })
    }
    
}


function personal_info(){
	
	
    var name = document.getElementById('name').value;
	var spouse_name = document.getElementById('spouse_name').value;
	var wedding_date = document.getElementById('wedding_date').value;
	var email = document.getElementById('email').value;
	var venue = document.getElementById('venue').value;
	var contact_no = document.getElementById('contact_no').value;
	var custom_url = document.getElementById('custom_url').value;
	var summary = document.getElementById('summary').value;
	var days = document.getElementById('days').value;
	var url = document.getElementById('url').value;
	var path = url+'ajax/personal_info';
	
	var formData = new FormData();
	formData.append('malephoto', $('#male_image')[0].files[0] );
	formData.append('femalephoto', $('#female_image')[0].files[0] );
	formData.append("name", name);
    formData.append("spouse_name", spouse_name);
	formData.append("wedding_date", wedding_date);
	formData.append("email", email);
	formData.append("venue", venue);
    formData.append("contact_no", contact_no);
	formData.append("summary", summary);
	formData.append("days", days);
	formData.append("custom_url", custom_url);
	
	 /*if($('#male_image').val()==''){
	  document.getElementById('male-error').style.borderColor='red';
	  return false;
	}else{
	   var file = $('#male_image')[0].files[0];
	   if (file.type.match('image.*')) {
          
       }else{
		   $('#maleimage-error').html('Image type is not valid');
		   $('#maleimage-error').css('color','red');
	     return false;
	   }
	 }
    if($('#female_image').val()==''){
	  document.getElementById('female-error').style.borderColor='red';
	  return false;
	}else{
	   var file = $('#female_image')[0].files[0];
	   if (file.type.match('image.*')) {
          
       }else{
		   $('#femaleimage-error').html('Image type is not valid');
		   $('#femaleimage-error').css('color','red');
	     return false;
	   }
	 }*/
	
		
	if(name=='' && spouse_name=='' && wedding_date=='' && email=='' &&  contact_no=='' && $('#male_image').val()=='' && $('#female_image').val()==''){

	  document.getElementById('name').style.borderColor='red';
	  document.getElementById('spouse_name').style.borderColor='red';
	  document.getElementById('wedding_date').style.borderColor='red';
	  document.getElementById('email').style.borderColor='red';
	  document.getElementById('contact_no').style.borderColor='red';
	  document.getElementById('male_image').style.borderColor='red';
	  document.getElementById('female_image').style.borderColor='red';
	}
	
	
	
	
	else if(name=='')
	{
		document.getElementById('name').style.borderColor='red';
		return false;
	}
	
	else if(spouse_name=='')
	{
		document.getElementById('spouse_name').style.borderColor='red';
		return false;
	}
	else if(wedding_date=='')
	{
		document.getElementById('wedding_date').style.borderColor='red';
		return false;
	}
	else if(email=='')
	{
		document.getElementById('email').style.borderColor='red';
		return false;
	}
	else if(contact_no=='')
	{
		document.getElementById('contact_no').style.borderColor='red';
		return false;
	}
	
	else{
	 $.ajax({
                 //data:{'name':name,'spouse_name':spouse_name,'wedding_date':wedding_date,'email':email,'venue':venue,'contact_no':contact_no,'summary':summary,'days':days},
                data:formData,
				url:path,
				mimeType:"multipart/form-data",
			    contentType: false,
			    cache: false,
			    processData:false,
			    dataType: "json",
                type:'POST',
                success:function(data)
                {
                    $('#homeli').removeClass('active');
	                $('#giftli').addClass('active');
	                $('#home').removeClass('active in');
	                $('#gift-repository').addClass('active in');
                }
         });
	 
	}
	

}

function continue_active(){
    $('#homeli').removeClass('active');
    $('#giftli').removeClass('active');
	$('#home').removeClass('active in');
	$('#gift-repository').removeClass('active in');
	$('#finalize').addClass('active');
	$('#messages').addClass('active in');
	
}

function itme_info()
{
  	
	var itemname = document.getElementById('item').value;
	var cost = document.getElementById('cost').value;
	var weburl = document.getElementById('weburl').value;
	var item_specification = document.getElementById('item_specification').value;
	var url = document.getElementById('url').value;
	var path = url+'ajax/item_info';
	
	var formData = new FormData();
	formData.append( 'photo', $('#userfile')[0].files[0] );
	formData.append("itemname", itemname);
    formData.append("cost", cost);
	formData.append("weburl", weburl);
	formData.append("item_specification", item_specification);
	formData.append("itemset", 1);
	
	if($('#userfile').val()==''){
	  document.getElementById('userfile').style.borderColor='red';
	  return false;
	}else{
	   var file = $('#userfile')[0].files[0];
	   if (file.type.match('image.*')) {
          
       }else{
		   $('#image-error').html('Image type is not valid');
		   $('#image-error').css('color','red');
	     return false;
	   }
	}
	if(itemname=='')
	{
		document.getElementById('item').style.borderColor='red';
		return false;
	}
	
	else if(cost=='')
	{
		document.getElementById('cost').style.borderColor='red';
		return false;
	}
	else if(url=='')
	{
		document.getElementById('wedding_date').style.borderColor='red';
		return false;
	}
	else{
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
			        $('#gift-refresh').load(url+'user/index #gift-refresh');
					
					$('#myModal').modal('toggle');
					document.getElementById('item').value='';
					document.getElementById('cost').value='';
					document.getElementById('weburl').value='';
					document.getElementById('item_specification').value='';
					
					$('#homeli').removeClass('active');
	                $('#giftli').addClass('active');
	                $('#home').removeClass('active in');
	                $('#gift-repository').addClass('active in');
				  }
			  }
		});
	}
	
	
}

function validate_text(id){
 if($('#'+id).val() == ''){
   $('#'+id).css('border-color','red');
 }else{
   $('#'+id).css('border-color','');
 }
}

function valid_text(id,error){
	
  if($('#'+id).val()!=''){ 
   var re = /^[a-zA-Z]*$/;
	var text = document.getElementById(id).value;
	if(!re.test(text)){
		$('#'+error).show();
	  $('#'+error).html('Please enter only character');
	  document.getElementById(id).value='';
	  document.getElementById(id).focus();
	 
	}else{
	   $('#'+error).html('');
	   $('#'+error).hide();
	}
  }
}

function valid_textitem(id,error){
	 if($('#'+id).val()!=''){ 
   var re = /^[a-zA-Z]*$/;
	var text = document.getElementById(id).value;
	if(!re.test(text)){
	  $('#'+error).html('Please enter only character');
	  $('#'+error).css('color','red');
	  document.getElementById(id).value='';
	  document.getElementById(id).focus();
	 
	}else{
	   $('#'+error).html('');
	  
	}
  }
}

function valid_email(id){
   
  if($('#'+id).val()!=''){ 
   var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	var email = document.getElementById(id).value;
	if(!re.test(email)){
	  $('#emailid-error').html('Please enter valid email id');
	  document.getElementById(id).value='';
	  document.getElementById(id).focus();
	  $('#emailid-error').css('color','red');
	}else{
	   $('#emailid-error').html('');
	  $('#emailid-error').css('color','');
	}
  }
}

function valid_contact(id){
 var re = /^\d*(?:\d{1,2})?$/;
 if($('#'+id).val()!=''){
 var contact = document.getElementById(id).value;
    if(!re.test(contact)){
      $('#contact-error').html('Please enter number');
      document.getElementById(id).value='';
      document.getElementById(id).focus();
      $('#contact-error').css('color','red');
    }else if(contact.length<10){
       $('#contact-error').html('Please enter atleast 10 numbers');
      document.getElementById(id).value='';
      document.getElementById(id).focus();
      $('#contact-error').css('color','red');
    }else{
      $('#contact-error').html('');
       $('#contact-error').css('color','');
    }
 }
 
}
 


function valid_url(id){ 
if($('#'+id).val()!=''){
	var url = $('#'+id).val()
var re = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/;
 if (!re.test(url)) { 
     $('#url-error').html('Please enter valid url');
	  document.getElementById(id).value='';
	  document.getElementById(id).focus();
	  $('#url-error').css('color','red');
  }else{
	   $('#url-error').html('');
	  $('#url-error').css('color','');
  }
 }
}

function valid_cost(id){

 if($('#'+id).val()!=''){
	var cost = $('#'+id).val()
 
  var re = /^\d*(?:\.\d{1,2})?$/;
 if (!re.test(cost)) { 
     $('#cost-error').html('Please enter digits');
	  document.getElementById(id).value='';
	  document.getElementById(id).focus();
	  $('#cost-error').css('color','red');
  }else{
	  $('#cost-error').html('');
	  $('#cost-error').css('color','');
  }
 }
}

function giftclick()
{
       var name = document.getElementById('name').value;
       var spouse_name = document.getElementById('spouse_name').value;
       var wedding_date = document.getElementById('wedding_date').value;
       var email = document.getElementById('email').value;
       var venue = document.getElementById('venue').value;
       var contact_no = document.getElementById('contact_no').value;
       var summary = document.getElementById('summary').value;
          
       if(name=='' && spouse_name=='' && wedding_date=='' && email=='' && venue=='' &&  contact_no==''){

         document.getElementById('name').style.borderColor='red';
         document.getElementById('spouse_name').style.borderColor='red';
         document.getElementById('wedding_date').style.borderColor='red';
         document.getElementById('email').style.borderColor='red';
         document.getElementById('venue').style.borderColor='red';
         document.getElementById('contact_no').style.borderColor='red';
       }
       
       else if(name=='')
       {
               document.getElementById('name').style.borderColor='red';
               return false;
       }
       
       else if(spouse_name=='')
       {
               document.getElementById('spouse_name').style.borderColor='red';
               return false;
       }
       else if(wedding_date=='')
       {
               document.getElementById('wedding_date').style.borderColor='red';
               return false;
       }
       else if(email=='')
       {
               document.getElementById('email').style.borderColor='red';
               return false;
       }
       else if(venue=='')
       {
               document.getElementById('venue').style.borderColor='red';
               return false;
       }
       else if(contact_no=='')
       {
               document.getElementById('contact_no').style.borderColor='red';
               return false;
       }

}


function active()
{
       var url = document.getElementById('url').value;
       var path = url+'ajax/insert_registrydata';
       $.ajax({
              url:path,
              data:{'regitration':1},
              type:'POST',
              success:function(data){
				  if(data == 1){
                   window.location= url+"user";  
				  }
              }
                         
       });
}

$(function() {
	$( "#wedding_date" ).datepicker({
			minDate : new Date()
	});
});



function reset_form(){
	$('#regis-div').show();
	$('#user_name').removeAttr('readonly');
	$('#email').removeAttr('readonly');
	$('#regis').find("input[type=text],input[type=password], textarea").val("");
	$('#loginform').find("input[type=text],input[type=password], textarea").val("");
	$('.eror').hide();
	$('.eror').html('');
	$('.border-color').css('border-color','');
	$('#after-regis-msg').html('');
	$('#form-error').html('');
}

function reset_registry(url){
	$('.error').html('');
	$('.error').css('color','');
	$('.border-color').css('border-color','');
	$('#gift-registry-form').find("input[type=text], textarea ").val("");
	
	 var $el = $('#gift-registry-form');
        $el.wrap('<form>').closest('form').get(0).reset();
        $el.unwrap();
		$('#item_image').attr('src', url+'ui/user/img/default.png');
		
}

function create_url(url){
   var spouse_name = '';
   var name = '';
   if($('#name').val()!=''){
     var name = $('#name').val(); 
   }
   if($('#spouse_name').val()!=''){
     var spouse_name = $('#spouse_name').val(); 
   }
   var path = url+'ajax/create_custom_url';
   $.ajax({
      url:path,
	  data:{'name':name,'spouse_name':spouse_name},
	  type:"POST",
	  success:function(data){
	   $('#custom_url').val(data);
	  }
   })
}

function readImage(file,preview) {
      
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
				$('.'+preview).attr('src', this.src);	
         };
        image.onerror= function() {
            alert('Invalid file type: '+ file.type);
        };      
    };

}

function image_change(id,preview){
	
	  // readURL(id);
	   if(id.disabled) return alert('File upload not supported!');
    var F = id.files;
    if(F && F[0]) for(var i=0; i<F.length; i++) readImage( F[i],preview );
}

function readEditImage(file,preview,cnt,url) {
      
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
				$('#'+preview+''+cnt).attr('src', this.src);
				$('#'+preview+''+cnt).attr('height', '280');
				$('#'+preview+''+cnt).attr('width', '200');
				var path = url+'ajax/change_gift_image';
		
	var formData = new FormData();
	formData.append( 'photo', file );
	formData.append("change", 1);
	formData.append("count", cnt);
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


function changeEditImage(id,preview,cnt,url){

  // readURL(id);
  
   if(id.disabled) return alert('File upload not supported!');
var F = id.files;
if(F && F[0]) for(var i=0; i<F.length; i++) readEditImage( F[i],preview,cnt,url );
}