<input type="hidden" disabled="disabled" name="data-link-to-pl" value="<?= base_url('sales/deliveries/update/') ?>">
<input type="hidden" name="data-list-pl-url" value="<?= base_url('sales/deliveries/a_get_uncountered') ?>" disabled="disabled"/>
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <?= form_open($action); ?>
            <div class="box-header">
                <h3 class="box-title"><?= $form_title ?></h3>
            </div>
            <div class="box-body">
                <div class="callout callout-danger hidden">
                    <h4>Error!</h4>
                    <ul class="list-unstyled"></ul>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="customer-name">Customer</label>
                            <?php if ($defaults['fk_sales_customer_id']): ?>
                                <p class="form-control-static"><?= $defaults['company_name'] ?></p>
                            <?php else: ?>
                                <?= form_dropdown('fk_sales_customer_id', $customers, FALSE, 'class="form-control"') ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="invoice-number">SI#</label>
                            <?= form_input(['name' => 'invoice_number', 'class' => 'form-control', 'value' => put_value($data, 'invoice_number', '')]); ?>
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
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="date">Remarks</label>
                            <?= form_textarea(array('name' => 'remarks', 'class' => 'form-control', 'id' => 'remarks', 'rows' => 3, 'resizable-x' => 'none', 'value' => $defaults['remarks'])); ?>
                        </div>  
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-bordered" style="border-bottom: none;border-left: none;border-right: none;">
                            <thead><tr class="info"><th>Packing List #</th><th>Invoice #</th><th>Date</th><th>Amount</th></tr></thead>
                            <tfoot><tr><td class="no-border" colspan="2"></td><td class="info">Total Amount</td><td class="info total-amount text-center">0.00</td></tr></tfoot>
                            <tbody>
                                <?php if (isset($defaults['details']) && is_array($defaults['details'])): ?>
                                    <?php for ($x = 0; $x < count($defaults['details']['fk_sales_delivery_id']); $x++): ?>
                                        <tr>
                                            <td>
                                                <input type="hidden" value="<?=$defaults['details']['id'][$x]?>" name="details[id][]"/>
                                                <div class="checkbox">
                                                    <label>
                                                        <input name="details[fk_sales_delivery_id][]" type="checkbox" value="<?= $defaults['details']['fk_sales_delivery_id'][$x] ?>" checked="checked"/>
                                                        P.L. # <?= $defaults['details']['fk_sales_delivery_id'][$x] ?>
                                                    </label>
                                                    <a style="margin-left:10px;" target="_blank" href="<?= base_url("sales/deliveries/update/{$defaults['details']['fk_sales_delivery_id'][$x]}") ?>"><i class="glyphicon glyphicon-link"></i></a>
                                                </div>
                                            </td>
                                            <td><?= $defaults['details']['ptn_number'][$x] ?></td>
                                            <td><?= date("F j, Y", strtotime($defaults['details']['date'][$x])); ?></td>
                                            <td><?= number_format($defaults['details']['amount'][$x], 2) ?></td>
                                        </tr>
                                    <?php endfor; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center">Please select a customer.</td></tr>
                                <?php endif; ?>
                            </tbody>

                        </table>
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
                                    Mark this counter receipt as finalized
                                </label>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="box-footer">
                <?php if ($defaults['status'] != M_Status::STATUS_FINALIZED || $this->session->userdata('type') == M_Account::TYPE_ADMIN): ?>
                    <?= form_button(array('type' => 'submit', 'class' => 'btn btn-primary', 'content' => 'Save')); ?>
                <?php else: ?>
                    <?= form_button(array('type' => 'button', 'onClick' => 'javascript:alert(\'Cannot do action\')', 'class' => 'btn btn-primary disabled', 'content' => 'Save')); ?>
                <?php endif; ?>
                <a href="<?= base_url('sales/counters') ?>" class="btn btn-danger pull-right">Go back</a>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>