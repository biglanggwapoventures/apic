<?php $url = base_url('sales/customer'); ?>
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
                        <input name="customer_code" class="form-control" value="<?= put_value($data, 'customer_code', '')?>">
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input name="company_name" class="form-control" value="<?= put_value($data, 'company_name', '')?>">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input name="address" class="form-control" value="<?= put_value($data, 'address', '')?>">
                    </div>
                    <div class="form-group">
                        <label>Contact number</label>
                        <input name="contact_number" class="form-control" value="<?= put_value($data, 'contact_number', '')?>">
                    </div>
                    <div class="form-group">
                        <label>Contact person</label>
                        <input name="contact_person" class="form-control" value="<?= put_value($data, 'contact_person', '')?>">
                    </div>
                    <div class="form-group">
                        <label>Credit limit</label>
                        <input name="credit_limit" class="form-control pformat" value="<?= number_format(put_value($data, 'credit_limit', 0), 2)?>">
                    </div>
                     <div class="form-group">
                        <label>Payment terms</label>
                        <input style="text-align: left!important;" type="number" min="0" name="credit_term" class="form-control" value="<?= put_value($data, 'credit_term', '')?>">
                    </div>
                    <?php if(can_set_status()):?>
                        <div class="form-group">
                            <label>Status</label>
                            <?= status_dropdown('customer_status', put_value($data, 'customer_status', ''), 'class="form-control"')?>
                        </div>
                    <?php endif;?>
                    <div class="form-group">
                        <div class="checkbox">
                            <label><input type="checkbox" name="for_trucking"<?= ($data['for_trucking'])?" checked":"";?>/> For <b>trucking</b></label>
                        </div>

                        <!-- <?= status_dropdown('for_trucking', put_value($data, 'for_trucking', ''), 'class="form-control"')?> -->
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