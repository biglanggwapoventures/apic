<?php $url = base_url('inventory/products'); ?>
<div class="box box-solid">
    <div class="box-header bg-light-blue-gradient" style="color:#fff">
        <h3 class="box-title">Master List</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
            <!-- button with a dropdown -->
            <div class="btn-group">
                <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li><a href="<?= "{$url}/create"?>">Create new product</a></li>
                    <li><a data-toggle="modal" data-target="#search">Search</a></li>
                </ul>
            </div>                 
        </div><!-- /. tools -->
    </div><!-- /.box-header -->

    <div class="box-body no-padding">
        <table class="table table-striped"> 
            <thead>
                <tr class="info"><th>CODE</th><th>DESCRIPTION</th><th>CATEGORY</th><th>UNIT QTY</th><th>PIECES</th><th>STATUS</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach($items AS $row):?>   
                    <tr data-pk="<?= $row['id']?>">
                        <td><a href="<?= "{$url}/edit/{$row['id']}"?>"><?= $row['code']?></a></td>
                        <td><?= $row['description']?></td>
                        <td><?= $row['category']?></td>
                        <td><?= isset($row['available_units']) ? "{$row['available_units']} {$row['unit']}" : '-'?></td>
                        <td><?= isset($row['available_pieces']) ? "{$row['available_pieces']} pieces" : '-'?></td>
                        <td>
                            <?php $status = status($row['status'])?>
                            <span class="label <?= $status['class']?>"><?= $status['text']?></span>
                        </td>
                        <td>
                            <a class="btn btn-xs btn-flat btn-default" href="<?= base_url("reports/product_inventory?product_id={$row['id']}")?>"><i class="fa fa-search"></i> Logs</a>
                            <a class="btn btn-xs btn-flat btn-danger _delete <?= can_delete($row) ? '' : 'disabled'?>"><i class="fa fa-times"></i> Delete</a>
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
                <p class="text-danger text-center text-bold">Do you really want to delete this product?<br> <u>This action cannot be undone.<u></p>
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
                        <label>Description</label>
                        <input type="text" class="form-control" value="<?= $this->input->get('description')?>" name="description"/>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <?php $type_default = $this->input->get('category') ? $this->input->get('category') : 'all';?>
                        <?= form_dropdown('category', [''=>'']+$category, $type_default, 'class="form-control"') ?>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <?php $status_default = $this->input->get('status') ? $this->input->get('status') : 'a';?>
                        <?= status_dropdown('status', $status_default, 'class="form-control"', TRUE)?>
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