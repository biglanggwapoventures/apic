(function($, n){

    var masterListUrl = $('table#master-list').data('master-list-url'),
        updateUrl = $('table#master-list').data('edit-url'),
        deleteUrl = $('table#master-list').data('delete-url'),
        printUrl = $('table#master-list').data('print-url'),
        yieldUrl = $('table#master-list').data('yield-url'),
        tbody = $('table#master-list tbody'),
        deleteTemplate = '<a class="btn btn-danger btn-flat btn-xs delete">Delete</a>',

        printTemplate = function(id){
            return '<a class="btn btn-primary btn-flat btn-xs print" href="'+printUrl+id+'">Print</a> ';
        },

        yieldTemplate = function(id){
            return '<a class="btn btn-default btn-flat btn-xs" data-pk="'+id+'" data-toggle="modal" data-target="#choose-yield-type">Process</a> ';
        },

        goToYield = function(id, yieldType){
            var type = yieldType === 'ltd' ? 'live-to-dressed' : 'dressed-to-cutups';
            return '<a class="btn btn-default btn-flat btn-xs" href="'+yieldUrl+id+'&type='+type+'">Process</a> ';
        },

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
                td[1] = v.po_id;
                td[2] = v.date;
                td[3] = $('#supplier-list').find('option[value='+v.supplier_id+']').text();
                td[4] = v.dr_si;
                td[5] = n(v.amount).format('0,0.00');
                td[6] = v.status === 'Approved' ? '<span class="label label-success">Approved</span>': '<span class="label label-warning">Pending</span>';
                td[7] = (v.status === 'Approved' ? yieldTemplate(v.id) + printTemplate(v.id)  : '') +(isAdmin ? deleteTemplate : '');
                tr.push('<tr data-pk="'+v.id+'"><td>'+td.join('</td><td>')+'</td></tr>');
                console.log(v.id+' '+v.yielding_type)
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
            $('.print').unbind().printPage();
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

        $('#choose-yield-type').on('show.bs.modal', function(e){
            var rrId = $(e.relatedTarget).data('pk')
            $(this).find('a').attr('href', function(){
                return $(this).data('href')+'&rr='+rrId;
            })
        });
    });

})(jQuery, numeral);