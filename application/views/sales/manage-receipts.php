<input type="hidden" disabled="disabled" name="data-link-to-pl" value="<?= base_url('sales/deliveries/update/') ?>">
<input type="hidden" name="data-list-pl-url" value="<?= base_url('sales/deliveries/a_get_unpaid') ?>" disabled="disabled"/>
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <?= form_open($action, array('role' => 'form')); ?>
            <div class="box-header">
                <h3 class="box-title"><?= isset($form_title) ? $form_title : '' ?></h3>
            </div>
            <div class="box-body">
                <div class="callout callout-danger hidden">
                    <h4>Error!</h4>
                    <ul class="list-unstyled"></ul>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer-name">Customer</label>
                            <?php if ($defaults['fk_sales_customer_id']): ?>
                                <input type="hidden" name="fk_sales_customer_id" value="<?= $defaults['fk_sales_customer_id'] ?>"/>
                                <p class="form-control-static"><?= $defaults['company_name'] ?></p>
                            <?php else: ?>
                                <input type="hidden" name="data-list-pl-url" value="<?= base_url('sales/deliveries/a_get_uncountered') ?>" disabled="disabled"/>
                                <?php $attr = 'class="form-control" id="customer-name" required="required"'; ?>
                                <?= form_dropdown('fk_sales_customer_id', $customers, FALSE, $attr) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <?= form_input(array('name' => 'date', 'class' => 'form-control datepicker', 'id' => 'date', 'required' => 'required', 'value' => $defaults['date'])); ?>
                        </div>  
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tracking-number-type">Tracking # Type</label>
                            <?= form_dropdown('tracking_number_type', array('PTN' => 'P.T.N.', 'CR' => 'C.R.'), $defaults['tracking_number_type'], "class='form-control' id='tracking-number-type' required") ?>
                        </div>  
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tracking-number">Tracking #</label>
                            <?= form_input(array('name' => 'tracking_number', 'class' => 'form-control', 'id' => 'tracking-number', 'required' => 'required', 'value' => $defaults['tracking_number'])); ?>
                        </div>  
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="date">Remarks</label>
                            <?= form_textarea(array('name' => 'remarks', 'class' => 'form-control', 'id' => 'remarks', 'rows' => 3, 'resizable-x' => 'none', 'value' => $defaults['remarks'])); ?>
                        </div>  
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab_1-1" data-toggle="tab">Receipt Details</a></li>
                                <li class=""><a href="#tab_2-2" data-toggle="tab">Check Payment Details</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_1-1">
                                    <table class="table table-bordered" style="border-bottom: none;border-left: none;border-right: none;">
                                        <thead><tr class="info"><th style="width:15%">Transaction (PTN)</th><th style="width:15%">Date</th><th style="width:15%">Payment Method</th><th style="width:15%">Total Amount</th><th style="width:15%">Total Amount Paid</th><th style="width:15%">This Payment</th></tr></thead>
                                        <tfoot><tr><td class="no-border" colspan="4"></td><td class="info">Total Amount</td><td class="info total-amount text-center"><?= $defaults['total_amount'] ?></td></tr></tfoot>
                                        <tbody id="receipt-details">
                                            <?php if (isset($defaults['details']) && is_array($defaults['details'])): ?>
                                                <?php foreach ($defaults['details'] as $details): ?>

                                                    <tr>
                                                        <td>
                                                            <input name="details[id][]" type="hidden" value="<?= $details['id'] ?>">
                                                            <input name="details[fk_sales_delivery_id][]" type="hidden" value="<?= $details['fk_sales_delivery_id'] ?>"> 
                                                            <a style="margin-left:10px;" target="_blank" href="<?= base_url("sales/deliveries/update{$details['fk_sales_delivery_id']}") ?>">
                                                                P.L. # <?= "{$details['fk_sales_delivery_id']} ({$details['ptn_number']})" ?></a> 
                                                        </td>
                                                        <td><?= $details['transaction_date'] ?></td>
                                                        <td>
                                                            <?= form_dropdown('details[payment_method][]', array('Cash' => 'Cash', 'Check' => 'Check'), $details['payment_method'], 'class="form-control"') ?>
                                                        </td>
                                                        <td><span class="has-amount"><?= $details['transaction_amount'] ?></span></td>
                                                        <td><span class="has-amount"><?= $details['amount_paid'] ?></span></td>
                                                        <td><input type="text" name="details[amount][]" value="<?= $details['this_payment'] ?>" class="form-control has-amount"></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="6" class="text-center">Please select a customer.</td></tr>
                                            <?php endif; ?>
                                        </tbody>

                                    </table>
                                </div><!-- /.tab-pane -->
                                <div class="tab-pane" id="tab_2-2">
                                    <table class="table table-bordered" style="border-bottom: none;border-left: none;border-right: none;">
                                        <thead><tr class="info"><th style="width:20%">Bank Account</th><th style="width:15%">Check Number</th><th style="width:15%">Check Date</th><th style="width:15%">Deposit Date</th><th style="width:15%">Amount</th><th style="width:5%"></th></tr></thead>
                                        <tfoot><tr><td class="no-border" colspan="5"></td><td class='text-center'><button type="button" class="add-line btn btn-info btn-sm"><i class="fa fa-plus"></i> </button></td></tr></tfoot>
                                        <tbody id="check-details">
                                            <tr id="template" class="hidden">
                                                <td>
                                                    <?= generate_dropdown('check[fk_accounting_bank_account_id][]', $bank_accounts, FALSE, 'class="form-control" disabled required disabled', 'bank account,') ?>
                                                </td>
                                                <td><?= form_input(array('class' => 'form-control', 'name' => 'check[check_number][]', 'disabled' => 'disabled', 'required' => 'required', 'disabled' => 'disabled')) ?></td>
                                                <td><?= form_input(array('class' => 'form-control datepicker', 'name' => 'check[check_date][]', 'disabled' => 'disabled', 'required' => 'required', 'disabled' => 'disabled')) ?></td>
                                                <td><?= form_input(array('class' => 'form-control datepicker', 'name' => 'check[deposit_date][]', 'disabled' => 'disabled', 'required' => 'required', 'disabled' => 'disabled')) ?></td>
                                                <td><?= form_input(array('class' => 'form-control has-amount', 'name' => 'check[amount][]', 'disabled' => 'disabled', 'required' => 'required', 'disabled' => 'disabled')) ?></td>
                                                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-line"><i class='fa fa-times'></i></button></td>
                                            </tr>
                                            <tr id="notify-no-entry"><td colspan="6" class='text-center'>There are no check transactions</td></tr>
                                            <?php if (isset($defaults['check']) && is_array($defaults['check'])): ?>
                                                <?php foreach ($defaults['check'] as $check): ?>
                                                    <tr>
                                                        <td>
                                                            <input name="check[id][]" type="hidden" value="<?= $check['id'] ?>">
                                                            <?= generate_dropdown('check[fk_accounting_bank_account_id][]', $bank_accounts, (int)$check['fk_accounting_bank_account_id'], 'class="form-control"', 'bank account') ?>
                                                        </td>
                                                        <td><?= form_input(array('class' => 'form-control', 'name' => 'check[check_number][]', 'value' => $check['check_number'])) ?></td>
                                                        <td><?= form_input(array('class' => 'form-control datepicker', 'name' => 'check[check_date][]', 'value' => $check['check_date'])) ?></td>
                                                        <td><?= form_input(array('class' => 'form-control datepicker', 'name' => 'check[deposit_date][]', 'value' => $check['deposit_date'])) ?></td>
                                                        <td><?= form_input(array('class' => 'form-control has-amount', 'name' => 'check[amount][]', 'value' => $check['amount'])) ?></td>
                                                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-line"><i class="fa fa-times"></i></button></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div><!-- /.tab-pane -->
                            </div><!-- /.tab-content -->
                        </div>
                    </div>
                </div>
                <?php if ($this->session->userdata('type_id') == M_Account::TYPE_ADMIN): ?>
                    <hr>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="status" value="<?= M_Status::STATUS_FINALIZED ?>" class="flat-red" 
                                           <?= $defaults['status'] == M_Status::STATUS_FINALIZED ? "checked" : "" ?>/>
                                    Mark this receipt as finalized
                                </label>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="box-footer">
                <?php if ((int) $defaults['status'] !== (int) M_Status::STATUS_FINALIZED || (int) $this->session->userdata('type_id') === (int) M_Account::TYPE_ADMIN): ?>
                    <?= form_button(array('type' => 'submit', 'class' => 'btn btn-primary', 'content' => 'Save')); ?>
                <?php else: ?>
                    <?= form_button(array('type' => 'button', 'onClick' => 'javascript:alert(\'Cannot do action\')', 'class' => 'btn btn-primary disabled', 'content' => 'Save')); ?>
                <?php endif; ?>
                <a href="<?= base_url('sales/receipts') ?>" class="btn btn-danger pull-right">Go back</a>
            </div>
            <?= form_close(); ?>
        </div>

    </div>
</div>