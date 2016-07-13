<?php $url = base_url('tracking/locations'); ?>
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
                        <label>Location Name</label>
                        <input name="name" style="text-align: left" type="text" class="form-control" value="<?= put_value($data, 'name', '')?>">
                    </div>
                </div><!-- /.box-body -->  

                <div class="box-body">
                    <button class="btn btn-flat btn-success <?= can_update($data) ? '' : 'disabled'?>">Submit</button>
                    <a class="btn btn-flat btn-warning" id="cancel" href="<?= $url?>">Cancel</a>
                </div><!-- /.box-footer -->  
            </form>
        </div>
    </div>
</div>