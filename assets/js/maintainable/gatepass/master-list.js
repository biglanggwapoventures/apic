(function($){

    var table = $('#list'),
        getURL = table.data('get'),
        updateURL = table.data('update'),
        deleteURL = table.data('delete'),
        printURL = table.data('print'),
        actionTemplate = '<a class="btn btn-flat btn-xs btn-danger delete">Delete</a> <a class="btn btn-flat btn-xs btn-primary print">Print</a>',
        fetchData = function(){
            var request = $.getJSON(getURL);
            request.done(function(response){
                var tr = [];
                $.each(response, function(i, v){
                    var td = [];
                    td[0] = '<a href="'+updateURL+v.id+'">'+v.id+'</a>';
                    td[1] = v.formatted_date;
                    td[2] = v.type === 'pl' ? 'Packing List' : 'Others';
                    td[3] = v.type === 'pl' ? v.customer : v.issued_to;
                    td[4] = v.created_by;
                    td[5] = actionTemplate;
                    tr.push('<tr data-pk="'+v.id+'"><td>'+td.join('</td><td>')+'</td></tr>');
                })
                if(response.length){
                    table.find('tbody').html(tr.join(''));
                    $('.print').each(function () {
                        $(this).printPage({
                            url: printURL+$(this).closest('tr').data('pk')
                        });
                    });
                }else{
                    table.find('tbody').html('<tr><td colspan="5" class="text-center">No more data to display</td></tr>');
                }
                
            });
            request.fail(function(){
                
            });
        }
        remove = function(id){
            var request = $.post(deleteURL, {id:id});
            request.done(function(response){
                if(response.error_flag){
                    $.growl.error({title:'Ooops', message:response.message});
                    return;
                }
                $('tr[data-pk="'+id+'"]').remove();
                $.growl.notice({title:'Done', message:'Gatepass has been successfully deleted!'});
            });
            request.fail(function(){
                $.growl.error({title:'Ooops', message:'An internal error has occured. Please try again later.'});
            })
        }

    $(document).ready(function(){
        fetchData();
        table.on('click', '.delete', function(){
            if(confirm('Are you sure?')){
                remove($(this).closest('tr').data('pk'));
            }
        });
    });

})(jQuery)