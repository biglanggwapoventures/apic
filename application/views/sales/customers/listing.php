<?php $url = base_url('sales/customer'); ?>
<div class="box box-solid">
    <div class="box-header bg-light-blue-gradient" style="color:#fff">
        <h3 class="box-title">Master List</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
            <!-- button with a dropdown -->
            <div class="btn-group">
                <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li><a href="<?= "{$url}/create"?>">Create new customer</a></li>
                    <li><a data-toggle="modal" data-target="#search">Search</a></li>
                </ul>
            </div>                 
        </div><!-- /. tools -->
    </div><!-- /.box-header -->

    <div class="box-body no-padding">
        <table class="table table-striped"> 
            <thead><tr class="info"><th>Code</th><th>Name</th><th>Address</th><th>Credit Limit</th><th>Payment terms</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php foreach($items AS $row):?>   
                    <tr data-pk="<?= $row['id']?>">
                        <td><a href="<?= "{$url}/edit/{$row['id']}"?>"><?= $row['customer_code']?></a></td>
                        <td><?= $row['company_name']?></td>
                        <td><?= $row['address']?></td>
                        <td><?= number_format($row['credit_limit'], 2)?></td>
                        <td><?= intval($row['credit_term']) ? "{$row['credit_term']} days" : 'Cash on delivery'?></td>
                        <td>
                            <?php $status = status($row['customer_status'])?>
                            <span class="label <?= $status['class']?>"><?= $status['text']?></span>
                        </td>
                        <td>
                            <a class="btn btn-xs btn-flat btn-primary" href="<?= "{$url}/show_pricing/{$row['id']}" ?>"><i class="fa fa-shopping-cart"></i> Prices</a>
                            <a class="btn btn-xs btn-flat btn-danger _delete <?= can_delete($row, 'customer_status') ? '' : 'disabled'?>"><i class="fa fa-times"></i> Delete</a>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
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


<div class="modal fade" id="search" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="mySmallModalLabel">Search</h4>
            </div>
            <form method="GET" action="<?= current_url()?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Code</label>
                        <input type="text" class="form-control" value="<?= $this->input->get('customer_code')?>" name="customer_code"/>
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" value="<?= $this->input->get('company_name')?>" name="company_name"/>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <?php $status_default = $this->input->get('customer_status') ? $this->input->get('customer_status') : 'a';?>
                        <?= status_dropdown('customer_status', $status_default, 'class="form-control"', TRUE)?>
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