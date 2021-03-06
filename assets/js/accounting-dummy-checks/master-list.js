(function($, n){

    var page=0;
    var params_global;
    var table = $('#list'),
        getURL = table.data('get'),
        updateURL = table.data('update'),
        deleteURL = table.data('delete'),
        printURL = table.data('print'),
        deleteTemplate = '<a class="btn btn-flat btn-xs btn-danger delete">Delete</a> ',
        actionTemplate = function(approved, id){
            return '<a href="'+printURL+id+'" class="btn btn-flat btn-xs btn-primary '+ (approved ? 'print' : 'disabled') +'">Print</a>';
        },
        fetchData = function(params){
            if(page==0){
                $('tbody tr:first-child').addClass('hidden');
            }
            var request = $.getJSON(getURL+'/'+(page*100), params);
            request.done(function(response){
                $('#btn-view-more').text('Loading...').addClass('disabled');
                var tr = [];
                console.log(response);
                $.each(response, function(i, v){
                    var td = [];
                    td[0] = '<a href="'+updateURL+v.id+'">'+v.id+'</a>';
                    td[1] = v.formatted_date;
                    td[2] = v.payee;
                    td[3] = n(v.check_amount).format('0,0.00');
                    // td[4] = v.approved_by !== null ? '<span class="label label-success">Approved</span>' : '<span class="label label-warning">Pending</span>';
                    td[4] = v.created_by;
                    td[5] = (isAdmin ? deleteTemplate : '') + (actionTemplate(v.approved_by !== null, v.id));
                    tr.push('<tr data-pk="'+v.id+'"><td>'+td.join('</td><td>')+'</td></tr>');
                })
                page++;
                table.find('tbody').append(tr.join(''));
                $('#btn-view-more').text('Click to view more').removeClass('disabled');
                if(response.length == 0 || response.length < 100){
                    table.find('tbody').append('<tr><td colspan="6" class="text-center">End of list. No more data to show</td></tr>');
                    $('tfoot').empty();
                }
                $('.print').printPage();
            });
            request.fail(function(){
                table.find('tbody').append('<tr><td colspan="6" class="text-center">An error has occured while triyng to fetch data. Please refresh the page to try again.</td></tr>');
            });
        },
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

        },

        getParams = function(){
            var form = $('#advanced-search');
            return {
                'check_number' : form.find('[name=check_number]').val(),
                'payee': form.find('[name=payee]').val(),
                'start_date': form.find('[name=start_date]').val(),
                'end_date': form.find('[name=end_date]').val()
            };
        };

    $(document).ready(function(){
        fetchData();
        table.on('click', '.delete', function(){
            if(confirm('Are you sure?')){
                remove($(this).closest('tr').data('pk'));
            }
        });
        table.on('click', '.print', function(){

        });
        $('#view-more-section').click(function(e){
            fetchData(params_global);
        });
        $('#advanced-search').submit(function(e){
            e.preventDefault();
            $('tbody').empty();
            page = 0;
            if(page==0){
                params_global = getParams();
            }
            fetchData(params_global);
        });
    });

})(jQuery, numeral)