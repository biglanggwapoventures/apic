(function($){

    var masterListUrl = $('table#master-list').data('master-list-url'),
        updateUrl = $('table#master-list').data('edit-url'),
        deleteUrl = $('table#master-list').data('delete-url'),
        tbody = $('table#master-list tbody'),
        deleteTemplate = '<a class="btn btn-danger btn-flat btn-xs delete">Delete</a>',

        goToUpdate = function(id){
            return updateUrl+id;
        },

        getData = function(params, overwrite){
            var request = $.getJSON(masterListUrl, params);
            request.done(function(response){
                // console.log(response)
                populate(response.data, overwrite);
            });

            request.fail(function(){
                tbody.html('<tr><td colspan="9" class="text-center">An error has occured while triyng to fetch data. Please refresh the page to try again.</td></tr>');
            })
        },

        populate = function(data, overwrite){
            if(typeof data === 'undefined' || !data.length){
                tbody.append('<tr><td colspan="9" class="text-center">No more items to show.</td></tr>');
                return;
            }
            var tr = [];
            $.each(data, function(i, v){
                var td = [];
                td[0] = '<a href="'+goToUpdate(v.id)+'">'+v.formulation_code+'</a>';
                td[1] = parseInt(v['status']) ? '<span class="label label-success">Active</span>': '<span class="label label-warning">Inactive</span>';
                td[2] = isAdmin ? deleteTemplate : '';
                tr.push('<tr data-pk="'+v.id+'"><td>'+td.join('</td><td>')+'</td></tr>');
            });
            var content = tr.join('');
            if(overwrite){
                tbody.html(content);
            }else{
                tbody.append(content);
            }
        },

        deleteItem = function(){
            var confirmed = confirm('Are you sure?');
            if(!confirmed){
                return;
            }
            var tr = $(this).closest('tr'),
                id = tr.data('pk'), 
                request = $.post(deleteUrl, {pk:id});
            request.done(function(response){
                if(response.error_flag){
                    $.growl.error({title:'Ooops', message:'Cannot delete selected voucher.'});
                    return;
                }
                tr.remove();
                $.growl.notice({title:'Done', message:'Voucher has been successfully deleted'});
            })

            request.fail(function(){
                $.growl.error({title:'Ooops', message:'An internal server error has occured. Please try again later'});
            })
        }
        getSearchParams = function(){
            var form = $('#advanced-search');
            return {
                'formulation_code' : form.find('[name=formulation_code]').val(),
                'status': form.find('[name=status]').val()
            };
        }

    $(document).ready(function(){
        
        getData(getSearchParams(), true);
        tbody.on('click', '.delete', deleteItem);
        $('#advanced-search').submit(function(e){
            e.preventDefault();
            tbody.empty();
            getData(getSearchParams(), true);
        });
        
        $('table.promix').stickyTableHeaders({fixedOffset: $('.content-header')});
    });

})(jQuery);