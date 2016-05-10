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
                    <table class="table table-bordered" style="border-bottom:none;border-right:none;border-left:none">
                        <thead>
                            <tr class="active">
                                <th>ITEM</th>
                                <th>UNIT</th>
                                <th>UNIT QTY</th>
                                <th>PIECES</th>
                                <th>UNIT PRICE</th>
                                <th>REMARKS</th>
                                <th style="width:5%"></th>
                            </tr>
                        </thead>
                        <?php $text_options = ['text' => 'description', 'attr' => ['name' => 'packaging', 'value' => 'unit']]?>
                        <?php $details = isset($data['details']) ? $data['details'] : [[]];?>
                        <tbody id="adjustment-details">
                        <?php foreach($details as $index => $row):?>
                            <tr>
                                <td>
                                    <?= arr_group_dropdown("items[{$index}][product_id]", $products, 'id', $text_options, put_value($row, 'product_id', FALSE), 'category', 'class="form-control items" data-name="items[idx][product_id]"')?>
                                </td>

                                <td class="packaging"></td>
                                
                                <td>
                                    <?php $quantity = put_value($row, 'quantity', 0)?>
                                    <?= form_input("items[{$index}][quantity]", (float)$quantity ?: '', 'class="form-control text-right" data-name="items[idx][quantity]"')?>
                                </td>

                               
                                <td>
                                    <?php $pieces = put_value($row, 'pieces', 0)?>
                                    <?= form_input("items[{$index}][pieces]", (float)$pieces ?: '', 'class="form-control text-right" data-name="items[idx][pieces]"')?>
                                </td>
                                
                                <td>
                                    <?php $unit_price = put_value($row, 'unit_price', 0)?>
                                    <?= form_input("items[{$index}][unit_price]",  (float)$unit_price ?: '', 'class="form-control text-right" data-name="items[idx][unit_price]"')?>
                                </td>

                                <td>
                                    <?php $remarks = put_value($row, 'remarks', '')?>
                                    <?= form_input("items[{$index}][remarks]",  $remarks, 'class="form-control" data-name="items[idx][remarks]"')?>
                                </td>
                                <td>
                                <?php if(isset($row['id'])):?>
                                    <?= form_hidden("items[{$index}][id]", $row['id'])?>
                                <?php endif;?>
                                    <a class="btn btn-flat btn-sm btn-danger remove-line"><i class="fa fa-times"></i></a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                        <tfoot style="border:none"><tr style="border:none"><td style="border:none" colspan="7"><a class="btn btn-default btn-flat" id="new-line"><i class="fa fa-plus"></i> Add new line</a></td></tr></tfoot>
                    </table>
                    <?php if(can_set_status()):?>
                        <hr/>
                        <div class="checkbox">
                            <?php $checked = isset($data['sa']['approved_by']) && $data['sa']['approved_by'] ? 'checked="checked"':''?>
                            <label><input type="checkbox" name="is_approved" value="1" <?= $checked?>/> Mark this request as approved</label>
                        </div>
                    <?php endif;?>
                </div>
                <div class="box-footer clearfix">
                    <button type="submit" class="btn btn-success btn-flat">Submit</button>
                    <a href="<?= $url ?>" id="cancel" role="button" class="btn btn-warning btn-flat pull-right">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>