function init_blocks_sortlist(){
Sortable.create
(
    'blocks_list_r',{
tag:'div',
handle:'handle',
containment:["blocks_list_r","blocks_list_c","blocks_list_l"],
        constraint: false,
        onUpdate: function()
        {
      new Ajax.Updater
            (
                'result', 'ajax.php',
                { postBody: Sortable.serialize('blocks_list_r') +'&action=set_blocks_sort'}
            );
        }
    }
);
Sortable.create
(
    'blocks_list_c',{
tag:'div',
handle:'handle',
containment:["blocks_list_r","blocks_list_c","blocks_list_l"],
        constraint: false,
        onUpdate: function()
        {
            new Ajax.Updater
            (
                'result', 'ajax.php',
                { postBody: Sortable.serialize('blocks_list_c') +'&action=set_blocks_sort'}
            );
        }
    }
);

Sortable.create
(
    'blocks_list_l',{
tag:'div',
handle:'handle',
containment:["blocks_list_r","blocks_list_c","blocks_list_l"],
        constraint: false,
             onUpdate: function()
        {
            new Ajax.Updater
            (
                'result', 'ajax.php',
                { postBody: Sortable.serialize('blocks_list_l') +'&action=set_blocks_sort'}
            );
        }
    }
);

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
                { postBody: Sortable.serialize('new_stores_list',{name:'sort_list'}) +'&action=set_new_stores_sort'}
            );
        }
    }
);
}










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
                { postBody: Sortable.serialize(div_name,{name:'sort_list'}) +'&action='+action_name}
            );

        }
    }
);

}
