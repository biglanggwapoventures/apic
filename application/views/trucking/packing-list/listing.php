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
                            <li><a href="<?= "{$url}/create"?>">Create new packing list</a></li>
                            <li><a data-toggle="modal" data-target="#search">Search</a></li>
                        </ul>
                    </div>                 
                </div><!-- /. tools -->
            </div><!-- /.box-header -->

            <div class="box-body no-padding">
                <table class="table table-striped"> 
                    <thead><tr class="info"><th>#</th><th>Trip Ticket #</th><th>Customer</th><th>Date</th><th>Tariff Code</th><th>Net Amount Due</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach($items AS $row):?>   
                            <tr data-pk="<?= $row['id']?>">
                                <td><a href="<?= "{$url}/get/{$row['id']}"?>"><?= $row['id']?></a></td>
                                <td class="text-center"><?= $row['trip_ticket']?></td>   
                                <td><?= $row['company']?></td>
                                <td><?= $row['date']?></td>
                                <td><?= $row['code']?></td>
                                <td class="text-right"><span class="amount"><?= number_format($row['net_amount'],2)?></span></td>
                                <td><?php if(!empty($row['approved_by'])) echo '<span class="label label-success">Approved</span>'; else echo '<span class="label label-warning">Pending Approval</span>';?></td>
                                <td>

                                    <a href="<?= "{$url}/do_print/{$row['id']}" ?>" class="btn btn-xs btn-flat btn-default print <?= !$row['approved_by'] ? 'disabled' : ''?>"><i class="fa fa-print"></i></a>
                                    <a class="btn btn-xs btn-flat btn-danger _delete <?= can_delete($row) ? '' : 'disabled'?>"><i class="fa fa-times"></i></a>

                                </td>
                            </tr>
                        <?php endforeach;?>
                        <?php if(empty($items)):?>
                            <tr><td colspan="7" class="text-center">No data to show.</td></tr>
                        <?php endif;?>
                    </tbody>




                </table>
            </div><!-- /.box-body -->  
        </div>
    </div>
</div>

<div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="mySmallModalLabel">Confirm action</h4>
            </div>
            <div class="modal-body">
                <p class="text-danger text-center text-bold">Do you really want to delete this packing list?<br> <u>This action cannot be undone.<u></p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-flat btn-danger" id="delete-confirmed" data-delete-url="<?= "{$url}/delete/"?>">Yes</a>
                <a data-dismiss="modal" class="btn btn-flat btn-default">No</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="search" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="mySmallModalLabel">Search</h4>
            </div>
            <form method="GET">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Customer</label>
                        <?= generate_customer_dropdown('fk_sales_customer_id', FALSE,  'class="form-control"')?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-flat btn-danger" type="submit">Search</button>
                    <a data-dismiss="modal" class="btn btn-flat btn-default">Cancel</a>
                </div>
             </form>
        </div>
    </div>
</div>