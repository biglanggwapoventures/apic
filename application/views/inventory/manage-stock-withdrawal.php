<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header  bg-light-blue-gradient" style="color:#FFFFFF!important">
                <h3 class="box-title">
                    <?= $form_title?>
                </h3>
            </div>
            <form action="<?=$form_action?>" method="post">
                <div class="box-body">
                    <div class="callout callout-danger hidden"><ul class="list-unstyled"></ul></div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date</label>
                                <p class="form-control-static"><?= isset($data['sw']['date']) ? $data['sw']['date']: date('m/d/Y')?></p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea class="form-control" name="remarks"><?= isset($data['sw']['remarks']) ? $data['sw']['remarks'] : ''?></textarea>
                    </div>
                    <table class="table">
                        <thead>
                            <tr class="active">
                                <th style="width:25%">Item</th>
                                <th style="width:25%">Quantity</th>
                                <th style="width:15%">Packaging</th>
                                <th style="width:20%">Unit Price</th>
                                <th style="width:20%">Amount</th>
                                <th style="width:5%"></th>
                            </tr>
                        </thead>
                        <?php $text_options = ['text' => 'description', 'attr' => ['name' => 'packaging', 'value' => 'unit_description']]?>
                        <?php $details = isset($data['details']) ? $data['details'] : [[]];?>
                        <tbody id="withdrawal-details">
                        <?php $total = 0;?>
                        <?php foreach($details as $row):?>
                            <tr>
                                <?php $amount = (isset($row['unit_price']) ? $row['unit_price'] : 0) * (isset($row['quantity']) ? $row['quantity'] : 0);?>
                                <td><?= arr_group_dropdown('items[]', $products, 'id', $text_options, isset($row['product_id']) ? $row['product_id'] : FALSE, 'class_description', 'class="form-control items" required="required"')?></td>
                                <td><input type="number" name="quantity[]" step="0.01" min="1" value="<?= isset($row['quantity']) ? $row['quantity'] : '' ?>" class="form-control quantity" required="required"></td>
                                <td class="packaging"></td>
                                <td><input name="unit_price[]" type="text" class="form-control price text-right"  value="<?= isset($row['unit_price']) ? $row['unit_price'] : '' ?>"  required="required"/></td>
                                <td class="amount text-right"><?= number_format($amount, 2)?></td>
                                <?php $total += $amount;?>
                                <td>
                                <?php if(isset($row['id'])):?>
                                    <input type="hidden" name="detail_id[]" value="<?= $row['id']?>"/>
                                <?php endif;?>
                                    <a class="btn btn-flat btn-sm btn-danger remove-line"><i class="fa fa-times"></i></a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"><a class="btn btn-default btn-flat" id="new-line"><i class="fa fa-plus"></i> Add new line</a></td>
                                <td class="text-bold text-right">Total:</td>
                                <td colspan="" id="total-amount" class="text-right text-bold"><?= number_format($total, 2)?></td><td></td>
                            </tr>
                        </tfoot>
                    </table>
                    <hr/>
                    <div class="checkbox">
                        <?php $checked = isset($data['sw']['approved_by']) && $data['sw']['approved_by'] ? 'checked="checked"':''?>
                        <label><input type="checkbox" name="is_approved" value="1" <?= $checked?>/> Mark this as approved</label>
                    </div>
                </div>
                <div class="box-footer clearfix">
                    <button type="submit" class="btn btn-success btn-flat">Submit</button>
                    <a href="<?= $url ?>" id="cancel" role="button" class="btn btn-warning btn-flat pull-right">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>