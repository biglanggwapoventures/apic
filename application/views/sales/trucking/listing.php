<?php $url = base_url('sales/trucking'); ?>
<div class="box box-solid">
    <div class="box-header bg-light-blue-gradient" style="color:#fff">
        <h3 class="box-title">Master List</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
            <!-- button with a dropdown -->
            <div class="btn-group">
                <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li><a href="<?= "{$url}/create"?>">Create new trucking</a></li>
                    <li><a data-toggle="modal" data-target="#search">Search</a></li>
                </ul>
            </div>                 
        </div><!-- /. tools -->
    </div><!-- /.box-header -->

    <div class="box-body no-padding">
        <table class="table table-striped"> 
            <thead><tr class="info"><th>Name</th><th>Driver</th><th>Plate #</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php foreach($items AS $row):?>   
                    <tr data-pk="<?= $row['id']?>">
                        <td><a href="<?= "{$url}/edit/{$row['id']}"?>"><?= $row['trucking_name']?></a></td>
                        <td><?= $row['driver']?></td>
                        <td><?= $row['plate_number']?></td>
                        <td>
                            <?php $status = status($row['status'])?>
                            <span class="label <?= $status['class']?>"><?= $status['text']?></span>
                        </td>
                        <td>
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
                <p class="text-danger text-center text-bold">Do you really want to delete this trucking?<br> <u>This action cannot be undone.<u></p>
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
                        <label>Name</label>
                        <input type="text" class="form-control" value="<?= $this->input->get('trucking_name')?>" name="trucking_name"/>
                    </div>
                    <div class="form-group">
                        <label>Driver</label>
                        <input type="text" class="form-control" value="<?= $this->input->get('driver')?>" name="driver"/>
                    </div>
                    <div class="form-group">
                        <label>Plate number</label>
                        <input type="text" class="form-control" value="<?= $this->input->get('plate_number')?>" name="plate_number"/>
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