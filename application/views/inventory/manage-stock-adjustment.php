<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header  bg-light-blue-gradient" style="color:#FFFFFF!important">
                <h3 class="box-title">
                    <?= $form_title?>
                </h3>
            </div>
            <form action="<?=$form_action?>">
                <div class="box-body">
                    <div class="callout callout-danger hidden"><ul class="list-unstyled"></ul></div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date</label>
                                <p class="form-control-static"><?= isset($data['sa']['date']) ? $data['sa']['date']: date('m/d/Y')?></p>
                            </div>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr class="active">
                                <th style="width:20%">Item</th>
                                <th style="width:15%">Packaging</th>
                                <th style="width:15%">Quantity</th>
                                <th style="width:15%">Unit Price</th>
                                <th style="width:30%">Remarks</th>
                                <th style="width:5%"></th>
                            </tr>
                        </thead>
                        <?php $text_options = ['text' => 'description', 'attr' => ['name' => 'packaging', 'value' => 'unit']]?>
                        <?php $details = isset($data['details']) ? $data['details'] : [[]];?>
                        <tbody id="adjustment-details">
                        <?php foreach($details as $row):?>
                            <tr>
                                <td><?= arr_group_dropdown('items[]', $products, 'id', $text_options, isset($row['product_id']) ? $row['product_id'] : FALSE, 'category', 'class="form-control items" required="required"')?></td>
                                <td class="packaging"></td>
                                <td><input type="number" name="quantity[]" step="0.01" value="<?= isset($row['quantity']) ? $row['quantity'] : '' ?>" class="form-control" required="required"></td>
                                
                                <td><input name="unit_price[]" type="text" class="form-control price text-right"  value="<?= isset($row['unit_price']) ? number_format($row['unit_price'], 2) : '' ?>"  required="required"/></td>
                                <td><input name="remarks[]" type="text" class="form-control"  value="<?= isset($row['remarks']) ? $row['remarks'] : '' ?>"  required="required"/></td>
                                <td>
                                <?php if(isset($row['id'])):?>
                                    <input type="hidden" name="detail_id[]" value="<?= $row['id']?>"/>
                                <?php endif;?>
                                    <a class="btn btn-flat btn-sm btn-danger remove-line"><i class="fa fa-times"></i></a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                        <tfoot><tr><td colspan="5"><a class="btn btn-default btn-flat" id="new-line"><i class="fa fa-plus"></i> Add new line</a></td></tr></tfoot>
                    </table>
                    <hr/>
                    <div class="checkbox">
                        <?php $checked = isset($data['sa']['approved_by']) && $data['sa']['approved_by'] ? 'checked="checked"':''?>
                        <label><input type="checkbox" name="is_approved" value="1" <?= $checked?>/> Mark this request as approved</label>
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