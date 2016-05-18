(function($){

    var masterListUrl = $('table#master-list').data('master-list-url'),
        updateUrl = $('table#master-list').data('edit-url'),
        update2Url = $('table#master-list').data('edit2-url'),
        deleteUrl = $('table#master-list').data('delete-url'),
        tbody = $('table#master-list tbody'),
        deleteTemplate = '<a class="btn btn-danger btn-flat btn-xs delete">Delete</a>',

        btnViewMore = '#btn-view-more'

        page = 1,

        goToUpdate = function(id, rrId, yieldType){
            if(rrId && yieldType){
                return update2Url.replace('_rr_', rrId).replace('_type_', (yieldType === 'dtc' ? 'dressed-to-cutups' : 'live-to-dressed'))
            }
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
                td[0] = '<a href="'+goToUpdate(v.id, v.fk_purchase_receiving_id, v.yield_type)+'">'+v.id+'</a>';
                td[1] = moment(v.created_at).format('MMM-DD-YYYY');
                td[2] = v.yield_type_description;
                td[3] = v.created_by;
                td[4] = (isAdmin ? deleteTemplate : '');
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
            }else{
                $(btnViewMore).closest('tr').removeClass('hidden');
            }
        },

        deleteItem = function(){
            if(confirm('Are you sure?')){
                return;
            }
            var tr = $(this).closest('tr'),
                id = tr.data('pk'), 
                request = $.post(deleteUrl, {pk:id});
            request.done(function(response){
                if(response.error_flag){
                    $.growl.error({title:'Ooops', message:'Cannot delete selected receiving report.'});
                    return;
                }
                tr.remove();
                $.growl.notice({title:'Done', message:'Receiving report has been successfully deleted'});
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
                'supplier': form.find('[name=supplier]').val(),
                'start_date': form.find('[name=start_date]').val(),
                'end_date': form.find('[name=end_date]').val(),
                'po_id': form.find('[name=po_id]').val(),
                'dr_si': form.find('[name=dr_si]').val(),
                'page': page
            };
        }

    $(document).ready(function(){
        getData(getSearchParams(), true);
        $(btnViewMore).click(nextPage);
        tbody.on('click', '.delete', deleteItem);
        $('#advanced-search').submit(function(e){
            e.preventDefault();
            tbody.empty();
            $(btnViewMore).closest('tr').removeClass('hidden');
            page = 1;
            getData(getSearchParams(), true);
        });
        $('.datepicker').datepicker({dateFormat:'mm/dd/yy'})
        $('table.promix').stickyTableHeaders({fixedOffset: $('.content-header')});
    });

})(jQuery);