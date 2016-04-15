<style type="text/css">
    table tbody tr td:nth-child(4), table thead tr th:nth-child(4){
        text-align: right;
    }
    table tbody tr td:nth-child(5), table thead tr th:nth-child(5){
        text-align: center;
    }
</style>
<?php $url = base_url('purchases/orders'); ?>
<div class="box box-solid">
    <div class="box-header bg-light-blue-gradient" style="color:#fff">
        <h3 class="box-title">Master List</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
            <!-- button with a dropdown -->
            <div class="btn-group">
                <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li><a href="<?= $url . '/manage?do=new-purchase-order' ?>">Add new purchase order</a></li>
                    <li><a href="#" data-toggle="modal" data-target=".advanced-search-modal">Advanced search</a></li>
                </ul>
            </div>                 
        </div><!-- /. tools -->
    </div><!-- /.box-header -->
    <div class="box-body no-padding" style="display: block;">
        <div class="row">
            <div class="col-xs-12">

                <table id="master-list" class="table table-striped table-condensed promix" data-edit-url="<?= $url . '/manage?do=update-purchase-order&id=' ?>"
                       data-master-list-url='<?= $url . '/ajax_master_list' ?>'
                       data-print-url='<?= $url . '/do_print?id=' ?>'
                       data-delete-url='<?= $url . '/a_do_action/delete' ?>'>
                    <thead>
                        <tr class="info">
                            <th>PO#</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="8" class="text-center">Loading data. Please wait...</td></tr>
                    </tbody>
                    <tfoot>
                        
                        <tr class="hidden"><td id="view-more-section" colspan="8" class="text-center"><span class="notification"></span><button id="btn-view-more" class="btn btn-flat btn-xs btn-default" type="button">Click to view more</button></td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div><!-- /.box-body -->  
</div>

<div class="modal fade advanced-search-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mySmallModalLabel">Advanced search</h4>
            </div>
            <form id="advanced-search">
                <div class="modal-body">
                    <div class="form-group">
                        <label>PO#</label>
                        <input type="number" class="form-control" name="id" placeholder="PO#">
                    </div>
                    <div class="form-group">
                        <label>Supplier</label>
                        <?= form_dropdown('supplier', $suppliers, FALSE, 'class="form-control" id="supplier-list"')?>
                    </div>
                    <div class="form-group">
                        <label>Start date</label>
                        <input type="text" class="form-control datepicker" name="start_date" placeholder="Start date">
                    </div>
                    <div class="form-group">
                        <label>End date</label>
                        <input type="text" class="form-control datepicker" name="end_date" placeholder="End date">
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
