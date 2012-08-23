function CheckAll(form_name){

 if(form_name===undefined){ var form_name = 'submit_form';}  
 
count = document.forms[form_name].elements.length;
    for (i=0; i < count; i++) 
	{
    if((document.forms[form_name].elements[i].checked == 1) ||(document.forms[form_name].elements[i].checked == 0))
    	{document.forms[form_name].elements[i].checked = 1; }
  
	}
}
function UncheckAll(form_name){

if(form_name===undefined){ var form_name = 'submit_form';} 

count = document.forms[form_name].elements.length;
    for (i=0; i < count; i++) 
	{
    if((document.forms[form_name].elements[i].checked == 1) || (document.forms[form_name].elements[i].checked == 0))
    	{document.forms[form_name].elements[i].checked = 0; }

	}
}



function show_adv_options(){

nms = $('type').value;

if (nms == 'menu') {
document.getElementById("add_after_menu").style.display = "inline";
document.getElementById("banners_pages_area").style.display = "inline";
document.getElementById("bnr_content_type").style.display = "inline";

show_banner_img_or_code();

}else if(nms == 'offer') {
document.getElementById("add_after_menu").style.display = "none";
document.getElementById("banners_pages_area").style.display = "none";
document.getElementById("bnr_content_type").style.display = "none";

document.getElementById("banners_code_area").style.display = "inline";
document.getElementById("banners_img_area").style.display = "inline";
document.getElementById("banners_url_area").style.display = "inline"


}else{
document.getElementById("add_after_menu").style.display = "none";
document.getElementById("banners_pages_area").style.display = "inline";
document.getElementById("bnr_content_type").style.display = "inline";

show_banner_img_or_code();
}

}



function show_banner_img_or_code(){

if($('c_type').value=="code"){
document.getElementById("banners_code_area").style.display = "inline";
document.getElementById("banners_img_area").style.display = "none";
document.getElementById("banners_url_area").style.display = "none"

}else{

document.getElementById("banners_code_area").style.display = "none";

document.getElementById("banners_img_area").style.display = "inline";
document.getElementById("banners_url_area").style.display = "inline"
}
}

function set_checked_color(id,box){
if(box.checked == true){
document.getElementById(id).style.backgroundColor='#EFEFEF';
}else{
document.getElementById(id).style.backgroundColor='#FFFFFF';
}
}

function set_tr_color(tr,color){

if(tr.style.backgroundColor !='#efefef'){
tr.style.backgroundColor=color;
}
}


function set_menu_pages(box){

nms = box.options[box.selectedIndex].value;

if (nms == 'c') {
count = document.submit_form.elements.length;
    for (i=0; i < count; i++) 
	{
    if((document.submit_form.elements[i].checked == 1) ||(document.submit_form.elements[i].checked == 0))
    	{
if(document.submit_form.elements[i].name == 'pages[0]'){
document.submit_form.elements[i].checked = 1; 
}else{
document.submit_form.elements[i].checked = 0; 
}
}

  
	}
}else{
count = document.submit_form.elements.length;
    for (i=0; i < count; i++) 
	{
    if((document.submit_form.elements[i].checked == 1) ||(document.submit_form.elements[i].checked == 0))
    	{document.submit_form.elements[i].checked = 1; }
  
	}
}

}

function uploader(folder,f_name,id)
{
if ( id === undefined ) {
      id = 'win0';
   }


msgwindow=window.open("uploader.php?folder="+folder+"&f_name="+f_name+"&win_name="+id,id,"toolbar=no,scrollbars=no,width=520,height=220,top=200,left=200")
}

function uploader2(folder,f_name,frm)
{

msgwindow=window.open("uploader.php?folder="+folder+"&f_name="+f_name+"&frm="+frm,"popup","toolbar=no,scrollbars=no,width=520,height=220,top=200,left=200")
}



function show_hide_preview_text(box){
if(box.checked == true){
document.getElementById('preview_text_tr').style.display = "none";
}else{
document.getElementById('preview_text_tr').style.display = "inline";
}
}


   function show_snd_mail_options(box){
           nms = box.options[box.selectedIndex].value;

if (nms == 'all') {
   document.getElementById("when_one_user_email").style.display = "none";
           }else{
   document.getElementById("when_one_user_email").style.display = "inline";
  }
  }

   function show_snd_mail_options2(box){
           nms = box.options[box.selectedIndex].value;

if (nms == 'msg') {
   document.getElementById("sender_email_tr").style.display = "none";
    document.getElementById("msg_type_tr").style.display = "none";
document.getElementById("msg_encoding_tr").style.display = "none";
           }else{
   document.getElementById("sender_email_tr").style.display = "inline";
document.getElementById("msg_type_tr").style.display = "inline";
document.getElementById("msg_encoding_tr").style.display = "inline";
 
  }
  }

function show_uploader_options(box){

if (box == '1') {
   document.getElementById("file_field").style.display = "none";
    document.getElementById("url_field").style.display ="inline";

           }else{
   document.getElementById("file_field").style.display = "inline";
document.getElementById("url_field").style.display =  "none";
 
  }
}





function show_hide_fields_divs(value){
if(value=="text"){
$('fields_default_value_div').style.display='inline';
$('fields_options_div').style.display='none';
}else{
$('fields_default_value_div').style.display='none';
$('fields_options_div').style.display='inline';
}
}


 function set_status_text_display(value){
        
    if(value==0){
    $('status_text').style.display='inline';
    }else{   
    $('status_text').style.display='none';
    }
    }
    
    
     function set_payment_method_text_display(value){
        
    if(value==0){
    $('payment_method_name').style.display='inline';
    }else{   
    $('payment_method_name').style.display='none';
    }
    }
    
     function set_shipping_method_text_display(value){
        
    if(value==0){
    $('shipping_method_name').style.display='inline';
    }else{   
    $('shipping_method_name').style.display='none';
    }
    }