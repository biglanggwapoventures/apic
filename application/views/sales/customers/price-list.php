<style type="text/css">
    input.price{
        text-align: right;
    }
</style>
<div class="box box-solid">
    <div class="box-header bg-light-blue-gradient" style="color:#fff">
        <h3 class="box-title">Assign product prices to customer: <strong><?= $name; ?></strong> </h3>
    </div>
    <form data-action="<?= base_url("sales/customers/save_customer_pricing/{$customer_id}")?>">
        <div class="box-body table-responsive no-padding">
            <table class="table promix">
                <thead>
                    <tr class="info">
                        <th>Product</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $ids = array_column($active_products, 'id'); ?>
                    <?php $text_options = ['text' => 'description', 'attr' => ['name' => 'packaging', 'value' => 'unit']]?>
                    <?php foreach($price_list AS $index => $row):?>
                        <?php $temp = $active_products; ?>
                        <tr>
                            <td>
                                <input type="hidden" value="<?= $row['id']?>" name="list[<?=$index?>][id]">
                                <?php if(!in_array($row['product_id'], $ids)):?>
                                    <?php $row['id'] = $row['product_id']; ?>
                                    <?php $row['description'] .= "{$row['product_id']} **INACTIVE**"; ?>
                                    <?php $temp[] = $row; ?>
                                <?php endif;?>
                                <?= arr_group_dropdown("list[{$index}][fk_inventory_product_id]", $temp, 'id', $text_options, $row['product_id'], 'category', 'class="form-control item" required="required" data-name="list[idx][fk_inventory_product_id]"')?>
                            </td>
                            <td class="packaging"><?= $row['unit']?></td>
                            </td>
                            <td><input type="text" class="form-control price" value="<?= number_format($row['price'], 2)?>" required="required" name="list[<?=$index?>][price]" data-name="list[idx][price]" /></td>
                            <td><input type="text" class="form-control price" value="<?= number_format($row['discount'], 2)?>" name="list[<?=$index?>][discount]" data-name="list[idx][discount]" /> </td>
                            <td><a class="btn btn-sm btn-flat btn-danger remove-line"><i class="fa fa-times"></i></a></td>
                        </tr>
                    <?php endforeach;?>
                    <?php if(!$price_list):?>
                        <tr>
                            <td>
                                <?= arr_group_dropdown("list[0][fk_inventory_product_id]", $active_products, 'id', $text_options, FALSE, 'category', 'class="form-control item" required="required" data-name="list[idx][fk_inventory_product_id]"')?>
                            </td>
                            <td class="packaging"></td>
                            <td><input type="text" class="form-control price" required="required" name="list[0][price]" data-name="list[idx][price]"/></td>
                            <td><input type="text" class="form-control price" name="list[0][discount]" data-name="list[idx][discount]"/></td>
                            <td><a class="btn btn-sm btn-flat btn-danger remove-line"><i class="fa fa-times"></i></a></td
                        </tr>
                    <?php endif?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><a class="btn btn-default btn-sm btn-flat" id="add-line"><i class="fa fa-plus"></i> Add new line</a></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <input type="hidden" data-name="index" disabled="disabled" data-value="<?= $price_list ? count($price_list) : 1 ?>">
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-success btn-flat">Submit</button>
            <a href="<?=base_url('sales/customer')?>" class="btn btn-danger pull-right btn-flat">Cancel</a>
        </div>
    </form>
</div>