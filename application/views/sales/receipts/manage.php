<style type="text/css">
    tbody#transactions tr td:nth-child(3),td:nth-child(4),td:nth-child(5),td:nth-child(6) input.price{
        text-align: right;
    }
</style>
<div class="box box-solid"
     data-pl-url="<?= base_url('sales/deliveries/update') ?>/"
     data-get-customer-unsettled-url="<?= base_url('sales/receipts/ajax_get_unsettled') ?>">
    <div class="box-header bg-light-blue-gradient" style="color:#fff!important;">
        <h3 class="box-title"><?= $form_title; ?></h3>
    </div>
     <form data-action="<?= $form_action ?>">
        <div class="box-body">
            <div class="callout callout-danger hidden"><ul class="list-unstyled"></ul></div>
             <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="date">Date</label>
                                <?php $date = isset($data['receipt']['date']) ? date('m/d/Y', strtotime($data['receipt']['date'])) : date('m/d/Y')?>
                                <p class="form-control-static"><?= $date?></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">Customer</label>
                                <?php if(isset($data['receipt']['customer'])):?>
                                    <p class="form-control-static"><?= $data['receipt']['customer']?></p>
                                <?php else:?>
                                    <?= generate_customer_dropdown('customer', FALSE, 'class="form-control" id="search-customer" required="required"')?>
                                <?php endif;?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="customer">Tracking #</label>
                                <?php $tracking_type = isset($data['receipt']['tracking_type']) ? $data['receipt']['tracking_type'] : FALSE;?>
                                <?= form_dropdown('tracking_type', ['PTN' => 'PTN', 'CR' => 'CR'], $tracking_type, 'class="form-control" id="tracking-type" required="required"');?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="customer">&nbsp;</label>
                                <?php $tracking_no = isset($data['receipt']['tracking_no']) ? $data['receipt']['tracking_no'] : FALSE;?>
                                <input type="text" class="form-control" id="tracking-number" name="tracking_no" value="<?= $tracking_no?>" required="required"/>
                            </div>
                        </div>
                    </div>
                     <div class="form-group">
                        <label for="remarks">Remarks</label>
                         <?php $remarks = isset($data['receipt']['remarks']) ? $data['receipt']['remarks'] : FALSE;?>
                        <textarea name="remarks" class="form-control"><?= $remarks?></textarea>
                    </div>
                    <hr/>
                    <table class="table table-bordered table-condensed" style="border-bottom:none;border-left:none">
                        <thead><tr class="active"><th>PL #</th><th>Date</th><th class="text-right">Total Amount</th><th class="text-right">Amount Paid</th><th class="text-right">Balance</th><th class="text-right">This Payment</th></tr></thead>
                        <tbody id="transactions">
                            <?php if(isset($data['details'])):?>
                                <?php $total = 0;?>
                                <?php foreach($data['details'] AS $row):?>
                                <tr>
                                    <td>
                                        <input type="hidden" value="<?= $row['id']?>" name="details[<?= $row['pl_id']?>][id]"/>
                                        <input type="hidden" value="<?= $row['pl_id']?>" name="details[<?= $row['pl_id']?>][pl_id]"/>
                                        <a target="_blank" href="<?= base_url("sales/deliveries/update/{$row['pl_id']}")?>">PL# <?= $row['pl_id']?></a>
                                    </td>
                                    <td><?= date('m/d/Y', strtotime($row['date']))?></td>
                                    <td><span class="full-pay"><?= number_format($row['pl_amount'], 2)?></span></td>
                                    <td><?= number_format($row['amount_paid'], 2)?></td>
                                     <td><span class="full-pay"><?= number_format($row['pl_amount']- $row['amount_paid'], 2)?></span></td>
                                    <td>
                                        <?php $total += $row['amount']; ?>
                                        <input type="text" value="<?= number_format($row['amount'], 2)?>" class="form-control price payment" name="details[<?= $row['pl_id']?>][this_payment]"/>
                                    </td>
                                </tr>
                                <?php endforeach;?>
                            <?php else:?>
                                <tr id="notification"><td class="text-center" colspan="6">Please select a customer to begin</td></tr>
                            <?php endif;?>
                        </tbody>
                        <tfoot><tr><td colspan="4" class="no-border"></td><td class="text-right active"><strong>Total</strong></td><td class="text-right active text-bold" id="total"><?= isset($total) ? number_format($total,2): '0.00'?></td></tr></tfoot>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="box box-solid box-primary">
                        <div class="box-header">
                            <h3 class="box-title">Payment Details</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label>Payment Type</label>
                                <?php $payment_type = !empty($data['receipt']['check']) ? 'check' : 'cash'; ?>
                                <?php $add_disabled = $payment_type === 'cash' ? 'disabled="disabled"' : '' ?>
                                <?= form_dropdown('payment[type]', ['check' => 'Check', 'cash' => 'Cash'], $payment_type, 'class="form-control" id="pay-opt"') ?>
                            </div>
                            <div class="form-group">
                                <label>Amount</label>
                                <?php $amount = isset($data['receipt']['check']['check_amount']) ? $data['receipt']['check']['check_amount'] : 0; ?>
                                <input type="text" value='<?= number_format($amount, 2) ?>' class="form-control price-format has-amount pay-opt-cash-disabled" id="total-payment" required="required" name="payment[amount]" <?= $add_disabled?>/>
                            </div>
                            <div class="form-group">
                                <label>Customer Bank</label>
                                <?php $account = isset($data['receipt']['check']['bank_account']) ? $data['receipt']['check']['bank_account'] : ''; ?>
                                <input type="text" value="<?=$account?>" class="form-control pay-opt-cash-disabled"  name="payment[pay_from]" <?= $add_disabled ?>/>
                            </div>
                            <div class="form-group">
                                <label>Deposit To</label>
                                <?php $pay_to = isset($data['receipt']['check']['pay_to']) ? $data['receipt']['check']['pay_to'] : FALSE; ?>
                                <?= arr_group_dropdown('payment[pay_to]', $bank_accounts, 'id', 'bank_name', $pay_to, FALSE, 'class="form-control pay-opt-cash-disabled" required="required"'.$add_disabled) ?>
                            </div>
                            <div class="form-group">
                                <label>Check Number</label>
                                <?php $check_num = isset($data['receipt']['check']['check_number']) ? $data['receipt']['check']['check_number'] : ''; ?>
                                <input type="text" class="form-control pay-opt-cash-disabled" value="<?= $check_num ?>" required="required" name="payment[check_number]" <?= $add_disabled ?>/>
                            </div>
                            <div class="form-group">
                                <label>Check Date</label>
                                <?php $check_date = isset($data['receipt']['check']['check_date']) ? date('m/d/Y', strtotime($data['receipt']['check']['check_date'])) : ''; ?>
                                <input type="text" class="form-control pay-opt-cash-disabled datepicker" value="<?= $check_date ?>" required="required" name="payment[check_date]" <?= $add_disabled ?>/>
                            </div>
                            <div class="form-group">
                                <label>Deposit Date</label>
                                <?php $deposit_date = isset($data['receipt']['deposit_date']) && $data['receipt']['deposit_date'] ? date('m/d/Y', strtotime($data['receipt']['deposit_date'])) : ''; ?>
                                <input type="text" class="form-control datepicker" value="<?= $deposit_date ?>" required="required" name="deposit_date"/>
                            </div>
                        </div><!-- /.box-body -->
                    </div>
                </div>
            </div>
            <?php if ($this->session->userdata('type_id') == M_Account::TYPE_ADMIN): ?>
                <?php $checked = isset($data['receipt']['status']) && $data['receipt']['status'] == M_Status::STATUS_FINALIZED ? 'checked="checked"': ''?>
                <div class="checkbox">
                    <label>
                        <input name="status" value="<?= M_Status::STATUS_FINALIZED ?>" type="checkbox" <?=$checked?>/> Mark this sales receipt as finalized
                    </label>
                </div>
            <?php endif; ?>
        </div>
        <div class="box-footer clearfix">
            <button class="btn btn-primary btn-flat" type="submit">Save</button>
            <a href="<?= base_url('sales/receipts') ?>" class="btn btn-danger pull-right btn-flat cancel">Go back</a>
        </div>
    </form>
</div>