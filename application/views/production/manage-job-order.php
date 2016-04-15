<div class="box box-solid">
    <div class="box-header  bg-light-blue-gradient" style="color:#FFFFFF!important">
        <h3 class="box-title">
            <?= $form_title?>
        </h3>
    </div>
    <form class="form" action="<?= $form_action ?>" method="post">
        <div class="box-body">
            <div class="callout callout-danger hidden"><ul class="list-unstyled"></ul></div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="datetime-start">Date Started:</label>
                        <input type="text" name="date_started" class="form-control datetimepicker" id="datetime-start" required="required"
                                value="<?= isset($data['jo']['date_started']) ? $data['jo']['date_started'] : date('m/d/Y h:i:s A')?>"/>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="production-code">* Production Code:</label>
                        <input type="text" class="form-control" name="production_code" id="production-code" required="required"
                                value="<?= isset($data['jo']['production_code']) ? $data['jo']['production_code'] : '' ?>"/>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="remarks">Remarks</label>
                <textarea class="form-control" name="remarks" id="remarks"><?= isset($data['jo']['remarks']) ? $data['jo']['remarks'] : '' ?></textarea>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <table class="table table-condensed table-bordered promix table-details" id="details-table" style="border-bottom:none;border-left: none">
                        <thead>
                            <tr class="active">
                                <th style="width:5%">Sq. #</th>
                                <th style="width:40%">Finished Product</th>
                                <th style="width:15%">Mix (Tons)</th>
                                <th style="width:35%">Customer</th>
                                <th style="width:5%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $data['details'] = isset($data['details']) ? $data['details'] : [[]];?>
                            <?php foreach($data['details'] AS $details):?>
                                <tr>
                                    <td>
                                        <?= isset($details['sequence_number']) ? $details['sequence_number'] : 1?>
                                    </td>
                                    <td>
                                        <?= arr_group_dropdown('fk_product_inventory_id[]', $finished_products, 'id', 'description', isset($details['fk_inventory_product_id']) ? $details['fk_inventory_product_id'] : FALSE, FALSE,'class="form-control" required="required"')?>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" required="required" class="form-control" name="mix_number[]" value="<?= isset($details['mix_number']) ? $details['mix_number'] : FALSE?>"/>
                                    </td>
                                    <td>
                                        <?= arr_group_dropdown('fk_sales_customer_id[]', $customers, 'id', 'name', isset($details['fk_sales_customer_id']) ? $details['fk_sales_customer_id'] : FALSE, FALSE,'class="form-control" required="required"')?>
                                    </td>
                                    <td>
                                        <?php if(isset($details['id'])):?>
                                            <input type="hidden" value="<?=$details['id']?>" name="detail_id[]"/>
                                        <?php endif;?>
                                        <a class="btn btn-danger btn-sm btn-flat remove-detail"><i class="fa fa-times"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        </tbody>
                        <tfoot><tr><td colspan="4" style="border:none!important"></td><td class="text-center"><a role="button" class="btn-flat btn-sm btn btn-info add-line-details"><span class="fa fa-plus"></span></a></td></tr></tfoot>
                    </table>
                </div>
            </div>
            <div class="row hidden">
                <div class="col-md-5 col-xs-12">
                    <table class="table table-condensed table-bordered promix" style="border-bottom:none;border-left: none" id="misc-table">
                        <thead>
                            <tr class="active"><th colspan="3" class="text-center">Miscellaneous Fees</th></tr>
                            <tr class="info"><th>Description</th><th>Amount</th><th style="width:5%"></th></tr>
                        </thead>
                        <tbody><tr class="add-line-notif-misc"><td colspan="3" class="text-center">Click the <i class="fa fa-plus"></i> below to add a line.</td></tr></tbody>
                        <tfoot>
                            <tr><td>Total amount</td><td class="text-right" colspan="2">0.00</td></tr>
                            <tr><td colspan="2" style="border:none!important"></td><td><a class="btn btn-info add-line-misc btn-flat" role="button"><i class="fa fa-plus"></i></a></td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php if ($this->session->userdata('type_id') == M_Account::TYPE_ADMIN): ?>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="1" name="is_approved" <?=isset($data['jo']['approved_by']) && $data['jo']['approved_by'] ? 'checked="checked"' : ''?>> Mark this job order as approved
                    </label>
                </div>
            <?php endif; ?>
        </div>
        <div class="box-footer">
            <div class="btn-toolbar">
                <button type="submit"  class="btn btn-flat btn-success">Submit</button>
                <a id="btn-cancel" href="<?= base_url('production/job_order') ?>" class="btn-flat btn btn-danger">Cancel</a>
            </div>
        </div>
    </form>
</div>