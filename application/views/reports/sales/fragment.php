<?php if (!empty($data)): ?>
<?php $pl_link = base_url('sales/deliveries/update') ?>
<?php foreach ($data as $row): ?>
    <tr><td class="text-center"><a href="<?= "{$pl_link}/{$row['pl']}" ?>" target="_blank"><?= $row['pl'] ?></a></td><td class="text-center"><?= $row['id'] ?></td><td class="text-center"><?= $row['po_number'] ?></td><td class="text-center"><?= $row['company_name'] ?></td><td class="text-center"><?= $row['date'] ?></td><td class="text-center"><?= $row['product'] ?></td><td class="text-center"><?= $row['product_quantity'] ?></td><td class="text-right"><?= $row['unit_price'] ?></td><td class="text-right"><?= $row['discount'] ?></td><td class="text-right"><?= $row['amount'] ?></td></tr>
<?php endforeach; ?>

<?php if (count($data) == 100): ?>
    <tr><td colspan="10" class="text-center"><button class="btn btn-success btn-xs btn-more">Load more results</button></td></tr>
<?php else: ?>
    <tr><td colspan="10" class="text-center">End of list. No more data to show.</td></tr>
<?php endif; ?>

<?php if (isset($total_units) && isset($total_amount)): ?>
    <?php $total_kilos = 0;?>
    <tr><td colspan="5" class="active">Report summary:</td></tr>
    <?php foreach($total_units AS $unit):?>
        <?php $total_kilos += $unit['q']; ?>
        <tr>
            <td class="text-bold text-right" colspan="3"><?= $unit['quantity']?></td>
            <td class="text-left" colspan="2"><?= $unit['unit_description']?></td>
        </tr>
    <?php endforeach;?>
    <tr><td class="text-bold text-right" colspan="3"><?= number_format($total_kilos / 50, 2)?></td><td class="text-left" colspan="2">Total bags (50 kgs)</td></tr>
    <tr><td colspan="3" class="text-bold text-right"><?= $total_amount['total_amount'] ?></td><td colspan="2">Total sales (Php)</td></tr>
    
<?php endif; ?>

<?php else: ?>
    <tr><td colspan="10" class="text-center">No results found.</td></tr>                    
<?php endif; ?>