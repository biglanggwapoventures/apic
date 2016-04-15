<style type="text/css">
    tbody > tr > td:nth-child(4),td:nth-child(5){
        text-align: right;
        font-weight: bold;
        font-size: 110%;
    }
    thead > tr > th:nth-child(4),th:nth-child(5){
        text-align: right;
    }
    table#summary > thead >  tr > th:nth-child(2){
        font-size: 130%!important;
        text-align: right;
    }
</style>
<?php $url = base_url('production/formulations')?>
<div class="box box-solid">
    <div class="box-header  bg-light-blue-gradient" style="color:#FFFFFF!important">
        <h3 class="box-title"> <?= $title ?></h3>
    </div>
    <form class="form" data-action="<?= $action === "c" ? "{$url}/store" : "{$url}/update/{$data['formulation']['id']}" ?>" method="post">
        <div class="box-body">
            <div class="callout callout-danger hidden">
                <ul class="list-unstyled">

                </ul>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label ><i class="fa fa-asterisk text-danger"></i> Formulation Code</label>
                        <input type="text" class="form-control" name="formulation_code" required="required" value="<?= isset($data['formulation']['formulation_code']) ? $data['formulation']['formulation_code'] : ''?>"/>
                    </div>
                </div>
                <?php if(is_admin()):?>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fa fa-asterisk text-danger"></i> Status</label>
                            <?= form_dropdown('status', ['Inactive', 'Active'], isset($data['formulation']['status']) ? $data['formulation']['status'] : 0, 'class="form-control"')?>
                        </div>
                    </div>
                <?php endif;?>
            </div>
            <hr>
            <table class="table">
                <thead>
                    <tr class="active">
                        <th>Raw Product</th>
                        <th style="width:3%"></th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Unit cost</th>
                        <th>Net cost</th>
                        <th style="width:5%"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $index = $data['raw_mats'] ? count($data['raw_mats']) : 1 ?>
                    <?php $keys = array_column($raw_mats, 'id');?>
                    <?php $net_weight = 0;?>
                    <?php $net_cost = 0;?>
                    <?php foreach($data['raw_mats'] AS $key => $row):?>
                        <tr>
                            <?php $dropdown = $raw_mats;?>
                            <?php if(!in_array($row['product_id'], $keys)):?>
                                <?php $dropdown = ([['id' => $row['product_id'], 'description' => "{$row['description']} **INACTIVE**", 'unit_description' => $row['unit']]] + $dropdown)?>
                            <?php endif;?>
                            <td>
                                <input type="hidden" name="formula[<?= $key?>][id]" value="<?=$row['id']?>">
                                <?= arr_group_dropdown("formula[{$key}][fk_inventory_product_id]", $dropdown, 'id', ['text'=> 'description', 'attr' => ['value' => 'unit_description', 'name' => 'unit']], $row['product_id'], FALSE, 'class="form-control mats" required="required" data-name="formula[idx][fk_inventory_product_id]"')?>
                            </td>
                            <td><a target="_blank" data-href="<?= base_url('reports/product_inventory?product_id=pid')?>" href="<?= base_url("reports/product_inventory?product_id={$row['product_id']}")?>" class="link btn btn-sm btn-flat btn-info"><i class="fa fa-link"></i></a></td>
                            <td><input name="formula[<?= $key?>][quantity]" data-name="formula[idx][quantity]" type="number" step="0.001" min="0.001" required="required" class="form-control quantity" value="<?= $row['quantity']?>"/></td>
                            <td class="unit-description reset"><?= $row['unit']?></td>
                            <td class="reset unit-cost"><?= is_admin() ? number_format($row['cost'], 2) : '0.00'?></td>
                            <?php $line_cost = $row['cost'] * $row['quantity']; ?>
                            <td class="reset net-cost"><?= is_admin() ? number_format($line_cost, 2) : '0.00'?></td>
                            <td><a class="btn btn-sm btn-flat btn-danger remove-line"><i class="fa fa-times"></i></a></td>
                        </tr>
                        <?php $net_weight += $row['quantity']?>
                        <?php $net_cost += $line_cost?>
                    <?php endforeach;?>
                    <?php if(!$data['raw_mats']):?>
                        <tr>
                            <td><?= arr_group_dropdown('formula[0][fk_inventory_product_id]', $raw_mats, 'id', ['text'=> 'description', 'attr' => ['value' => 'unit_description', 'name' => 'unit']], FALSE, FALSE, 'class="form-control mats" required="required" data-name="formula[idx][fk_inventory_product_id]"')?></td>
                            <td><a target="_blank" data-href="<?= base_url('reports/product_inventory?product_id=pid')?>" class="link btn btn-sm btn-flat btn-info"><i class="fa fa-link"></i></a></td>
                            <td><input name="formula[0][quantity]" data-name="formula[idx][quantity]" type="number" step="0.001" min="0.001" required="required" class="form-control quantity"/></td>
                            <td class="unit-description reset"></td>
                            <td class="reset unit-cost"></td>
                            <td class="reset net-cost"></td>
                            <td><a class="btn btn-sm btn-flat btn-danger remove-line"><i class="fa fa-times"></i></a></td>
                        </tr>
                    <?php endif;?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6"><a class="btn btn-default btn-flat btn-sm add-line"><i class="fa fa-plus"></i> Add new line</a></td>
                    </tr>
                </tfoot>
            </table>
            <div class="row">
                <div class="col-sm-3 col-sm-offset-4">
                    <table class="table table-bordered" id="summary">
                        <thead>
                            <tr><th>Net weight: (in kgs)</th><th id="net-weight"><?=  number_format($net_weight, 3) ?></th></tr>
                            <tr><th>Net cost:</th><th id="net-cost"><?= is_admin() ? number_format($net_cost, 2) : '0.00'?></th></tr>
                            <tr><th>Cost per kgs</th><th id="kgs-cost"><?= is_admin() && $net_weight ? number_format($net_cost/$net_weight, 2) : '0.00'?></th></tr>
                        </thead>
                    </table>
                </div>
            </div>
            <input type="hidden" data-name="index" data-value="<?= $index?>" disabled="disabled">
            <?php if(is_admin()):?>
                <input type="hidden" data-name="get-cost-url" data-value="<?= "{$url}/get_cost/"?>" disabled="disabled">
            <?php endif; ?>
        </div>
        <div class="box-footer clearfix">
            <button type="submit" class="btn btn-flat btn-success">Submit</button>
            <a href="<?= $url ?>" class="btn btn-flat btn-danger pull-right " id="btn-cancel">Cancel</a>    
        </div>
    </form>
</div>