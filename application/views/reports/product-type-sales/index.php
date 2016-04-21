<style type="text/css">
    tr.labels > td{
        text-align: center;
        border: 1px solid black;
        padding: 4px;
    }
    table td{
        border: 1px solid black;
        padding:3px;
    }
    tbody tr td:not(:first-child){
        text-align: right;
    }
    tbody > tr > td:nth-last-child(1),
    tbody > tr > td:nth-last-child(2),
    tbody > tr > td:nth-last-child(3){
        font-weight: bold;
        font-size: 110%!important;
    }
    tfoot td{
        color: blue;
    }
    tfoot > tr:first-child > td:nth-last-child(1),
    tfoot > tr:first-child > td:nth-last-child(2),
    tfoot > tr:first-child > td:nth-last-child(3){
        font-weight: bold;
        font-size: 130%!important;
    }

    tfoot tr td:not(:first-child),
    tfoot tr:nth-child(2) td:first-child{
        text-align: right;
    }
</style>
<?php
    $selected_categories = $this->input->get('categories') ?: [];
    $selected_categories_count= count($selected_categories);
    $category_total = [];
    $total = ['kgs' => 0, 'pcs' => 0, 'value' => 0];
?>
<div class="box box-solid">
   <div class="box-body table-responsive">
        <div class="row">
            <div class="col-sm-12">
                <table class="table-striped" style="width:100%">
                    <thead>
                        <?php $colspan = 4 + ($selected_categories_count * 3)?>
                        <tr><th colspan="<?= $colspan?>" class="text-center"><h4 style="margin-bottom:0">PRODUCT TYPE SALES</h4></th></tr>
                        <tr>
                            <th  colspan="<?= $colspan?>" class="text-center text-primary" style="padding-bottom:20px">
                                <a data-toggle="modal" data-target="#select-date" style="text-decoration:underline;">
                                <?= $this->input->get('start_date') ? date('F d, Y', strtotime($this->input->get('start_date'))) : 'Start of time'  ?> - <?= $this->input->get('end_date') ? date('F d, Y', strtotime($this->input->get('end_date'))) : date('F d, Y')?>
                                </a>
                            </th>
                        </tr>
                        
                        <tr class="labels">
                            <td rowspan="2">CUSTOMER</td>
                            <?php foreach($selected_categories AS $cat):?>
                                <td colspan="3"><?= $categories[$cat]['description']?></td>
                            <?php endforeach;?>
                            <td colspan="3">TOTAL</td>
                        </tr>
                        <tr class="labels">

                            <?php for($x = 0; $x < $selected_categories_count; $x++):?>
                                <td>KGS TOTAL</td>
                                <td>PCS TOTAL</td>
                                <td>PESO VALUE</td>
                            <?php endfor;?>

                            <td>KGS TOTAL</td>
                            <td>PCS TOTAL</td>
                            <td>PESO VALUE</td>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data AS $row):?>
                            <tr>
                                <td><?= $customers[$row['customer_id']]?></td>
                                <?php
                                    $customer_kgs_total = 0;
                                    $customer_pcs_total = 0;
                                    $customer_value_total = 0;
                                ?>
                                <?php foreach($selected_categories AS $cat):?>
                                    <?php if(isset($row[$categories[$cat]['description']])):?>
                                        <td>
                                            <?php 
                                                $kgs = floatval($row[$categories[$cat]['description']]['kilograms_total']);
                                                $customer_kgs_total += $kgs;
                                            ?>
                                            <?= number_format($kgs, 2) ?>
                                            <?php 
                                                if(isset($category_total[$categories[$cat]['description']]['kilograms_total'])){
                                                    $category_total[$categories[$cat]['description']]['kilograms_total'] += $kgs;
                                                }else{
                                                    $category_total[$categories[$cat]['description']]['kilograms_total'] = $kgs;
                                                }
                                                $total['kgs'] += $kgs;
                                            ?>
                                        </td>   
                                        <td>
                                            <?php 
                                                $pcs = floatval($row[$categories[$cat]['description']]['pieces_total']); 
                                                $customer_pcs_total += $pcs;
                                            ?>
                                            <?= number_format($pcs, 2) ?>
                                            <?php 
                                                if(isset($category_total[$categories[$cat]['description']]['pieces_total'])){
                                                    $category_total[$categories[$cat]['description']]['pieces_total'] += $pcs;
                                                }else{
                                                    $category_total[$categories[$cat]['description']]['pieces_total'] = $pcs;
                                                }
                                                $total['pcs'] += $pcs;
                                            ?>
                                        </td>  
                                        <td>
                                            <?php 
                                                $value = floatval($row[$categories[$cat]['description']]['value_total']); 
                                                $customer_value_total += $value;
                                            ?>
                                            <?= number_format($value, 2) ?>
                                            <?php 
                                                if(isset($category_total[$categories[$cat]['description']]['value_total'])){
                                                    $category_total[$categories[$cat]['description']]['value_total'] += $value;
                                                }else{
                                                    $category_total[$categories[$cat]['description']]['value_total'] = $value;
                                                }
                                                $total['value'] += $value;
                                            ?>
                                        </td>   
                                    <?php else:?>
                                        <td>0.00</td><td>0.00</td><td>0.00</td> 
                                    <?php endif;?>
                                <?php endforeach;?>
                                <td><?= number_format($customer_kgs_total, 2)?></td>
                                <td><?= number_format($customer_pcs_total, 2)?></td>
                                <td><?= number_format($customer_value_total, 2)?></td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                     <tfoot>
                        <tr>
                            <td rowspan="2" class="text-center">TOTAL</td>
                            <?php foreach($selected_categories AS $cat):?>
                                <?php if(isset($category_total[$categories[$cat]['description']])):?>
                                    <td>

                                        <?= number_format($category_total[$categories[$cat]['description']]['kilograms_total'], 2)?>
                                    </td>
                                    <td>
                                        <?= number_format($category_total[$categories[$cat]['description']]['pieces_total'], 2)?>
                                    </td>
                                    <td>
                                        <?= number_format($category_total[$categories[$cat]['description']]['value_total'], 2)?>
                                    </td>
                                <?php else:?>
                                    <td>0.00</td><td>0.00</td><td>0.00</td> 
                                <?php endif;?>
                            <?php endforeach;?>
                            <td rowspan="2"><?= number_format($total['kgs'], 2)?></td>
                            <td rowspan="2"><?= number_format($total['pcs'], 2)?></td>
                            <td rowspan="2"><?= number_format($total['value'], 2)?></td>
                        </tr>
                        <tr>
                            <?php foreach($selected_categories AS $cat):?>
                                <?php if(isset($category_total[$categories[$cat]['description']])):?>
                                    <td>
                                        <?= $total['kgs'] ? number_format(($category_total[$categories[$cat]['description']]['kilograms_total'] / $total['kgs']) * 100, 2): 0?>%
                                    </td>
                                    <td>
                                        <?= $total['pcs'] ? number_format(($category_total[$categories[$cat]['description']]['pieces_total'] / $total['pcs']) * 100, 2): 0?>%
                                    </td>
                                    <td>
                                        <?= $total['value'] ? number_format(($category_total[$categories[$cat]['description']]['value_total'] / $total['value']) * 100, 2): 0?>%
                                    </td>
                                <?php else:?>
                                    <td>0.00</td><td>0.00</td><td>0.00</td> 
                                <?php endif;?>
                            <?php endforeach;?>
                        </tr>
                        
                    </tfoot>
                </table>
                Time elapsed: <?= $this->benchmark->elapsed_time();?>s
            </div>
        </div>
   </div>
</div>

<div class="modal fade" id="select-date" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Select date range</h4>
            </div>
            <form action="<?= current_url()?>" method="GET">
                <div class="modal-body">
                    <div class="form-group">
                       <label>Start date</label>
                       <input type="text" class="form-control datepicker"  name="start_date" value="<?= $this->input->get('start_date')?>"/>
                    </div>
                    <div class="form-group">
                       <label>End date</label>
                       <input type="text" class="form-control datepicker" name="end_date" value="<?= $this->input->get('end_date')?>"/>
                    </div>
                    <?php foreach($categories AS $c):?>
                        <?php $checked = in_array($c['id'], $selected_categories) ? 'checked="checked"' : ''?>
                        <div class="checkbox">
                            <label>
                                <input name="categories[]" type="checkbox" value="<?= $c['id']?>" <?= $checked?>/> <?= $c['description']?>
                            </label>
                        </div>
                    <?php endforeach;?>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-flat">Generate</button>
                    <button type="button" data-dismiss="modal" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($){
        $(document).ready(function(){
            $('.datepicker').datepicker({dateFormat:'yy-mm-dd'});
        })
    })(jQuery)
</script>