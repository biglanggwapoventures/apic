<?php $url = base_url('sales/trucking'); ?>
<div class="row">
    <div class="col-md-6">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff">
                <h3 class="box-title"><?= $title ?></h3>
            </div><!-- /.box-header -->

            <form data-action="<?= $action ?>">
                <div class="box-body">
                    <div class="callout callout-danger hidden">
                      <ul class="list-unstyled">
                        
                      </ul>
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input name="trucking_name" class="form-control" value="<?= put_value($data, 'trucking_name', '')?>">
                    </div>
                    <div class="form-group">
                        <label>Driver</label>
                        <input name="driver" class="form-control" value="<?= put_value($data, 'driver', '')?>">
                    </div>
                    <div class="form-group">
                        <label>Plate Number</label>
                        <input name="plate_number" class="form-control" value="<?= put_value($data, 'plate_number', '')?>">
                    </div>
                    <?php if(can_set_status()):?>
                        <div class="form-group">
                            <label>Status</label>
                            <?= status_dropdown('status', put_value($data, 'status', ''), 'class="form-control"')?>
                        </div>
                    <?php endif;?>
                </div><!-- /.box-body -->  

                <div class="box-body">
                    <button class="btn btn-flat btn-success <?= can_update($data) ? '' : 'disabled'?>">Submit</button>
                    <a class="btn btn-flat btn-warning" id="cancel" href="<?= $url?>">Cancel</a>
                </div><!-- /.box-footer -->  
            </form>
        </div>
    </div>
</div>