
function banner_pop_open(url,name){
    msgwindow=window.open(url,name,"toolbar=yes,scrollbars=yes,resizable=yes,width=650,height=300,top=200,left=200");
}

function banner_pop_close(url,name){
    msgwindow=window.open(url,name,"toolbar=yes,scrollbars=yes,resizable=yes,width=650,height=300,top=200,left=200");
}


function snd(id)
{
    msgwindow=window.open("send2friend.php?id="+id,"displaywindow","toolbar=no,scrollbars=no,width=400,height=320,top=200,left=200")
}



function add2fav(id)
{
    msgwindow=window.open("add2fav.php?id="+id,"displaywindow","toolbar=no,scrollbars=no,width=350,height=150,top=200,left=200")
}


function CheckAll(form_id)
{

    count = document.getElementById(form_id).elements.length;
    for (i=0; i < count; i++) 
    {
        if((document.getElementById(form_id).elements[i].checked == 1) ||(document.getElementById(form_id).elements[i].checked == 0))
        {
            document.getElementById(form_id).elements[i].checked = 1;
        }
  
    }
}
function UncheckAll(form_id){
    count = document.getElementById(form_id).elements.length;
    for (i=0; i < count; i++) 
    {
        if((document.getElementById(form_id).elements[i].checked == 1) || (document.getElementById(form_id).elements[i].checked == 0))
        {
            document.getElementById(form_id).elements[i].checked = 0;
        }

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
     
            $('#info_name').val(data.name);
            $('#info_country').val(data.country);
            $('#info_city').val(data.city);  
            $('#info_address_1').val(data.address_1);
            $('#info_address_2').val(data.address_2);
            $('#info_telephone').val(data.tel);
                                             
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

    $('#'+type+id+'_rating_loading_div').css('display', 'inline');
    $('#'+type+id+'_rating_status_div').css('display', 'none');   

    new $.post('ajax.php',  {
        action:'rating_send',
        type: type,
        id: id,
        score: score
    },      
    function(data){

        $('#'+type+id+'_rating_status_div').html(data); 

        $('#'+type+id+'_rating_loading_div').css('display', 'none'); 
        $('#'+type+id+'_rating_status_div').css('display', 'inline');; 
  

    }); 
 
}