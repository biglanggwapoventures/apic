<style type="text/css">
    td:nth-child(2){
        text-align: center;
    }
    div.t{
        border-bottom:1px solid black;
    }
</style>
<input type='hidden' name='data-bank-list-url' value='<?= base_url('accounting/bank_accounts/a_get') ?>'/>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff!important;">
                <h3 class="box-title"><?= $form_title; ?></h3>
            </div>
            <input type="hidden" name="data-parent-url" value="<?= base_url('sales/deliveries') ?>" disabled="disabled"/>
            <?= form_open($url, array('role' => 'form')) ?>
            <div class="box-body">
                <div class="callout callout-danger hidden">
                    <h4>Error!</h4>
                    <ul class="list-unstyled error-list"></ul>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer-name">Customer</label>
                            <input type="hidden" name="data-list-so-url" value="<?= base_url('sales/customers/a_get_undelivered_so') ?>" disabled="disabled"/>
                            <?php if ($defaults['fk_sales_order_id']): ?>
                                <p class="form-control-static"><?= $defaults['company_name'] ?></p>
                                <input type="hidden" class="form-control" name="customer-id" value="<?= $defaults['customer_id'] ?>">
                            <?php else: ?>
                                <?php $attr = 'class="form-control" id="customer-name" required="required"'; ?>
                                <?= form_dropdown('customer-id', $customers, FALSE, $attr) ?>
                            <?php endif; ?>
                        </div>  
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="so-number">S.O. No.</label>
                            <input type="hidden" name="data-so-details-url" value="<?= base_url('sales/orders/a_fetch_details') ?>" disabled="disabled"/>
                            <?php if ($defaults['fk_sales_order_id']): ?>
                                <input type="hidden" name="fk_sales_order_id" value="<?= $defaults['fk_sales_order_id'] ?>"/>
                                <p class="form-control-static"><?= str_pad($defaults['fk_sales_order_id'], 4, "0", STR_PAD_LEFT) ?></p>
                            <?php else: ?>
                                <?php $attr = 'class="form-control" id="so-number" required="required"'; ?>
                                <?= form_dropdown('fk_sales_order_id', array('' => 'Please select a customer.'), FALSE, $attr) ?>
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
                <div class="row"><div class="col-md-3">
                        <div class="form-group">
                            <label for="delivered-by">Delivered by</label>
                            <?php $truckers = dropdown_format($truckers, 'id', array('trucking_name', 'plate_number')) ?>
                            <?= generate_dropdown('fk_sales_trucking_id', $truckers, (int) $defaults['fk_sales_trucking_id'], "class='form-control' id='delivered-by'", 'trucker') ?>
                        </div>  
                    </div><div class="col-md-3">
                        <div class="form-group">
                            <label for="invoice-number">Invoice No.</label>
                            <?= form_input(array('name' => 'invoice_number', 'class' => 'form-control', 'id' => 'invoice-number', 'value' => $defaults['invoice_number'])); ?>
                        </div>  
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="remarks">
                            <label for="date">Remarks</label>
                            <?= form_textarea(array('name' => 'remarks', 'class' => 'form-control', 'id' => 'remarks', 'rows' => 3, 'resizable-x' => 'none', 'value' => $defaults['remarks'])); ?>
                        </div>  
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                         <table class="table table-bordered table-condensed" style="border-bottom: none;border-left: none;border-right: none;margin-bottom: 50px;margin-top:20px;">
                            <thead><tr class="active">
                                    <th>Product</th>
                                    <th>Units</th>
                                    <th>Ordered</th>
                                    <th>Delivered</th>
                                    <th>This Delivery</th>
                                    <th>Unit Price</th>
                                    <th>Discount</th>
                                    <th>Net Unit Price</th>
                                    <th>Gross</th>
                                    <th>Net</th>
                                </tr></thead>

                            <tbody id='pl-details'>
                                <?php $totalAmount = 0 ?>
                                <?php if (isset($defaults['details'])): ?>
                                    <?php $count = count($defaults['details']['product_description']); ?>
                                    <?php for ($x = 0; $x < $count; $x++): ?>
                                        <?php $d = $defaults['details']; ?>
                                        <tr>

                                            <td><?= "{$d['prod_descr'][$x]} [" . ($d['prod_formu_code'][$x] ? $d['prod_formu_code'][$x] : $d['prod_code'][$x]) . "]" ?><input type="hidden" name="details[id][]" value="<?= $d['id'][$x] ?>"></td>

                                            <td><div class="t"><?= $d['unit_description'][$x] ?></div>hds/pcs</td>

                                            <td><div class="t"><?= $d['product_quantity'][$x] ?></div><?= $d['total_units'][$x] ?></td>

                                            <td><div class="t"><?= $d['quantity_delivered'][$x] ?></div><?= $d['units_delivered'][$x] ?></td>

                                            <td>
                                                <div class="t"><input type="number" step="0.01" class="form-control this-delivery text-right" value="<?= $d['this_delivery'][$x] ?>" name="details[this_delivery][]"></div><input type="number" step="0.01" class="form-control" value="<?= $d['delivered_units'][$x] ?>" name="details[delivered_units][]"></td>
                                            <td class="text-right"><span class="unit-price"><?= number_format($d['unit_price'][$x], 2) ?></span></td>
                                            <?php $totalDiscount = ($d['this_delivery'][$x] * $d['discount'][$x]); ?>
                                            <?php $grossAmount = ($d['unit_price'][$x] * $d['this_delivery'][$x]); ?>
                                            <?php $formattedGrossAmount = number_format($grossAmount, 2); ?>
                                            <?php $netAmount = ($grossAmount - $totalDiscount); ?>
                                            <?php $formattedNetAmount = number_format($netAmount, 2); ?>
                                            <td class="text-right"><span class="discount"><?= number_format($d['discount'][$x], 2) ?></span></td>
                                            <td class="text-right"><span class="net-unit-price"><?= number_format($d['unit_price'][$x] - $d['discount'][$x], 2) ?></span></td>
                                            <td class="text-right"><span class="gross-amount"><?= $formattedGrossAmount ?></span></td>
                                            <td class="text-right"><span class="net-amount"><?= $formattedNetAmount ?></span><input type="hidden" name="details[fk_sales_order_detail_id][]" value="<?= $d['fk_sales_order_detail_id'][$x] ?>"></td>
                                        </tr>
                                        <?php $totalAmount+=$netAmount; ?>
                                    <?php endfor; ?>

                                <?php elseif (!isset($defaults['details'])): ?>
                                    <tr><td colspan="10" class="text-center">Please select a customer and his/her corresponding S.O. No.</td></tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="7" class="no-border"></td>
                                    <td class="active total-amount text-right no-border text-bold" colspan="3">
                                        <?= number_format($totalAmount, 2) ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <?php if (array_key_exists('details', $defaults)): ?>   
                    <fieldset class="">
                        <legend class="clearfix">Credit Memo
                            <small  class="pull-right"> 
                                <a href="<?=base_url("sales/deliveries/print_credit_memo/{$defaults['id']}")?>" id="print-credit-memo"><i class="fa fa-print"></i> Print | </a>
                                <a href="<?=base_url("sales/deliveries/credit_memo/{$defaults['id']}")?>" ><i class="fa fa-pencil"></i> Update</a>
                            </small>
                        </legend>
                            <table class="table table-condensed table-bordered" style="table-layout:fixed;border-bottom:0;border-left:0;margin-bottom:50px;">
                                <tbody>
                                    <tr><td class="text-center">Amount for returned items: </td><td class="text-right text-bold"><?=number_format($credit_memo['returns_total_amount'], 2)?></td><tr>
                                    <tr><td class="text-center">Amount for other fees: </td><td class="text-right text-bold"><?=number_format($credit_memo['others_total_amount'], 2)?></td><tr>
                                    <tr><td class="text-bold text-center no-border"></td><td class="text-bold text-right active"><?= number_format(doubleval($credit_memo['others_total_amount'])+doubleval($credit_memo['returns_total_amount']), 2)?></td></tr>
                                </tbody>
                            </table>
                    </fieldset>
                <?php endif; ?>
            </div>
            <div class="box-footer">
                <?= form_button(array('type' => 'submit', 'class' => 'btn btn-primary btn-flat', 'content' => 'Save')); ?>
                <a href="<?= base_url('sales/deliveries') ?>" class="btn btn-danger pull-right btn-flat">Go back</a>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>