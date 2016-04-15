<style type="text/css">
    table.promix tbody td:nth-child(5){
        text-align: right;
    }
    table.promix thead th:nth-child(5){
        text-align: right;
    }
</style>
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
                            <li><a href="<?php echo base_url('sales/receipts/create'); ?>">Add new sales receipt</a></li>
                            <li><a href="#" data-toggle="modal" data-target=".advanced-search-modal">Advanced search</a></li>
                        </ul>
                    </div>                 
                </div><!-- /. tools -->
            </div><!-- /.box-header -->
            <div class="box-body no-padding" style="display: block;">
                <div class="row">
                    <div class="col-xs-12">
                        <?php $url = base_url('sales/receipts'); ?>
                        <table class="table table-hover table-condensed promix" data-edit-url='<?= $url . '/update/' ?>' 
                               data-master-list-url='<?= $url . '/a_master_list' ?>'
                               data-delete-url='<?= $url . '/a_delete/' ?>'>
                            <thead>
                                <tr class="info"><th>S.R. #</th><th>Date</th><th>Customer</th><th>Tracking #</th><th>Amount</th><th>Status</th><th class="text-right"></th></tr>

                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr ><td id="view-more-section" colspan="7" class="text-center"><span class="notification"></span><button id="btn-view-more" class="btn btn-flat btn-xs btn-default" type="button">Click to view more</button></td></tr>
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
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="sr-only" for="search-so">S.O. #</label>
                                <input type="number" class="form-control" name="so" id="search-so" placeholder="S.O. #">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="sr-only" for="search-po">P.O. #</label>
                                <input type="number" class="form-control" name="po" id="search-po" placeholder="P.O. #">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="sr-only" for="search-date">Date</label>
                        <input type="text" class="form-control datepicker has-default" name="date" id="search-date" placeholder="Date">
                    </div>
                    <div class="form-group">
                        <label class="sr-only"  for="search-customer">Customer</label>
                        <select name="customer" class="form-control" id="search-customer">
                            <option value="">Any customer</option>
                            <?php foreach ($customers as $item): ?>
                                <option value="<?= $item['id'] ?>"><?= $item['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
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
