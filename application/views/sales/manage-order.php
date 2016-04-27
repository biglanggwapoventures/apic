<style type="text/css">
    .text-white-important{
        color:#FFFFFF!important;
    }
    table tbody td:last-child{
        text-align: center;
    }
    table tbody td:nth-child(7),td:nth-child(8),td:nth-child(6){
        text-align: right;
    }
    table thead th{
        text-align: center;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff!important;">
                <h3 class="box-title"><?= $form_title; ?></h3>
            </div>
            <?= form_open('', array('role' => 'form', 'data-action' => $url)) ?>
            <div class="box-body clearfix">
                <div class="callout callout-danger hidden" id="messages">
                    <h4>Oops!</h4>
                    <ul class="list-unstyled"></ul>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?php if(isset($order_prev_info)){ ?>
                        <?= ($order_prev_info) ? '<a href="'.$order_prev_info.'"><i class="fa fa-arrow-left"></i> Go to SO # '.$order_prev_id.'</a>' : '' ?>
                        <?php } ?>
                    </div>
                    <div class="col-sm-6 text-right">
                        <?php if(isset($order_next_info)){ ?>
                        <?= ($order_next_info) ? '<a href="'.$order_next_info.'">Go to SO # '.$order_next_id.' <i class="fa fa-arrow-right"></i></a>' : '' ?>
                        <?php } ?>
                    </div>
                </div>
                <?= ( (isset($order_prev_info) || isset($order_next_info)) && (!empty($order_prev_info) || !empty($order_next_info)) ) ? "<hr/>" : "" ?>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="customer-name">Customer</label>
                            <input type="hidden" name="data-price-list-url" disabled="disabled" value="<?= base_url('sales/customers/a_get_registered_products') ?>"/>
                            <?php $attr = 'class="form-control" id="customer-name"'; ?>
                            <?= form_dropdown('fk_sales_customer_id', $customers, $defaults['fk_sales_customer_id'], $attr) ?>
                        </div>  
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="po-number">P.O. No.</label>
                            <?= form_input(array('name' => 'po_number', 'class' => 'form-control', 'id' => 'po-number', 'value' => $defaults['po_number'])); ?>
                        </div>  
                    </div>
                    <div class="col-sm-3 col-sm-offset-2">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <?= form_input(array('name' => 'date', 'class' => 'form-control datepicker', 'id' => 'date', 'value' => $defaults['date'])); ?>
                        </div>  
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Sale agent</label>
                            <?= form_dropdown('fk_sales_agent_id', ['' => ''] + array_column($agents, 'name', 'id'),  $defaults['fk_sales_agent_id'], 'class="form-control"')?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="remarks">
                            <label for="date">Remarks</label>
                            <?= form_textarea(array('name' => 'remarks', 'class' => 'form-control ', 'id' => 'remarks', 'rows' => 3, 'resizable-x' => 'none', 'value' => $defaults['remarks'])); ?>
                        </div>  
                    </div>
                </div>
                <hr>
                <div class="row" id="sales-order-details">
                    <div class="col-md-12 table-responsive">
                        <div class="callout callout-info">
                            <strong><i class="fa fa-bullhorn"></i> Note:</strong> The discount field below refers to discount per unit.
                        </div>
                        <table class="table table-bordered table-condensed table-hover" style="border-bottom: none;border-left: none;border-right: none;">
                            <thead>
                                <tr class="info">
                                    <th style="width:19%">Product</th>
                                    <th style="width:9%">Unit</th>
                                    <th style="width:10%">Qty</th>
                                    <th style="width:15%">hds/pcs</th>
                                    <th style="width:11%">Unit Price</th>
                                    <th style="width:11%">Discount</th>
                                    <th style="width:10%">Net Unit Price</th>
                                    <th style="width:10%">Net</th>
                                    <th style="width:4%">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody id="order-line">
                                <?php $totalAmount = 0; ?>
                                <?php $ids = isset($products) ? array_column(json_decode(json_encode($products), TRUE), 'product_id') : []?>
                                <tr id="template">
                                    <td>
                                        <?php if (isset($defaults['details']['id'][0])): ?>
                                            <input type="hidden" name="details[id][]" class="detail-id" value="<?= $defaults['details']['id'][0] ?>"/>
                                        <?php endif; ?>
                                        <?php if (isset($products)): ?>
                                             <?php if(in_array($defaults['details']['fk_inventory_product_id'][0], $ids)):?>
                                                <?= generate_customer_product_dropdown('details[fk_inventory_product_id][]', $products, 'product_id', 'description', $defaults['details']['fk_inventory_product_id'][0], FALSE, 'class="form-control product-list select-clear"') ?>
                                            <?php else:?>
                                                <?= form_hidden('details[fk_inventory_product_id][]', $defaults['details']['fk_inventory_product_id'][0]); ?>
                                                <p class="form-control-static"><?= "{$defaults['details']['product_description'][0]}"?><span class="text-danger"> ** INACTIVE **</span></p>
                                            <?php endif;?>
                                        <?php endif; ?>
                                    </td>
                                    <td >
                                        <span class="unit"> <?= $defaults['details']['unit_description'][0] ?></span>
                                    </td>
                                    <td>
                                        <?= form_input(array('name' => 'details[product_quantity][]', 'type' => 'number', 'step' => '0.01', 'class' => 'form-control  input-clear for-calculation product-quantity', 'value' => $defaults['details']['product_quantity'][0])); ?>
                                    </td>
                                    <td>
                                        <?= form_input(['name' => 'details[total_units][]', 'type' => 'number', 'step' => '0.01', 'class' => 'form-control  input-clear', 'value' => (float)$defaults['details']['total_units'][0] ?: '']); ?>
                                    </td>
                                    <td >
                                        <?= form_input(array('name' => 'details[unit_price][]', 'class' => 'form-control  unit-price text-right has-amount for-calculation input-clear', 'value' => number_format($defaults['details']['unit_price'][0], 2))); ?>
                                    </td>
                                    <td >
                                        <?php if (is_adm()): ?>
                                            <?= form_input(array('name' => 'details[discount][]', 'class' => 'form-control  discount text-right has-amount for-calculation input-clear', 'value' => number_format($defaults['details']['discount'][0], 2))); ?>
                                        <?php else: ?>
                                            <?= form_input(array('name' => 'details[discount][]', 'readonly' => 'readonly', 'class' => 'form-control  discount text-right has-amount for-calculation input-clear', 'value' => number_format($defaults['details']['discount'][0], 2))); ?>
                                        <?php endif; ?>

                                    </td>
                                    <td class="net-unit-price text-clear"><?= number_format($defaults['details']['unit_price'][0] - $defaults['details']['discount'][0], 2) ?></td>
                                    <?php $grossAmount = $defaults['details']['unit_price'][0] * $defaults['details']['product_quantity'][0]; ?>
                                    <?php $formattedGrossAmount = number_format($grossAmount, 2); ?>
                                    <?php $totalDiscount = $defaults['details']['product_quantity'][0] * $defaults['details']['discount'][0]; ?>
                                    <?php $netAmount = $grossAmount - $totalDiscount; ?>
                                    <?php $formattedNetAmount = number_format(($netAmount), 2); ?>

                                    <td class="net-amount text-clear"><?= $formattedNetAmount ?></td>
                                    <td><button type='button' class='btn btn-danger btn-sm btn-flat remove-line'><i class='fa fa-times'></i></button></td>
                                    <?php $totalAmount += $netAmount; ?>
                                </tr>
                                 
                                <?php for ($x = 1; $x < count($defaults['details']['fk_inventory_product_id']); $x++): ?>
                                    <tr>
                                        <td>
                                            <?php if (isset($defaults['details']['id'][$x])): ?>
                                                <?= form_hidden('details[id][]', $defaults['details']['id'][$x]); ?>
                                            <?php endif; ?>
                                            <?php if(in_array($defaults['details']['fk_inventory_product_id'][$x], $ids)):?>
                                                <?= generate_customer_product_dropdown('details[fk_inventory_product_id][]', $products, 'product_id', 'description', $defaults['details']['fk_inventory_product_id'][$x], FALSE, 'class="form-control product-list select-clear"') ?>
                                            <?php else:?>
                                                <?= form_hidden('details[fk_inventory_product_id][]', $defaults['details']['fk_inventory_product_id'][$x]); ?>
                                                <p class="form-control-static"><?= "{$defaults['details']['product_description'][$x]}"?><span class="text-danger"> ** INACTIVE **</span></p>
                                            <?php endif;?>
                                            

                                        </td>
                                        <td>
                                             <?= $defaults['details']['unit_description'][$x] ?>
                                        </td>
                                        <td>
                                            <?= form_input(array('name' => 'details[product_quantity][]', 'type' => 'number', 'step' => '0.01', 'class' => 'form-control  input-clear for-calculation product-quantity','value' => $defaults['details']['product_quantity'][$x])); ?>
                                        </td>
                                        <td>
                                        <?= form_input(['name' => 'details[total_units][]', 'type' => 'number', 'step' => '0.01', 'class' => 'form-control  input-clear', 'value' => (float)$defaults['details']['total_units'][$x] ?: '']); ?>
                                    </td>
                                        <td>
                                            <?= form_input(array('name' => 'details[unit_price][]', 'class' => 'form-control  unit-price text-right has-amount for-calculation input-clear', 'value' => number_format($defaults['details']['unit_price'][$x], 2))); ?>
                                        </td>
                                        <td>
                                            <?php if (is_adm()): ?>
                                                <?= form_input(array('name' => 'details[discount][]', 'class' => 'form-control  discount text-right has-amount for-calculation input-clear', 'value' => number_format($defaults['details']['discount'][$x], 2))); ?>
                                            <?php else: ?>
                                                <?= form_input(array('name' => 'details[discount][]', 'readonly' => 'readonly', 'class' => 'form-control  discount text-right has-amount for-calculation input-clear', 'value' => number_format($defaults['details']['discount'][$x], 2))); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="net-unit-price text-clear"><?= number_format($defaults['details']['unit_price'][$x] - $defaults['details']['discount'][$x], 2) ?></td>
                                        <?php $grossAmount = $defaults['details']['unit_price'][$x] * $defaults['details']['product_quantity'][$x]; ?>
                                        <?php $formattedGrossAmount = number_format($grossAmount, 2); ?>
                                        <?php $totalDiscount = $defaults['details']['product_quantity'][$x] * $defaults['details']['discount'][$x]; ?>
                                        <?php $netAmount = $grossAmount - $totalDiscount; ?>
                                        <?php $formattedNetAmount = number_format(($netAmount), 2); ?>

                                        <td class="net-amount text-clear"><?= $formattedNetAmount ?></td>
                                        <td><button type='button' class='btn btn-danger btn-flat btn-sm remove-line'><i class='fa fa-times'></i></button></td>
                                        <?php $totalAmount += $netAmount; ?>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                            <tfoot>
                                <tr><td colspan="8" class="no-border"></td><td class="text-center"><a class="add-line btn btn-flat btn-primary btn-sm"><i class="fa fa-plus"></i></a></td></tr>
                                <tr>
                                    <td colspan="5" class="no-border"></td><td class="info text-center"><strong>Total</strong></td><td class="info total-amount text-right" colspan="3"><strong><?= number_format($totalAmount, 2) ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <?php if(can_set_status()):?>
                    <div class="checkbox">
                        <?php $checked = $defaults['status'] == M_Status::STATUS_APPROVED ? 'checked="checked"' : '';?>
                        <label><input type="checkbox" name="is_approved" <?= $checked?>/> Mark this sales order as <b>approved</b></label>
                    </div>
                <?php endif;?>
            </div>
            <div class="box-footer clearfix">
                <button type="submit" class="btn btn-success btn-flat">Submit</button>
                <a class="btn btn-default btn-flat pull-right" href="<?= base_url('sales/orders')?>" id="cancel">Go back</a>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<div class="modal fade bs-loading-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close hidden" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mySmallModalLabel">Loading...</h4>
            </div>
            <div class="modal-body">
                <p class="text-center">Retrieving customer's price list...</p>
            </div>
        </div>
    </div>
</div>