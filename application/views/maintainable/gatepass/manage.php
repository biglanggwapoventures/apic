<?php $url = base_url('maintainable/gatepass'); ?>
<div class="row">
    <div class="col-md-6">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff">
                <h3 class="box-title"><?= $form_title?></h3>
            </div><!-- /.box-header -->
            <form id="form" data-action="<?= $form_action?>" data-available="<?="{$url}/ajax_get_available/"?>">
                <div class="box-body">
                    <?php if(isset($gp['type']) && $gp['type'] === 'others'):?>
                        <?php $type = 'Others'?>
                        <?php $pls_hidden = 'hidden'?>
                        <?php $pls_disabled = 'disabled="disabled"'?>
                     <?php else:?>
                        <?php $type = 'Packing List'?>
                        <?php $others_hidden = 'hidden'?>
                        <?php $others_disabled= 'disabled="disabled"'?>
                     <?php endif;?>
                    <div class="callout callout-danger hidden"><ul class="list-unstyled"></ul></div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Gatepass type</label>
                                <?php if(isset($gp['type'])):?>
                                    <p class="form-control-static"><?= $type?></p>
                                <?php else:?>
                                    <?= form_dropdown('type', [''=>'', 'pl' => 'Packing List', 'others' => 'Others'], isset($gp['type']) ? $gp['type'] : FALSE, 'class="form-control" required="required" id="gp-type"')?>
                                <?php endif;?>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Customer</label>
                                <?php if(isset($gp['customer'])):?>
                                    <p class="form-control-static"><?= $gp['customer']?></p>
                                <?php else:?>
                                    <?= arr_group_dropdown('customer_id', $customers, 'id', 'company_name', FALSE, FALSE, 'class="form-control" required="required" id="customer"'.(isset($pls_disabled) ? $pls_disabled : ''))?>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Trucking</label>
                                <?= arr_group_dropdown('trucking', $drivers, 'id', 'trucking_name', isset($gp['trucking']) ? $gp['trucking'] : FALSE, FALSE, 'class="form-control"');?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Issued to</label>
                                <input type="text" class="form-control" name="issued_to" required="required" value="<?= isset($gp['issued_to']) && $gp['issued_to'] ? $gp['issued_to'] : ''?>" <?=isset($others_disabled) ? $others_disabled : ''?>/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea class="form-control" name="remarks"><?= isset($gp['remarks']) && $gp['remarks'] ? $gp['remarks'] : '' ?></textarea>
                    </div>
                     <div class="row">
                        <div class="col-md-12 <?=isset($others_hidden) ? $others_hidden : ''?>" id="gp-others">
                             <table class="table">
                                <thead><tr class="active"><th>Description</th><th>Quantity</th><th></th></tr></thead>
                                <tbody>
                                    <?php $items = isset($gp['items']) && $gp['items'] ? $gp['items'] : [[]];?>
                                    <?php foreach($items AS $i):?>
                                        <tr>
                                            <td><input value="<?= isset($i['description']) ? $i['description'] : '' ?>" type="text" name="items[description][]" class="form-control" required="required" <?=isset($others_disabled) ? $others_disabled : ''?>/></td>
                                            <td><input value="<?= isset($i['quantity']) ? $i['quantity'] : '' ?>" type="number" name="items[quantity][]" step="0.01" class="form-control" equired="required" <?=isset($others_disabled) ? $others_disabled : ''?> /></td>
                                            <td><a class="btn btn-sm btn-flat btn-danger remove-line"><i class="fa fa-times"></i></a></td>
                                        </tr>
                                    <?php endforeach;?>
                                </tbody>
                                <tfoot><tr><td colspan="3"><a class="btn btn-sm btn-flat btn-default new-line"><i class="fa fa-plus"></i> Add new line</a></td></tr></tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 <?=isset($pls_hidden) ? $pls_hidden : ''?>" id="gp-pl">
                             <table class="table">
                                <tbody>
                                    <?php if(isset($gp['pls'])):?>
                                            <?php foreach($gp['pls'] AS $p):?>
                                                <tr><td ><div class="checkbox"><label><input type="checkbox" name="pl_id[]" value="<?= $p['pl_id']?>" checked="checked">Packing List # <?= $p['pl_id']?></label></div></td></tr>
                                            <?php endforeach;?>
                                    <?php endif;?>
                                    <?php if(isset($available)):?>
                                            <?php foreach($available AS $p):?>
                                                <tr><td ><div class="checkbox"><label><input type="checkbox" name="pl_id[]" value="<?= $p?>">Packing List # <?= $p?></label></div></td></tr>
                                            <?php endforeach;?>
                                    <?php endif;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!-- /.box-body -->  
                <div class="box-footer clearfix">
                    <button type="submit" class="btn btn-success btn-flat">Submit</button>
                    <a href="<?= $url?>" class="btn btn-warning btn-flat pull-right cancel">Go back</a>
                </div><!-- /.box-body --> 
            </form>
        </div>
    </div>

    
</div>
