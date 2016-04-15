(function($) {

    var table = 'table#rr-master-list',
        updateURL = $(table).data('update-url'),

        statusTemplate = '<a class="btn btn-danger btn-flat btn-xs delete" role="button">Delete</a>';

        fetchData = function(){
            var request = $.getJSON($(table).data('master-list-url'));
            request.done(function(response){
                if(response.data.length === 0){
                    $(table).find('tbody').html('<tr><td colspan="5" class="text-center">No items to display</tr>');
                }else{
                    populate(response.data);
                }
            });
        },

        remove = function(id){
            var confirmed = confirm('Are you sure?');
            if(!confirmed){
                return;
            }
            var request = $.post($(table).data('delete-url'), {id:id});
            request.done(function(response){
                if(response.error_flag){
                    $.growl.error({title:'Ooops!', message: 'Failed to delete the receiving report. Please try again later.'});
                }else{
                    $(table).find('tbody tr[data-pk='+id+']').remove();
                }
            });
        }, 

        populate = function(data, wipe){
            var tbody = $(table).find('tbody'),
                row = [],
                content;
            $.each(data, function(i, v){
                var td = [];
                td[0] = '<td><a href="'+updateURL+v.id+'">'+v.id+'</a>';
                td[1] = v.datetime;
                td[2] = v.production_code;
                td[3] = v.approved_by !== null ? '<span class="label label-success">Approved</span>': '<span class="label label-warning">Pending</span>';
                td[4] = statusTemplate+'</td>';
                row.push('<tr data-pk="'+v.id+'">'+td.join('</td><td>')+'</tr>');
            });
            content = row.join('');
            if(wipe){
                tbody.html(content);
            }else{
                tbody.append(content);
            }
            
        };
        
    $(document).ready(function(){
        fetchData();
        $(table).on('click', '.delete', function(){
            remove($(this).closest('tr').data('pk'));
        });
    });

}(jQuery));