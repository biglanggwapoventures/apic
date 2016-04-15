<input type='hidden' value='<?= base_url('warehousing/gatepass') ?>' name='redirect-url'/>
<div class="row">
    <div class="col-md-7">
        <form class='form' method='post' action='<?= $form_action ? $form_action : '' ?>'>
            <div class="box box-info">
                <div class="box-header">
                    <h3 class='box-title'><?= $form_title ? $form_title : '' ?></h3>
                </div><!-- /.box-heading -->
                <div class="box-body">
                    <div class='row'>
                        <div class='col-md-12'>
                            <div class='callout callout-danger hidden'>
                                <h4> Errors have occurred!</h4>
                                <ul class='list-unstyled'>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-5'>
                            <div class="form-group">
                                <label class='control-label' for='delivered-by'>Delivered by:</label>
                                <?php if (is_array($trucking_list) && !empty($trucking_list)): ?>
                                    <?php $trucking_list = dropdown_format($trucking_list, 'id', array('trucking_name', 'plate_number')) ?>
                                    <?= generate_dropdown('fk_sales_trucking_id', $trucking_list, $defaults['fk_sales_trucking_id'], 'class="form-control" required') ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class='col-md-5'>
                            <div class="form-group">
                                <label class='control-label' for='exit-time'>Exit datetime:</label>
                                <input type='text' class='form-control datetimepicker' id='exit-time' name='exit_datetime' required/>
                            </div>
                        </div>
                    </div><!-- /.row -->
                    <div class='row'>
                        <div class='col-md-10'>
                            <div class="form-group">
                                <label class='control-label' for='truck-boy'>Truck boy:</label>
                                <input type='text' class='form-control' id='truck-boy' name='truck_boy' required/>
                            </div>
                        </div>
                    </div><!-- /.row -->
                    <hr>
                    <div class='row'>
                        <div class='col-md-12'>
                            <table class='table table-hover table-bordered adrian-gwapo'>
                                <thead><tr class='info'><th class='width-60'>Product Description (Code) (Packaging)</th><th>Quantity</th><th class='width-5'></th></tr></thead>
                                <tfoot><tr>
                                        <td style="border:none!important;" colspan="2"></td><td class="text-center">
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm add-line" role="button"><i class="fa fa-plus"></i></a>
                                        </td>
                                    </tr></tfoot>
                                <tbody>
                                    <tr class='no-remove'>
                                        <td>
                                            <?php if (is_array($product_list) && !empty($product_list)): ?>
                                                <?php $product_list = dropdown_format($product_list, 'id', array('description', 'code', 'unit_description')) ?>
                                                <?= generate_dropdown('details[fk_inventory_product_id][]', $product_list, '', 'class="form-control" required') ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <input type='number' name='details[quantity][]' class='form-control ' required/>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" class="btn btn-danger btn-sm remove-line" role="button"><i class="fa fa-times"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <input type='submit' value='Save' class='btn btn-success'/>
                    <a href='<?= base_url('warehousing/gatepass') ?>' class='btn btn-danger pull-right' role='button'>Go back</a>
                </div><!-- /.box-footer -->
            </div><!-- /.box -->
        </form>
    </div><!-- /.col-md-8 -->
</div><!-- /.row -->