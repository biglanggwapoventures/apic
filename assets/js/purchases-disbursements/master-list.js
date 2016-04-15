(function($, n){

    var masterListUrl = $('table#master-list').data('master-list-url'),
        updateUrl = $('table#master-list').data('edit-url'),
        deleteUrl = $('table#master-list').data('delete-url'),
        printUrl = $('table#master-list').data('print-url'),
        tbody = $('table#master-list tbody'),
        deleteTemplate = '<a class="btn btn-danger btn-flat btn-xs delete">Delete</a>',

        printTemplate = '<a class="btn btn-primary btn-flat btn-xs" data-toggle="modal" data-target="#print">Print</a> ';

        btnViewMore = '#btn-view-more'

        page = 1,

        goToUpdate = function(id){
            return updateUrl+id;
        },

        getData = function(params, overwrite){
            var request = $.getJSON(masterListUrl, params);
            request.done(function(response){
                // console.log(response)
                populate(response.data, overwrite);
                $(btnViewMore).removeClass('disabled').text('Click to view more');
            });

            request.fail(function(){
                tbody.html('<tr><td colspan="9" class="text-center">An error has occured while triyng to fetch data. Please refresh the page to try again.</td></tr>');
            })
        },

        populate = function(data, overwrite){
            if(typeof data === 'undefined' || !data.length){
                tbody.append('<tr><td colspan="9" class="text-center">No more items to show.</td></tr>');
                $(btnViewMore).closest('tr').addClass('hidden');
                return;
            }
            var tr = [];
            $.each(data, function(i, v){
                var td = [];
                td[0] = '<a href="'+goToUpdate(v.id)+'">'+v.id+'</a>';
                td[1] = v.date;
                td[2] = $('#supplier-list').find('option[value='+v.supplier_id+']').text();
                td[3] = v['type'];
                td[4] = v.check_number;
                if(v.check_amount){
                    if(parseFloat(v.check_amount) < parseFloat(v.amount)){
                        td[5] = '<span class="text-danger text-bold">'+n(v.check_amount).format('0,0.00')+'</span>'
                    }else{
                        td[5] = n(v.check_amount).format('0,0.00');
                    }
                }else{
                    td[5] = v.check_amount;
                }
                td[6] = n(v.amount).format('0,0.00');
                td[7] = v['status'] === 'Approved' ? '<span class="label label-success">Approved</span>': '<span class="label label-warning">Pending</span>';
                td[8] = printTemplate + (isAdmin ? deleteTemplate : '');
                tr.push('<tr data-pk="'+v.id+'" data-po="'+v.po_id+'" data-type="'+v.disbursement_type+'"><td>'+td.join('</td><td>')+'</td></tr>');
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
                'check_number': form.find('[name=check_number]').val(),
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
        $('#print').on('show.bs.modal', function(e){
            var row = $(e.relatedTarget).closest('tr'),
                id = row.data('pk'),
                po_id = row.data('po'),
                type = row.data('type');

            $('.print').attr('href', function(){
                return $(this).data('url')+id+'&po='+po_id+'&disbursement_type='+type;
            }).unbind().printPage();  
        });
        $('.datepicker').datepicker({dateFormat:'mm/dd/yy'})
        $('table.promix').stickyTableHeaders({fixedOffset: $('.content-header')});
    });

})(jQuery, numeral);