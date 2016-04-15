<style type="text/css">
    table.promix tbody td:nth-child(6){
        text-align: right;
    }
    table.promix thead th:nth-child(6){
        text-align: right;
    }
    table.promix tbody td:nth-child(7){
        text-align: center; 
    }
    table.promix thead th:nth-child(7){
        text-align: center;
    }
</style>
<?php $url = base_url('sales/deliveries'); ?>
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
                            <li><a href="<?= $url . '/add/' ?>">Add new packing list</a></li>
                            <li><a href="<?= base_url('sales/dressed_packing_list/create') ?>">Add new packing list (dressed)</a></li>
                            <li><a href="<?= $url . '/add/' ?>">Add new packing list (live)</a></li>
                            <li><a href="#" data-toggle="modal" data-target=".advanced-search-modal">Advanced search</a></li>
                        </ul>
                    </div>                 
                </div><!-- /. tools -->
            </div><!-- /.box-header -->
            <div class="box-body no-padding" style="display: block;">
                <div class="row">
                    <div class="col-xs-12">

                        <table id="so-master-list" class="table table-striped table-condensed promix" 
                            data-edit-url='<?= $url . '/update/' ?>' 
                            data-edit-dressed='<?= base_url('sales/dressed_packing_list/edit') ?>/' 
                               data-master-list-url='<?= $url . '/a_master_list' ?>'
                               data-delete-url='<?= $url . '/a_delete/' ?>'
                               data-print-url='<?= base_url("sales/deliveries/do_print")?>'
                               data-print-dressed='<?= base_url('sales/dressed_packing_list/do_print') ?>/'
                               data-gatepass-url='<?= "{$url}/print_gatepass/"?>'>
                            <thead>
                                <tr class="info">
                                    <th>P.L.#</th>
                                    <th>S.O.#</th>
                                    <th>P.O.#</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th class="text-right"></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr ><td id="view-more-section" colspan="8" class="text-center"><span class="notification"></span><button id="btn-view-more" class="btn btn-flat btn-xs btn-default" type="button">Click to view more</button></td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div><!-- /.box-body -->  

        </div>
    </div>
</div>

<div class="modal fade advanced-search-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mySmallModalLabel">Advanced search</h4>
            </div>
            <form id="advanced-search">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="sr-only" for="search-so">P.L. #</label>
                                <input type="number" class="form-control" name="pl_no" id="search-so" placeholder="P.L. #">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="sr-only" for="search-so">S.O. #</label>
                                <input type="number" class="form-control" name="so_no" id="search-so" placeholder="S.O. #">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="sr-only" for="search-po">P.O. #</label>
                                <input type="number" class="form-control" name="po_no" id="search-po" placeholder="P.O. #">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="sr-only" for="search-date">Date</label>
                        <input type="text" class="form-control datepicker has-default" name="date" id="search-date" placeholder="Date">
                    </div>
                    <div class="form-group">
                        <label class="sr-only"  for="search-customer">Customer</label>
                        <?= generate_customer_dropdown('customer', FALSE, 'class="form-control" id="search-customer"', 'All customer')?>
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
