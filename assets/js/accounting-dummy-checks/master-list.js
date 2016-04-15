(function($, n){

    var table = $('#list'),
        getURL = table.data('get'),
        updateURL = table.data('update'),
        deleteURL = table.data('delete'),
        printURL = table.data('print'),
        deleteTemplate = '<a class="btn btn-flat btn-xs btn-danger delete">Delete</a> ',
        actionTemplate = function(approved, id){
            return '<a href="'+printURL+id+'" class="btn btn-flat btn-xs btn-primary '+ (approved ? 'print' : 'disabled') +'">Print</a>';
        },
        fetchData = function(){
            var request = $.getJSON(getURL);
            request.done(function(response){
                var tr = [];
                $.each(response, function(i, v){
                    var td = [];
                    td[0] = '<a href="'+updateURL+v.id+'">'+v.id+'</a>';
                    td[1] = v.formatted_date;
                    td[2] = v.payee;
                    td[3] = n(v.check_amount).format('0,0.00');
                    td[4] = v.approved_by !== null ? '<span class="label label-success">Approved</span>' : '<span class="label label-warning">Pending</span>';
                    td[5] = (isAdmin ? deleteTemplate : '') + (actionTemplate(v.approved_by !== null, v.id));
                    tr.push('<tr data-pk="'+v.id+'"><td>'+td.join('</td><td>')+'</td></tr>');
                })
                if(response.length){
                    table.find('tbody').html(tr.join(''));
                }else{
                    table.find('tbody').html('<tr><td colspan="5" class="text-center">No more data to display</td></tr>');
                }
                $('.print').printPage();
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
                $.growl.notice({title:'Done', message:'Dummy check has been successfully deleted!'});
            });
            request.fail(function(){
                $.growl.error({title:'Ooops', message:'An internal error has occured. Please try again later.'});
            })
        },

        printVoucher = function(){

        }

    $(document).ready(function(){
        fetchData();
        table.on('click', '.delete', function(){
            if(confirm('Are you sure?')){
                remove($(this).closest('tr').data('pk'));
            }
        });
        table.on('click', '.print', function(){

        });
    });

})(jQuery, numeral)