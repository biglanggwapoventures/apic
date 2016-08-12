(function($, n){

    var masterListUrl = $('table#master-list').data('master-list-url'),
        updateUrl = $('table#master-list').data('edit-url'),
        printUrl = $('table#master-list').data('print-url'),
        deleteUrl = $('table#master-list').data('delete-url'),
        tbody = $('table#master-list tbody'),
        deleteTemplate = '<a class="btn btn-danger btn-flat btn-xs delete"><i class="fa fa-times"></i></a>',
        deleteTemplateDisabled = '<a class="btn btn-danger btn-flat btn-xs delete disabled"><i class="fa fa-times"></i></a>',
        btnViewMore = '#btn-view-more'

        page = 1,

        goToUpdate = function(id){
            return updateUrl+id;
        },
        goToPrint = function(id){
            return printUrl+id;
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
                td[2] = v.company;
                td[3] = v.trucking;
                td[4] = v.trucking_assistant;
                if(v.trip_type == 1) td[5] = '<span>Chick Van</span>'; else if(v.trip_type == 2) td[5] = '<span>Harvester</span>'; else td[5] = '<span>Dressed Chicken</span>';
                td[6] = v.approved_by != null ? '<span class="label label-success">Approved</span>': '<span class="label label-warning">Pending</span>';
                td[7] = v.approved_by != null ? deleteTemplateDisabled : '<a href="'+goToPrint(v.id)+'" class="btn btn-xs btn-flat btn-default print"><i class="fa fa-print"></i></a>'+deleteTemplate;
                if(isAdmin)
                    td[7] = '<a href="'+goToPrint(v.id)+'" class="btn btn-xs btn-flat btn-default print"><i class="fa fa-print"></i></a>'+ deleteTemplate;
                // td[8] = isAdmin ? deleteTemplate : '';

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
                    $.growl.error({title:'Ooops', message:'Cannot delete selected trip ticket receipt.'});
                    return;
                }
                tr.remove();
                $.growl.notice({title:'Done', message:'Trip ticket has been successfully deleted'});
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
                'fk_sales_customer_id' : form.find('[name=fk_sales_customer_id]').val(),
                'trip_type': form.find('[name=trip_type]').val(),
                'start_date': form.find('[name=start_date]').val(),
                'end_date': form.find('[name=end_date]').val(),
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

        $('table').stickyTableHeaders({fixedOffset: $('.content-header')});

        $('.print').printPage();
    });

})(jQuery);