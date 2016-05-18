<style type="text/css">
    table > tbody > td:nth-child(3),td:nth-child(4),td:nth-child(5),td:nth-child(6),td:nth-child(7),td:nth-child(8){
        text-align: right;
    }
    th{
        text-align: center;
    }
    th.header{
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
        <table class="table table-condensed table-bordered" id="sr" style="table-layout:fixed;border:none;">
            <thead>
                <tr class="active">
                    <th colspan="9" class="header"><span class="fs130">
                        <?= "{$product['description']} [{$product['code']}]" ?></span>
                    </th>
                </tr>
                <tr class="active">
                    <th colspan="9" class="header">
                        <span class="fs130">
                            <?= number_format($current_stock['available_units'], 2).' '.$product['unit_description']. ' ('. number_format($current_stock['available_pieces'], 2).' pieces)'?>
                        </span>
                    </th>
                </tr>
                <tr class="active">
                    <th rowspan="2">DATE &amp; TIME</th>
                    <th rowspan="2">ACTION</th>
                    <th rowspan="2">ACQUISITION<br>COST</th>
                    <th colspan="2">IN</th>
                    <th colspan="2">OUT</th>
                    <th colspan="2">REMAINING</th>
                </tr>
                <tr class="active">
                    <th><?= strtoupper($product['unit_description'])?></th>
                    <th>PIECES</th>
                    <th><?= strtoupper($product['unit_description'])?></th>
                    <th>PIECES</th>
                    <th><?= strtoupper($product['unit_description'])?></th>
                    <th>PIECES</th>
                </tr>
            </thead>
            <tbody>

                <?php $temp_units = $current_stock['available_units'];?>
                <?php $temp_pieces = $current_stock['available_pieces'];?>

                <?php foreach($data AS $key => $row):?>
                    <tr>
                        <td><?= date_create($row['date'])->format('d-M-Y h:i A')?></td>
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
                            <?php elseif($rr_id1 = $row['yieldt_no'] || $row['yieldf_no']):?>
                                <a href="<?= base_url("production/yielding/redirect?id={$row['yieldt_no']}{$row['yieldf_no']}")?>"target="_blank">
                                    <?= "Process # {$row['yieldt_no']}{$row['yieldf_no']}" ?>
                                </a>
                            <?php endif;?>
                        </td>
                        <td class="text-right">
                            <?php if($row['rr_no'] || ($row['sa_no'] && $row['in'] > 0)):?>
                                <?= $row['unit_price'] ? number_format($row['unit_price'], 2): ''?>
                            <?php endif;?>
                        </td>
                        <!-- IN:START -->
                        <td class="text-right">
                            <?php if($row['in']):?>
                                <span class="text-info">
                                    <?= number_format($row['in'], 2)?>
                                </span>
                            <?php endif;?>
                        </td>
                        <td class="text-right">
                            <?php if($row['in'] && $row['pieces']):?>
                                <span class="text-info">
                                    <?= number_format($row['pieces'], 2)?>
                                </span>
                            <?php endif;?>
                        </td>
                        <!-- IN:END -->
                        <!-- OUT:START -->
                        <td class="text-right">
                            <?php if($row['out']):?>
                                <span class="text-danger">
                                    <?= number_format($row['out'], 2)?>
                                </span>
                            <?php endif;?>
                        </td>
                        <td class="text-right">
                            <?php if($row['out'] && $row['out']):?>
                                <span class="text-danger">
                                    <?= number_format($row['pieces'], 2)?>
                                </span>
                            <?php endif;?>
                        </td>
                        <!-- OUT:END -->
                        <td class="text-right">
                            <?php if($key === 0):?>
                                <?= number_format($temp_units, 2)?>
                            <?php else:?>
                                <?php $temp_units += $data[$key-1]['out']?>
                                <?php $temp_units -= $data[$key-1]['in']?>
                                <?= number_format($temp_units, 2)?>
                            <?php endif;?>
                        </td>
                        <td class="text-right">
                            <?php if($key === 0):?>
                                <?= number_format($temp_pieces, 2)?>
                            <?php else:?>
                                <?php $temp_pieces += ($data[$key-1]['out'] ? $data[$key-1]['pieces'] : 0);?>
                                <?php $temp_pieces -= ($data[$key-1]['in'] ? $data[$key-1]['pieces'] : 0);?>
                                <?= number_format($temp_pieces, 2)?>
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