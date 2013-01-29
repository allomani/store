
function banner_pop_open(url,name){
    msgwindow=window.open(url,name,"toolbar=yes,scrollbars=yes,resizable=yes,width=650,height=300,top=200,left=200");
}

function banner_pop_close(url,name){
    msgwindow=window.open(url,name,"toolbar=yes,scrollbars=yes,resizable=yes,width=650,height=300,top=200,left=200");
}



function add_to_fav(id){
    var action ='';
    
    if($('.add_to_fav').hasClass('success')){
        action = 'remove_from_fav';
    }else{
        action = 'add_to_fav';   
    }
    $.post('ajax.php',{
        action: action,
        id: id
    },function(data){
        if(data == ""){
            if(action == 'remove_from_fav'){
                $('.add_to_fav').removeClass('success');    
            }else{
                $('.add_to_fav').addClass('success');    
            }
        }else{
            alert(data);
        }
    }
    );
    return false;
}


function init_tabs(div_id){
    $(function() {
        $('#'+div_id).tabs();
    });
}

function CheckAll(form_name){

    
    if(form_name){
        $('form[name="'+form_name+'"] INPUT[type=checkbox]').attr('checked', true);
    }else{
        $('form INPUT[type=checkbox]').attr('checked', true);
    }
}
function UncheckAll(form_name){

    /*  if(form_name===undefined){
        var form_name = 'submit_form';
    } */

    if(form_name){
        $('form[name="'+form_name+'"] INPUT[type=checkbox]').attr('checked', false);
    }else{
        $('form INPUT[type=checkbox]').attr('checked', false);
    }
           
}



function enlarge_pic(sPicURL,title) { 
    msgwindow=window.open("enlarge_pic.php?url="+sPicURL+"&title="+title, "","resizable=1,scrollbars=1,HEIGHT=10,WIDTH=10"); 
} 

function product_photos(pid,id) { 
    msgwindow=window.open("product_photos.php?pid="+pid+"&id="+id, "product_photos","resizable=1,scrollbars=1,HEIGHT=10,WIDTH=10"); 
}


/* -------------- AJAX ------------------------ */

function ajax_check_register_username(str){
 
    $.post(
        "ajax.php",
        {
            action: "check_register_username" , 
            str: str
        },
        function(data){
            $('#register_username_area').html(data);
        }
        );
            

}

function ajax_check_register_email(str){
 
    $.post(
        "ajax.php",
        {
            action: "check_register_email" , 
            str: str
        },
        function(data){
            $('#register_email_area').html(data);
        }
        );

}


function get_saved_address(id,type){
 
    $('#address_loading_div').css('display', 'inline');
    $.post(
        "ajax.php",
        {
            action : "get_saved_address" , 
            id: id , 
            type: type , 
            sid: Math.random()
        },
        function(data){
            if(data){
                $('#info_name').val(data.name);
                $('#info_country').val(data.country);
                $('#info_city').val(data.city);  
                $('#info_address_1').val(data.address_1);
                $('#info_address_2').val(data.address_2);
                $('#info_telephone').val(data.tel);
            }           
            $('#address_loading_div').css('display', 'none');   
        },'json'
        );

}

function get_shipping_method_price(id){
    $('#shipping_method_price').empty();   
    $('#loading_div').css('display','inline');
    $.post('ajax.php', {
        action: 'shipping_method_price',
        id: id
    },
    function(data){
        $('#shipping_method_price').html(data);
        $('#loading_div').css('display','none');  
    });    

}


function cart_add_item(form_name){

    $('#'+form_name+' .cart_button').addClass('btn_loading');

    $.post( 'ajax.php', $('#'+form_name).serialize(),
        function( data ) {
      
            $('#'+form_name+' .cart_button').removeClass('btn_loading');
            $('#'+form_name+' .cart_button').addClass('btn_done');
            get_cart_items();
           
        }
        );
    return false;
}


function cart_item_options(form_name){
    var $dialog =  $('<div><img src="images/ajax_loading.gif"></div>').dialog({
        modal: true,
        width:'30%',
        height:'auto',
        close: function(ev, ui) {
            $(this).remove();
        }
    }); 
        
    $.post('ajax.php',$('#'+form_name).serialize(),
        function(data){
            $dialog.html(data);
            $dialog.dialog('option', 'position', 'center');
          
        });
          
}
    
function cart_delete_item(hash){
    $.post('ajax.php', {
        action: 'cart_delete_item',
        hash: hash
    },
    function(data){
        get_cart_items();    
    }
    );
}

function cart_clear(){
    $.post('ajax.php', {
        action: 'cart_clear'
    },
    function(data){
        get_cart_items();    
    });
}

function get_cart_items(){
    
    $('#cart_loading_div').css('display','inline');
    $.post('ajax.php', {
        action: 'get_cart_items'
    },
    function(data){
        $('#cart_div').html(data);
        $('#cart_loading_div').css('display','none');  
    });

}

//------------- Payment Method Details --------//
function show_payment_method_details(id,order_id){

    $('#payment_method_details_loading_div').css('display','inline');
    $('#payment_method_details_div').html('');

    $.post('ajax.php',{
        action: 'payment_method_details',
        id: id,
        order_id: order_id, 
        sid:Math.random()
    },
    function(data){
        $('#payment_method_details_div').html(data);
        $('#payment_method_details_loading_div').css('display','none');    
    }
    );
}


//------------- Payment Gateway Details --------//
function show_payment_gateway_details(id,order_id){

    $('#payment_gateway_details_loading_div').css('display','inline');
    $('#payment_gateway_details_div').html('');

    $.post('ajax.php',{
        action: 'payment_gateway_details',
        id: id,
        order_id: order_id, 
        sid:Math.random()
    },
    function(data){
        $('#payment_gateway_details_div').html(data);
        $('#payment_gateway_details_loading_div').css('display','none');    
    }
    ); 

}


//----------- Rating -------------------
function rating_init(type,id,rating,read_only,path){
    $('#'+type+id+'_rating_div').raty({
        start:     rating,
        showHalf:  true,
        readOnly: read_only,
        hintList:        ['1/5', '2/5', '3/5', '4/5', '5/5'],
        path: path+'/',
        onClick: function(score) {
            rating_send(type,id,score);
        }
    }); 
}
    
function rating_send(type,id,score){ 

    $('#'+type+id+'_rating_loading_div').show();
    $('#'+type+id+'_rating_status_div').hide();   

    new $.post('ajax.php',  {
        action:'rating_send',
        type: type,
        id: id,
        score: score
    },      
    function(data){

        $('#'+type+id+'_rating_status_div').html(data); 

        $('#'+type+id+'_rating_loading_div').hide(); 
        $('#'+type+id+'_rating_status_div').show(); 
  

    }); 
 
}



//------------- Reports ---------------------
function report(id,report_type){

    var $dialog =  $('<div id="report_dialog"><img src="images/ajax_loading.gif"></div>').dialog({
        modal: true,
        width:'30%',
        height:'auto',
        close: function(ev, ui) {
            $(this).remove();
        }
    }); 
        
    $.post('ajax.php',{
        action:'report',
        id: id,
        report_type: report_type
    },
    function(data){
        $dialog.html(data);
        $dialog.dialog('option', 'position', 'center');
          
    });
 
}


function report_send(){
    
    $('#send_button').disabled=true;

    $.post('ajax.php',$('#report_submit').serializeArray(),
        function(data){
            $('#report_dialog').html(data);
            $('#report_dialog').dialog('option', 'position', 'center');
          
        });
        


}



//---------- Comments Functions -------------------------------

function comments_add(type,id){
    $('#comment_add_button').attr('disabled','disabled');
    $('#comment_content').attr('disabled','disabled');
  
    $.post("ajax.php",
    {
        action:'comments_add',
        type: type,
        id: id,
        content: $('#comment_content').val()
    },
    function(data){
      
        $('#comment_add_button').removeAttr('disabled');
        $('#comment_content').removeAttr('disabled');

        if(data.status == 1){
            $('#comment_content').val(''); 
            $('#comment_content').focus();
            $('#no_comments').css('display','none');

            if(data.content == ""){
                //$('comment_status').innerHTML = json.msg;
                alert(data.msg); 
            }else{
                $('#comments_div').append(data.content);
            }
        }else{
            alert(data.msg);
        //$('comment_status').innerHTML = json.msg;
        }
    },'json');
    
}


function comments_delete(id){

    $.post("ajax.php",{
        action:'comments_delete', 
        id: id
    },function(){
        $('#comment_'+id).css('display','none');    
    });

}
    
var comments_offset = 1;

function comments_get(type,id){
    $('#comments_loading_div').css('display',''); 
    $('#comments_older_div').css('display','none');    

    $.post("ajax.php",{
        action:'comments_get',
        type: type,
        id: id,
        offset: comments_offset
    },function(data){
    
        $('#comments_div').append(data); 
        $('#comments_loading_div').css('display','none'); 
        comments_offset++;  
 
    });
}


function comments_init(){

    $('#content_mask').focus(function(){
        $('#comment_controls').css('display',''); 
        $('#content_mask').css('display',"none"); 
        $('#comment_content').css('display','');   
        $('#comment_content').focus();
    });

    $('#comment_content').blur(function(){
        if($('#comment_content').val().length == 0){
            $('#comment_controls').css('display',"none"); 
            $('#content_mask').css('display',''); 
            $('#comment_content').css('display',"none"); 
        }
    });
    
  
    $('#comment_content').keydown(function(){
        comments_remaining_letters();
    });
    $('#comment_content').keyup(function(){
        comments_remaining_letters();
    });
}


function comments_remaining_letters(){
    var len = $('#comment_content').val().length;
                             
    if (len > comments_max_letters) {
  
        $('#comment_content').val($('#comment_content').val().substring(0,comments_max_letters));
        len = comments_max_letters;
    }
    $('#remaining_letters').html((comments_max_letters - len));
    
}