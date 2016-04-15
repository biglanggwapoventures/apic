<?php $action = (isset($mode) ? base_url("inventory/products/update/{$defaults['id']}") : base_url("inventory/products/add")) ?>
<div class="row">
    <div class="col-md-7">
        <div class="box box-success">
            <form class="pm-inventory-form"  method="post" action="<?= $action ?>">
                <div class="box-header">
                    <h3 class="box-title"><?= isset($mode) ? 'Update' : 'Add new ' ?> product</h3>
                </div>
                <div class="box-body">
                    <?php if (isset($validation_errors)): ?>
                        <div class="callout callout-danger">
                            <h4>Errors!</h4>
                            <ul class="list-unstyled"><?= $validation_errors ?></ul>
                        </div>

                    <?php endif; ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="product-class">Product Class</label>
                                <?= form_dropdown('class', $classes, $defaults['class'], "id='product-class' class='form-control' required='required'"); ?>
                            </div>
                        </div>
                        <div class="col-sm-6 ">
                            <div class="form-group">
                                <label for="formulation-code">Formulation Code</label>
                                <!-- <input type="text" id="formulation-code" 
                                       name="formulation_code" required="required" class="form-control finished-product-only" <?= $disabled ?>
                                       value="<?= isset($defaults['formulation_code']) ? $defaults['formulation_code'] : "" ?>" /> -->
                                <?= arr_group_dropdown('formulation_code', $formulations, 'id', 'formulation_code', $defaults['fk_production_formulation_id'],  FALSE, 'class="form-control finished-product-only"'.$disabled)?>
                            </div> 
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="product-description">Product Description</label>
                        <input type="text" id="product-description" name="description" required="required" class="form-control"
                               value="<?= $defaults['description'] ?>"/>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="product-code">Product Code</label>
                                <input type="text" id="product-code" name="code" required="required" class="form-control"
                                       value="<?= $defaults['code'] ?>"/>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="product-unit">Unit</label>
                                <?= form_dropdown('unit', $units, $defaults['unit'], "id='product-unit' class='form-control' required='required'"); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-sm-6">

                            <div class="form-group">
                                <label for="product-type">Type</label>
                                <?= form_dropdown('type', $types, isset($defaults['type']) ? $defaults['type'] : "", "id='product-types' class='form-control finished-product-only'  {$disabled} required='required'"); ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="product-category">Category</label>
                                <?= form_dropdown('category', $categories, isset($defaults['category']) ? $defaults['category'] : "", "id='product-category' class='form-control  finished-product-only'  {$disabled} required='required'"); ?>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="product-reorder-level">Reorder Level</label>
                                <input type="number" id="product-reorder-level" required="required" name="reorder_level" class="form-control"
                                       value="<?= $defaults['reorder_level'] ?>"/>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="product-reorder-level">Cost Method</label>
                                <?php $cost_method = isset($defaults['cost_method']) ? $defaults['cost_method'] : FALSE?>
                                <?= form_dropdown('cost_method', ['fifo'=>'First In First Out', 'ave' => 'Averaging'], $cost_method, 'class="form-control" required="required"')?>
                            </div>
                        </div>
                    </div>
                    <?php if(is_admin()):?>
                        <div class="checkbox">
                            <?php $checked = $defaults['status'] == 'Inactive' ? '' : 'checked="checked"'?>
                            <label><input type="checkbox" name="status" value="1" <?=$checked?>/>Mark this product as active</label>
                        </div>
                    <?php endif;?>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <button type="button submit" class="btn btn-success">Submit</button>
                    <a href="<?= base_url('inventory/products') ?>" class="btn btn-danger">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>