<style type="text/css">
    #master-list tbody tr td:nth-child(4){
        text-align: right;
    }
</style>
<?php $url = base_url('sales/counter_receipts'); ?>
<div class="box box-solid">
    <div class="box-header bg-light-blue-gradient" style="color:#fff">
        <h3 class="box-title">Master List</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
            <!-- button with a dropdown -->
            <div class="btn-group">
                <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li><a href="<?= "{$url}/create"?>">Create new counter receipt</a></li>
                    <li><a data-toggle="modal" data-target="#advanced-search-modal">Advanced search</a></li>
                </ul>
            </div>                 
        </div><!-- /. tools -->
    </div><!-- /.box-header -->

    <div class="box-body no-padding">
        <table class="table table-striped" id="master-list"
            data-master-list-url="<?= "{$url}/master_list"?>"
            data-edit-url="<?= "{$url}/edit"?>/"
            data-delete-url="<?= "{$url}/delete"?>"
            data-print-url="<?= "{$url}/do_print"?>/"> 
            <thead><tr class="info"><th>CR #</th><th>Date</th><th>Customer</th><th>Amount</th><th>Status</th><th>CB</th><th>AB</th><th></th></tr></thead>
            <tbody>
                <tr><td colspan="8" class="text-center">Loading data. Please wait...</td></tr>
            </tbody>
            <tfoot>
                
                <tr class="hidden"><td id="view-more-section" colspan="8" class="text-center"><span class="notification"></span><button id="btn-view-more" class="btn btn-flat btn-xs btn-default" type="button">Click to view more</button></td></tr>
            </tfoot>
        </table>
    </div><!-- /.box-body -->  
</div>
<div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="mySmallModalLabel">Confirm action</h4>
            </div>
            <div class="modal-body">
                <p class="text-danger text-center text-bold">Do you really want to delete this customer?<br> <u>This action cannot be undone.<u></p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-flat btn-danger" id="delete-confirmed" data-delete-url="<?= "{$url}/delete/"?>">Yes</a>
                <a data-dismiss="modal" class="btn btn-flat btn-default">No</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="advanced-search-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mySmallModalLabel">Advanced search</h4>
            </div>
            <form id="advanced-search">
                <div class="modal-body">
                    <div class="form-group">
                        <label>CR#</label>
                        <input type="number" class="form-control" name="id" placeholder="CR#">
                    </div>
                    <div class="form-group">
                        <label>Start date</label>
                        <input type="text" class="form-control datepicker" name="start_date" placeholder="Start date">
                    </div>
                    <div class="form-group">
                        <label>End date</label>
                        <input type="text" class="form-control datepicker" name="end_date" placeholder="End date">
                    </div>
                    <div class="form-group">
                        <label>Customer</label>
                        <?= form_dropdown('customer', $customers,  FALSE, 'class="form-control" id="search-customer"')?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    var masterListUrl = $('table#master-list').data('master-list-url'),
    updateUrl = $('table#master-list').data('edit-url'),
    deleteUrl = $('table#master-list').data('delete-url'),
    printUrl = $('table#master-list').data('print-url'),
    tbody = $('table#master-list tbody'),
    deleteTemplate = '<a class="btn btn-danger btn-flat btn-xs delete">Delete</a>',

    printTemplate = function(id){
        return '<a class="btn btn-primary btn-flat btn-xs print" href="'+printUrl+id+'">Print</a> ';
    },

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
            td[1] = v.customer;
            td[2] = moment(v.date).format('MMM-DD-YYYY')
            td[3] = numeral(v.amount).format('0,0.00')
            td[4] = v.approved_by ? '<span class="label label-success">Approved</span>': '<span class="label label-warning">Pending</span>';
            td[5] = v.created_by
            td[6] = v.approved_by
            td[7] = (!v.approved_by && isAdmin ? deleteTemplate : '') + (v.approved_by? printTemplate(v.id) : '');
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
                $.growl.error({title:'Error!', message:response.message[0]});
                return;
            }
            tr.remove();
            $.growl.notice({title:'Done!', message:'Receiving report has been successfully deleted'});
        })

        request.fail(function(){
            $.growl.error({title:'Error!', message:'An internal server error has occured. Please try again later'});
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
            'start_date': form.find('[name=start_date]').val(),
            'end_date': form.find('[name=end_date]').val(),
            'customer': form.find('[name=customer]').val(),
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
        $('.datepicker').datepicker({dateFormat:'M-dd-yy'})
        $('#master-list').stickyTableHeaders({fixedOffset: $('.content-header')});
    });
</script>