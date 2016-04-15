<style type="text/css">
    table thead th:nth-child(3),th:nth-child(4),th:nth-child(5),th:nth-child(6),th:nth-child(7),th:nth-child(8),
    table tbody td:nth-child(3),td:nth-child(4),td:nth-child(5),td:nth-child(6),td:nth-child(7),td:nth-child(8){
        text-align: right;
    }
</style>
<div class="box box-solid">
    <div class="box-body">
        <p class="text-center text-bold"><?= $product['description']?> [<?= $product['code']?>]</p>
        <p class="text-center">Current stock on hand: <?= number_format($current_stock, 2).' '.$product['unit_description']?></p>
        <p class="text-center">Current cost: <?= number_format($current_cost, 2)?></p>
        <table class="table table-condensed table-bordered" id="sr" style="table-layout:fixed">
            <thead>
                <tr class="active">
                    <th style="width: 20%">Date &amp; time</th>
                    <th></th>
                    <th>Purchase price</th>
                    <th>In</th>
                    <th>Out</th>
                    <th>Stock left</th>
                </tr>
            </thead>
            <tbody>
                <?php $temp_stock = $current_stock;?>
                <?php foreach($data AS $key => $row):?>
                    <tr>
                        <td><?= date('d-M-Y h:i:s A', strtotime($row['date']))?></td>
                        <td>
                            
                        </td>
                        <td class="text-right">
                            <?= $row['in'] > 0 && $row['unit_price'] ? number_format($row['unit_price'], 2): ''?>
                        </td>
                        <td class="text-right"><?= $row['in'] > 0 ? '<span class="text-info">'.number_format($row['in'], 2).'</span> ('.number_format($row['remaining'], 2).')': ''?></td>
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