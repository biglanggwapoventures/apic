<?php $url = base_url('accounting/debit_memo'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff">
                <h3 class="box-title"><?= $form_title?></h3>
            </div><!-- /.box-header -->
            <form data-action="<?= $form_action?>">
                <div class="box-body">
                    <div class="callout callout-danger hidden"><ul class="list-unstyled"></ul></div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date</label>
                                <input value="<?= isset($dc['date']) ? date('m/d/Y', strtotime($dc['date'])) : date('m/d/Y')?>" type="text" class="form-control datepicker" required="required" name="date"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="customer">Customer</label>
                                <?php if(isset($dc['company_name'])):?>
                                    <p class="form-control-static"><?= $dc['company_name']?></p>
                                <?php else:?>
                                    <?= generate_customer_dropdown('customer', FALSE, 'class="form-control" id="search-customer" required="required"')?>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Amount</label>
                                <input value="<?= isset($dc['amount']) ? number_format($dc['amount'],2) : ''?>" type="text" class="form-control price" name="amount" required="required"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea class="form-control" name="remarks"><?= isset($dc['remarks']) ? $dc['remarks'] : ''?></textarea>
                    </div>
                    <?php $checked = (!empty($dc['approved_by'])) ? 'checked' : ''?>
                    <?php if($this->session->userdata('type_id') == 1): ?>
                    <hr/>
                    <div class="checkbox">
                        <label>
                            <input name="status" value="<?= M_Status::STATUS_FINALIZED ?>" type="checkbox" <?=$checked?>/> Mark this debit memo as approved
                        </label>
                    </div>
                    <?php endif; ?>
                </div><!-- /.box-body -->
                <div class="box-footer clearfix">
                    <button type="submit" class="btn btn-success btn-flat">Submit</button>
                    <a href="<?= $url?>" class="btn btn-warning btn-flat pull-right cancel">Go back</a>
                </div><!-- /.box-body --> 
            </form>
        </div>
    </div>
</div>
<div class="hidden" id="print-check"></div>