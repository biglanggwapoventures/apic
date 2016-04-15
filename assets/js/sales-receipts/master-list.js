(function($, n){

    var masterListUrl = $('table#master-list').data('master-list-url'),
        updateUrl = $('table#master-list').data('edit-url'),
        deleteUrl = $('table#master-list').data('delete-url'),
        tbody = $('table#master-list tbody'),
        deleteTemplate = '<a class="btn btn-danger btn-flat btn-xs delete">Delete</a>',

        btnViewMore = '#btn-view-more'

        page = 1,

        goToUpdate = function(id){
            return updateUrl+id;
        },

        getData = function(params, overwrite){
            var request = $.getJSON(masterListUrl, params);
            request.done(function(response){
                populate(response.data, overwrite);
                $(btnViewMore).removeClass('disabled').text('Click to view more');
            });

            request.fail(function(){
                tbody.html('<tr><td colspan="7" class="text-center">An error has occured while triyng to fetch data. Please refresh the page to try again.</td></tr>');
            })
        },

        populate = function(data, overwrite){
            if(typeof data === 'undefined' || !data.length){
                tbody.append('<tr><td colspan="7" class="text-center">No more items to show.</td></tr>');
                $(btnViewMore).closest('tr').addClass('hidden');
                return;
            }
            var tr = [];
            $.each(data, function(i, v){
                var td = [];
                td[0] = '<a href="'+goToUpdate(v.id)+'">'+v.id+'</a>';
                td[1] = v.date;
                td[2] = v.customer;
                td[3] = v.tracking_number_type+'# '+v.tracking_number;
                td[4] = n(v.total_amount).format('0,0.00');
                td[5] = v.approved == 1 ? '<span class="label label-success">Approved</span>': '<span class="label label-warning">Pending</span>';
                td[6] = isAdmin ? deleteTemplate : '';
                tr.push('<tr data-pk="'+v.id+'"><td>'+td.join('</td><td>')+'</td></tr>');
            });
            var content = tr.join('');
            if(overwrite){
                tbody.html(content);
            }else{
                tbody.append(content);
            }
            if(data.length < 100){
                $(btnViewMore).closest('tr').addClass('hidden');
            }
        },

        deleteReceipt = function(){
            var confirmed = confirm('Are you sure?');
            if(!confirmed){
                return;
            }
            var tr = $(this).closest('tr'),
                id = tr.data('pk'), 
                request = $.post(deleteUrl, {id:id});
            request.done(function(response){
                if(response.error_flag){
                    $.growl.error({title:'Ooops', message:'Cannot delete selected sales receipt.'});
                    return;
                }
                tr.remove();
                $.growl.notice({title:'Done', message:'Sales receipt has been successfully deleted'});
            })

            request.fail(function(){
                $.growl.error({title:'Ooops', message:'An internal server error has occured. Please try again later'});
            })
        },

        nextPage = function(){
            $(this).addClass('disabled').text('Fetching data...');
            page++;
            getData(getSearchParams(), false);
        },

        getSearchParams = function(){
            var form = $('#advanced-search');
            return {
                'id' : form.find('[name=id]').val(),
                'customer': form.find('[name=customer]').val(),
                'start_date': form.find('[name=start_date]').val(),
                'end_date': form.find('[name=end_date]').val(),
                'tracking_no': form.find('[name=tracking_no]').val(),
                'tracking_type': form.find('[name=tracking_type]').val(),
                'page': page
            };
        }

    $(document).ready(function(){
        getData(getSearchParams(), true);
        $(btnViewMore).click(nextPage);
        tbody.on('click', '.delete', deleteReceipt);
        $('.datepicker').datepicker({dateFormat:'mm/dd/yy'});
        $('#advanced-search').submit(function(e){
            e.preventDefault();
            tbody.empty();
            $(btnViewMore).closest('tr').removeClass('hidden');
            page = 1;
            getData(getSearchParams(), true);
        });
    });

})(jQuery, numeral);