<?php $url = base_url('sales/agents'); ?>
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
                        <label>Agent code</label>
                        <input name="agent_code" style="text-align: left" type="text" class="form-control" value="<?= put_value($data, 'agent_code', '')?>">
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input name="name" class="form-control" value="<?= put_value($data, 'name', '')?>">
                    </div>
                    <div class="form-group">
                        <label>Area</label>
                        <input name="area" class="form-control" value="<?= put_value($data, 'area', '')?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label>Commission Rate</label>
                                <div class="input-group">
                                <span class="input-group-addon" id="basic-addon2">x</span>
                                  <input name="commission_rate" class="form-control" value="<?= put_value($data, 'commission_rate', '')?>" aria-describedby="basic-addon2">
                                  
                                </div>
                            </div>
                        </div>
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