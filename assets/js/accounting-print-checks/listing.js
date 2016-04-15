(function($, n){

    var table = $('#list'),
        getURL = table.data('get'),
        updateURL = table.data('update'),
        deleteURL = table.data('delete'),
        printURL = table.data('print'),
        deleteTemplate = '<a class="btn btn-flat btn-xs btn-danger delete">Delete</a>',
        actionTemplate = function(approved, id){
            return '<a href="'+printURL+id+'" class="btn btn-flat btn-xs btn-primary print">Print</a> ';
        },
        editTemplate = function(id){
            return '<a href="'+updateURL+id+'" class="btn btn-flat btn-xs btn-info">Edit</a> ';
        }
        fetchData = function(){
            var request = $.getJSON(getURL);
            request.done(function(response){
                var tr = [];
                $.each(response, function(i, v){
                    var td = [];
                    td[0] = v.created_at;
                    td[1] = $('select[name=banks]').find('option[value='+v.bank_account+']').text();
                    td[2] = v.payee;
                    td[3] = v.check_date;
                    td[4] = v.check_number;
                    td[5] = n(v.amount).format('0,0.00');
                    td[6] = editTemplate(v.id) + (actionTemplate(v.approved_by !== null, v.id)) + (isAdmin ? deleteTemplate : '') ;
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