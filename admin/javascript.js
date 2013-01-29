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



function show_banners_options(){

    nms = $('select#type').val();

    if (nms == 'menu') {
        $("#add_after_menu").show();
        $("#banners_pages_area").show();
        $("#bnr_content_type").show();

        show_banner_img_or_code();

    }else if(nms == 'offer') {
        $("#add_after_menu").hide();
        $("#banners_pages_area").hide();
        $("#bnr_content_type").hide();

        $("#banners_code_area").show();
        $("#banners_img_area").show();
        $("#banners_url_area").show();


    }else{
        $("#add_after_menu").hide();
        $("#banners_pages_area").show();
        $("#bnr_content_type").show();

        show_banner_img_or_code();
    }

}



function show_banner_img_or_code(){

    if($('#c_type').val()=="code"){
        $("#banners_code_area").show();
        $("#banners_img_area").hide();
        $("#banners_url_area").hide()

    }else{

        $("#banners_code_area").hide();

        $("#banners_img_area").show();
        $("#banners_url_area").show()
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
            {
                document.submit_form.elements[i].checked = 1;
            }
  
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
        document.getElementById('preview_text_tr').hide();
    }else{
        document.getElementById('preview_text_tr').show();
    }
}


function show_snd_mail_options(box){
    nms = box.options[box.selectedIndex].value;

    if (nms == 'all') {
        $("#when_one_user_email").hide();
    }else{
        $("#when_one_user_email").show();
    }
}

function show_snd_mail_options2(box){
    nms = box.options[box.selectedIndex].value;

    if (nms == 'msg') {
        $("#sender_email_tr").hide();
        $("#msg_type_tr").hide();
        $("#msg_encoding_tr").hide();
    }else{
        $("#sender_email_tr").show();
        $("#msg_type_tr").show();
        $("#msg_encoding_tr").show();
 
    }
}

function show_uploader_options(box){

    if (box == '1') {
        $("#file_field").hide();
        $("#url_field").style.display ="inline";

    }else{
        $("#file_field").show();
        $("#url_field").style.display =  "none";
 
    }
}





function show_hide_fields_divs(value){
    if(value=="text"){
        $('#fields_default_value_div').show();
        $('#fields_options_div').hide();
    }else{
        $('#fields_default_value_div').hide();
        $('#fields_options_div').show();
    }
}


function set_status_text_display(value){
        
    if(value==0){
        $('#status_text').show();
    }else{   
        $('#status_text').hide();
    }
}
    
    
function set_payment_method_text_display(value){
        
    if(value==0){
        $('#payment_method_name').show();
    }else{   
        $('#payment_method_name').hide();
    }
}
    
function set_shipping_method_text_display(value){
        
    if(value==0){
        $('#shipping_method_name').show();
    }else{   
        $('#shipping_method_name').hide();
    }
}
    
/* -------------- AJAX -------------------------*/

function init_blocks_sortlist(){
    $(".blocks_group" ).sortable({
        connectWith: ".blocks_group",
        update: function(event,ui) { 
                            
            var $sortable = $(this);

            // To avoid double-firing the event, return if it's not the sortable
            // where the item was dropped into.
            if(ui.item.parent()[0] != this) return;

            // Create object from the current sortable to post
            var postData = {};
            postData['action'] = 'set_blocks_sort';
            postData['blocks'] = {};
            postData['blocks'][$sortable.attr('id')] =$sortable.sortable('serialize');
        

            // If the item came from a connected sortable, include that in the post too
            if(ui.sender){
                postData['blocks'][ui.sender.attr('id')] = ui.sender.sortable('serialize');
    
            } 
                
            $.post("ajax.php",postData,function(data){});	
        }
    });
}


function init_sortlist(div_name,op){
   
    $('#'+div_name).sortable({
        update: function(event,ui) { 
             $.post("ajax.php",{
             action : 'set_sort',
             op: op,
             list : $(this).sortable('serialize')
           });	
        }
        
        });
}



function init_new_stores_sortlist(){
    Sortable.create
    (
        'new_stores_list',{
            tag:'div',
            constraint: false,
            onUpdate: function()
            {
                new Ajax.Updater
                (
                    'result', 'ajax.php',
                    {
                        postBody: Sortable.serialize('new_stores_list',{
                            name:'sort_list'
                        }) +'&action=set_new_stores_sort'
                    }
                    );
            }
        }
        );
}

function init_dynatree(div_id,input_id){
      $("#"+div_id).dynatree({
                    checkbox: true,
                    selectMode: 3,
                    onCreate: function(node, nodeSpan) {
                        tree_get_selected_nodes(node);	
                    },
                    onSelect: function(select, node) {
                        tree_get_selected_nodes(node);
                    },
                    onDblClick: function(node, event) {
                        node.toggleSelect();
                    },
                    onKeydown: function(node, event) {
                        if( event.which == 32 ) {
                            node.toggleSelect();
                            return false;
                        }
                    }
                });
                function tree_get_selected_nodes(node){
                    var selRootKeys = $.map(node.tree.getSelectedNodes(true), function(node){
                        return node.data.key;
                    });
                    $("#"+input_id).val(selRootKeys.join(","));
                }
    }
    
/*

function init_sortlist(div_name,action_name){
    Sortable.create
    (
        div_name,{
            tag:'div',
            handle:'handle',
            constraint: false,
            onUpdate: function()
            {
                new Ajax.Updater
                (
                    'result', 'ajax.php',
                    {
                        postBody: Sortable.serialize(div_name,{
                            name:'sort_list'
                        }) +'&action='+action_name
                    }
                    );

            }
        }
        );

}*/