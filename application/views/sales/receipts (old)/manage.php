<div class="row">
    <div class="col-md-12">
        <div class="box box-solid"
             data-pl-url="<?= base_url('sales/deliveries/update') ?>/"
             data-get-customer-unsettled-url="<?= base_url('sales/customers/a_get_unsettled') ?>">
            <div class="box-header bg-light-blue-gradient" style="color:#fff!important;">
                <h3 class="box-title"><?= $form_title; ?></h3>
            </div>
            <form method="post" action="<?= $form_action ?>">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label for="customer">Customer</label>
                                <?= form_dropdown('fk_sales_customer_id', $customers, '', 'id="customer" required="required" class="form-control"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3 col-sm-offset-4">
                            <div class="form-group">
                                <label for="date">Date</label>
                                <input type="text" class="form-control datepicker" id="date" name="date" required="required"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="customer">Tracking Type</label>
                                <select class="form-control" id="tracking-type" name="tracking_number_type">
                                    <option value=""></option>
                                    <option value="PTN">PTN</option>
                                    <option value="CR">CR</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="customer">Tracking #</label>
                                <input type="text" class="form-control" id="tracking-number" name="tracking_number"/>
                            </div>
                        </div>
                    </div>
                     <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea name="remarks" class="form-control"></textarea>>
                            </div>
                    <hr/>
                    <table class="table table-bordered table-condensed" style="border-bottom:none;border-left:none">
                        <thead><tr class="info"><th>Transaction</th><th>Date</th><th class="text-right">Total Amount</th><th class="text-right">Amount Paid</th><th class="text-right">Payment Method</th><th class="text-right">This Payment</th></tr></thead>
                        <tbody id="transactions"><tr id="notification"><td class="text-center bg-red" colspan="6">Please select a customer</td></tr></tbody>
                        <tfoot><tr><td colspan="4" class="no-border"></td><td class="text-right info"><strong>Total</strong></td><td class="text-right info text-bold" id="total">0.00</td></tr></tfoot>
                    </table>
                    <?php if ($this->session->userdata('type_id') == M_Account::TYPE_ADMIN): ?>
                        <div class="checkbox">
                            <label>
                                <input name="status" value="<?= M_Status::STATUS_FINALIZED ?>" type="checkbox"> Mark this sales receipt as finalized
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="box-footer clearfix">
                    <button class="btn btn-primary" type="submit">Save</button>
                    <a href="<?= base_url('sales/receipts') ?>" class="btn btn-danger pull-right">Go back</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/x-data" id="bank-account-dropdown">
    <?= form_dropdown('check_payments[fk_accounting_bank_account_id][]', $banks, '', 'class="form-control" required="required"') ?>
</script>