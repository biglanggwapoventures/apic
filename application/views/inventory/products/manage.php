<?php $url = base_url('inventory/products'); ?>
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
                        <label>Code</label>
                        <input name="code" class="form-control" value="<?= put_value($data, 'code', '')?>">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <input name="description" class="form-control" value="<?= put_value($data, 'description', '')?>">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <?= form_dropdown('fk_category_id', ['' => ''] + array_column($categories, 'description', 'id'), put_value($data, 'fk_category_id', ''), 'class="form-control"')?>
                    </div>
                    <div class="form-group">
                        <label>Units</label>
                        <?= form_dropdown('fk_unit_id', ['' => ''] + array_column($units, 'description', 'id'), put_value($data, 'fk_unit_id', ''), 'class="form-control"')?>
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