<style type="text/css">
    table thead th:nth-child(3),th:nth-child(4),th:nth-child(5),th:nth-child(6),th:nth-child(7),th:nth-child(8),
    table tbody td:nth-child(3),td:nth-child(4),td:nth-child(5),td:nth-child(6),td:nth-child(7),td:nth-child(8){
        text-align: right;
    }
    th.header{
        text-align: center;
        border:0!important;
        padding:0!important;
        background:#fff!important;
    }
    thead tr:not(:last-child){
        border:0!important;
    }
    span.fs130{
        font-size: 130%!important;
    }
</style>
<div class="box box-solid">
    <div class="box-body">
        <p class="text-center text-bold"></p>
        <p class="text-center"></p>
        <p class="text-center"></p>
        <table class="table table-condensed table-bordered" id="sr" style="table-layout:fixed">
            <thead>
                <tr class="active"><th colspan="8" class="header"><span class="fs130"><?= $product['description']?> <?= $product['formulation_code'] ? " [{$product['formulation_code']}]" : '' ?></span></th></tr>
                <tr class="active"><th colspan="8" class="header"><span class="fs130"><?= number_format($current_stock, 2).' '.$product['unit_description']?></span> @ <span class="fs130">PHP <?= number_format($current_cost, 2)?></span></th></tr>
                <tr class="active">
                    <th style="width: 20%">Date &amp; time</th>
                    <th></th>
                    <th>Purchase price</th>
                    <th>Production cost</th>
                    <th>Cost per bag</th>
                    <th>In</th>
                    <th>Out</th>
                    <th>Stock left</th>
                </tr>
            </thead>
            <tbody>
                <?php $temp_stock = $current_stock;?>
                <?php foreach($data AS $key => $row):?>
                    <tr>
                        <td><?= date('d-M-Y', strtotime($row['date']))?></td>
                        <td>
                            <?php if($row['pl_no']):?>
                                <a target="_blank"href="<?= base_url("sales/deliveries/update/{$row['pl_no']}")?>">
                                    <?= "Packing List # {$row['pl_no']}"?>
                                </a>
                            <?php elseif($row['sa_no']):?>
                                 <a target="_blank"href="<?= base_url("inventory/stock_adjustments/update/{$row['sa_no']}")?>">
                                    <?= "Stock Adjust # {$row['sa_no']}"?>
                                </a>
                            <?php elseif($row['rr_no']):?>
                                <a target="_blank"href="<?= base_url("purchases/receiving/manage?do=update-purchase-receiving&id={$row['rr_no']}")?>">
                                    <?= "Purchase RR # {$row['rr_no']}"?>
                                </a>
                            <?php elseif($row['jo_no']):?>
                                <a target="_blank"href="<?= base_url("production/job_order/update/{$row['jo_no']}")?>">
                                    <?= "Job Order # {$row['jo_no']}"?>
                            <?php elseif($row['prr_no']):?>
                                 <a target="_blank"href="<?= base_url("production/receiving/update/{$row['prr_no']}")?>">
                                    <?= "Production RR # {$row['prr_no']}"?>
                                </a>
                            <?php endif;?>
                        </td>
                        <td class="text-right">
                            <?php if($row['rr_no'] || ($row['sa_no'] && $row['in'] > 0)):?>
                                <?= $row['unit_price'] ? number_format($row['unit_price'], 2): ''?>
                            <?php endif;?>
                        </td>
                        <td class="text-right">
                            <?php if($row['prr_no'] && $row['prr_no'] >= 86):?>
                                <?php $cost_per_kilo = $this->receiving->get_cost($row['production_receiving_detail_id']); ?>
                                <?= number_format($cost_per_kilo,2)?>
                            <?php endif;?>
                        </td>
                        <td class="text-right">
                            <?php if($row['prr_no'] && $row['prr_no'] >= 86):?>
                                <?= number_format($cost_per_kilo/$row['in'],2)?>
                            <?php endif;?>
                        </td>
                        <td class="text-right"><?= $row['in'] > 0 ? '<span class="text-info">'.number_format($row['in'], 2).'</span>': ''?></td>
                        <td class="text-right"><?= $row['out'] > 0 ? '<span class="text-danger">'.number_format($row['out'], 2).'</span>': ''?></td>
                        <td class="text-right">
                            <?php if($key === 0):?>
                                <?= number_format($temp_stock, 2)?>
                            <?php else:?>
                                <?php $temp_stock += $data[$key-1]['out']?>
                                <?php $temp_stock -= $data[$key-1]['in']?>
                                <?= number_format($temp_stock, 2)?>
                            <?php endif;?>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>    
        <div class="box-footer">
            Time elapsed: <?= $this->benchmark->elapsed_time();?>s
        </div>
    </div>
</div> 