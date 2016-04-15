<div class="box box-solid">
    <div class="box-header bg-light-blue-gradient" style="color:#fff!important;">
        <h3 class="box-title">Credit memo for packing list # <?= str_pad($pl_id, 4, 0, STR_PAD_LEFT); ?></h3>
    </div>
    <form action="<?= !array_key_exists('id', $credit_memo) ? base_url("sales/deliveries/ajax_create_credit_memo/{$pl_id}") : base_url("sales/deliveries/ajax_update_credit_memo/{$pl_id}") ?>">
        <div class="box-body">
            <div class="callout callout-danger hidden" id="validation-errors">
                <h4>Validation errors</h4>
                <ul class="list-unstyled"></ul>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="text" class="form-control datepicker" name="date" value="<?= array_key_exists('date', $credit_memo) ? $credit_memo['date'] : date('Y-m-d')?>"/>
                    </div>
                </div>
            </div>
            <fieldset>
                <legend><h3>Returned Items</h3></legend>
                <table class="table"  id="credit-memo-returns">
                    <thead>
                        <tr class="active">
                            <th style="width:5%"></th>
                            <th style="width:20%">Item</th>
                            <th style="width:10%">Packaging</th>
                            <th style="width:11%">Net UPrice</th>
                            <th style="width:12%">Return Qty</th>
                            <th style="width:20%">Remarks</th>
                            <th style="width:15%">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total_returns = 0; ?>
                        <?php foreach ($credit_memo['returned'] as $returns): ?>
                            <?php $disabled = cif(doubleval($returns['quantity']) > 0, '', 'disabled="disabled"'); ?>
                            <tr>
                                <td><input type="checkbox" class="enable" <?= cif($disabled === '', 'checked="checked"', '') ?>/></td>
                                <?php $net_unit_price = doubleval($returns['product_unit_price']) - doubleval($returns['product_unit_discount']) ?>
                                <td>
                                    <input name="returns[item_delivery_id][]" type="hidden" value="<?= $returns['item_delivery_id'] ?>" <?= $disabled ?> required="required"/>
                                    <?= $returns['product'] ?>
                                </td>
                                <td><?= $returns['product_unit_description'] ?></td>
                                <td class="unit-price"><?= number_format($net_unit_price, 2) ?></td>
                                <td><input name="returns[quantity][]" type="number" min="0.01" step="0.01" class="form-control quantity" value="<?= $returns['quantity'] ?>" <?= $disabled ?> required="required"/></td>
                                <td><input name="returns[remarks][]" type="text" class="form-control" value="<?= $returns['remarks'] ?>" <?= $disabled ?> required="required"/></td>
                                <td><?= number_format(doubleval($returns['quantity']) * $net_unit_price, 2) ?></td>
                                <?php if(!$disabled):?>
                                    <?php $total_returns += ($net_unit_price*doubleval($returns['quantity'])); ?>
                                <?php endif;?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5"></td><td  class="active text-bold">Total</td><td  class="active text-right text-bold"><?= number_format($total_returns, 2)?></td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
            <fieldset>
                <legend><h3>Other Fees</h3></legend>
                <table class="table" id="credit-memo-others">
                <thead>
                    <tr class="active">
                        <th style="width:30%">Description</th>
                        <th style="width:35%">Remarks</th>
                        <th style="width:30%">Amount</th>
                        <th style="width:5%"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $disabled = '';?>
                    <?php if (empty($credit_memo['other_fees'])): ?>
                        <?php $disabled = 'disabled="disabled"'; ?>
                        <?php $credit_memo['other_fees'] = [[]]; ?>
                    <?php endif; ?>
                    <?php $total_others = 0;?>
                    <?php foreach ($credit_memo['other_fees'] as $others): ?>
                        <tr <?= cif(empty($others), 'class="hidden"', '') ?>>
                            <td><input name="others[description][]" type="text" class="form-control" value="<?= ckey_exists('description', $others, '') ?>" <?= $disabled ?> required="required"/></td>
                            <td><input name="others[remarks][]" type="text" class="form-control" value="<?= ckey_exists('remarks', $others, '') ?>" <?= $disabled ?>/></td>
                            <td><input name="others[amount][]" type="text" class="form-control price amount" value="<?= ckey_exists('amount', $others, '') ?>" <?= $disabled ?> required="required"/></td>
                            <td><button type="button" class="btn btn-flat btn-danger remove-line-others"><i class="fa fa-times"></i></button></td>
                            <?php $total_others += doubleval(ckey_exists('amount', $others, 0)); ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td><button type="button" class="btn btn-default btn-flat" id="add-cm-others"><i class="fa fa-plus"></i> Add new line</button></td>
                        <td colspan="1"></td><td class="active text-bold clearfix" colspan="2">Total<span class="pull-right total-amount"><?= number_format($total_others, 2)?></span></td>
                    </tr>
                </tfoot>
            </table>
            </fieldset>
        </div>
        <div class="box-footer clearfix">
            <button type="submit" class="btn btn-success btn-flat">Save and submit</button>
            <a class="btn btn-primary btn-flat pull-right" id="pl-link" href="<?=base_url("sales/deliveries/update/{$pl_id}")?>">Go to PL # <?= str_pad($pl_id, 4, 0, STR_PAD_LEFT); ?></a>
        </div>
    </form>
</div>