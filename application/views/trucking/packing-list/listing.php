<style type="text/css">
    table.promix tbody td:nth-child(5){
        text-align: right;
    }
    table.promix tbody td:nth-child(6),
    table.promix thead th:nth-child(6){
        text-align: center;
    }

</style>
<?php $url = base_url('trucking/packing_list'); ?>
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
                            <li><a href="<?= $url . '/create/' ?>">Add new packing list</a></li>
                            <li><a href="#" data-toggle="modal" data-target=".advanced-search-modal">Advanced search</a></li>
                        </ul>
                    </div>                 
                </div><!-- /. tools -->
            </div><!-- /.box-header -->
            <div class="box-body no-padding" style="display: block;">
                <div class="row">
                    <div class="col-xs-12">

                        <table id="master-list" class="table table-hover table-condensed promix" data-edit-url='<?= $url . '/get/' ?>' 
                               data-master-list-url='<?= $url . '/ajax_master_list' ?>'
                               data-print-url='<?= $url . '/do_print/' ?>'
                               data-delete-url='<?= $url . '/delete/' ?>'>
                            <thead>
                                <tr class="info">
                                    <th>#</th>
                                    <th>Trip Ticket #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Tariff Code</th>
                                    <th>Net Amount Due</th>
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
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mySmallModalLabel">Advanced search</h4>
            </div>
            <form id="advanced-search">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Customer</label>
                        <?= generate_customer_dropdown('fk_sales_customer_id', FALSE,  'class="form-control"')?>
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
