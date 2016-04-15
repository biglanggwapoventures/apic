<style type="text/css">
    table.promix tbody td:nth-child(6),
    table.promix thead th:nth-child(6){
        text-align: right;
    }
    table.promix tbody td:nth-child(7),
    table.promix thead th:nth-child(7){
        text-align: center;
    }
</style>
<?php $url = base_url('purchases/other_disbursements'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff">
                <h3 class="box-title">Master List</h3>
                <!-- tools box -->
                <div class="pull-right box-tools">
                    <!-- button with a dropdown -->
                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li><a href="<?= $url . '/manage?do=new-check-voucher' ?>">Add new voucher</a></li>
                            <li><a href="#" data-toggle="modal" data-target=".advanced-search-modal">Advanced search</a></li>
                        </ul>
                    </div>                 
                </div><!-- /. tools -->
            </div><!-- /.box-header -->
            <div class="box-body no-padding" style="display: block;">
                <div class="row">
                    <div class="col-xs-12">
                        <table id="master-list" class="table table-striped table-condensed promix" data-edit-url="<?= $url . '/manage?do=update-check-voucher&id=' ?>"
                               data-master-list-url='<?= $url . '/ajax_master_list' ?>'
                               data-delete-url='<?= $url . '/a_do_action/delete' ?>'>
                            <thead>
                                <tr class="info"><th style="width:5%">CV#</th><th style="width:9%">Date</th><th>Pay to</th><th>Remarks</th><th>Check #</th><th>Amount</th><th>Status</th><th style="width:123px"></th></tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="9" class="text-center">Loading data. Please wait...</td></tr>
                            </tbody>
                            <tfoot>
                                <tr class="hidden"><td id="view-more-section" colspan="9" class="text-center"><span class="notification"></span><button id="btn-view-more" class="btn btn-flat btn-xs btn-default" type="button">Click to view more</button></td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div><!-- /.box-body -->  

        </div>
    </div>
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
                        <label>CV#</label>
                        <input type="number" class="form-control" name="id" placeholder="CV#">
                    </div>
                    <div class="form-group">
                        <label>Payee</label>
                        <input type="text" class="form-control" name="payee" placeholder="Payee">
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <input type="text" class="form-control" name="remarks" placeholder="Remarks">
                    </div>
                    <div class="form-group">
                        <label>Check #</label>
                        <input type="text" class="form-control" name="check_number" placeholder="Check #">
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
<div class="modal fade" id="print" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mySmallModalLabel">Print</h4>
            </div>
            <form id="advanced-search">
                <div class="modal-body">
                    <a class="btn btn-flat btn-block btn-default print" data-url="<?=$url?>/do_print/?id=">Print voucher</a>
                    <a class="btn btn-flat btn-block btn-default print" data-url="<?=$url?>/ajax_print_check/?id=">Print check</a>
                </div>
            </form>
        </div>
    </div>
</div>

