<?php $pl_link = base_url('sales/deliveries/update');?>
<?php if (!empty($data)): ?>
    <?php $total_balance = 0; ?>
    <?php foreach ($data as $row): ?>
        <?php $payment_count = 1; ?>
        <?php $has_payment = FALSE; ?>
        <?php if (!empty($row['payments'])): ?>
            <?php $payment_count = count($row['payments']); ?>
            <?php $has_payment = TRUE; ?>
        <?php endif; ?>
        <tr>
            <td class="text-center" colspan="2" rowspan="<?= $payment_count ?>"><?= $row['date'] ?></td>
            <td class="text-center" rowspan="<?= $payment_count ?>"><a target="_blank" href="<?="{$pl_link}/{$row['fk_sales_delivery_id']}"?>"><?= $row['fk_sales_delivery_id'] ?></a></td>
            <td class="text-center" rowspan="<?= $payment_count ?>"><?=$row['invoice_number'] ?></td>
            <td class="text-right" colspan="2"><?= number_format($row['total_amount'], 2) ?></td>
            <?php if ($has_payment): ?>
                <?php foreach ($row['payments'] as $index => $payment): ?>
                    <?php if ($index != 0): ?>
                    <tr><td colspan="2">&nbsp;</td>
                    <?php endif; ?>
                    <?php $td_class=''?>
                    <?php if(strtotime($payment['deposit_date']) > strtotime(date('Y-m-d'))):?>
                        <?php $td_class=' warning'?>
                    <?php endif; ?>
                    <td class="text-center<?=$td_class?>"><?= $payment['date'] ?></td>
                    <td class="text-center<?=$td_class?>">
                        <?php if(isset($payment['id'])):?>
                            <a target="_blank" href="<?=base_url("sales/receipts/update/{$payment['id']}")?>"><?= "{$payment['tracking_type']} {$payment['tracking_number']}"?></a>
                        <?php else:?>
                           <?= "{$payment['tracking_type']} {$payment['tracking_number']}"?>
                        <?php endif;?>
                    </td>
                    
                    <td class="text-center<?=$td_class?>"><?= $payment['check_number'] ?></td>
                    <td class="text-center<?=$td_class?>"><?= $payment['check_date'] ?></td>
                    <td class="text-center<?=$td_class?>"><?= $payment['deposit_date'] ?></td>
                    <td class="text-right<?=$td_class?>">
                        <?php if((double) $payment['check_payment'] > 0):?>
                            <?= number_format($payment['check_payment'], 2)?>
                        <?php elseif((double) $payment['cash_payment'] > 0):?>
                            <?= number_format($payment['cash_payment'], 2)?>
                        <?php endif;?>
                    </td>
                    <td class="text-right">
                        <?= isset($payment['whtax_amount']) ? number_format($payment['whtax_amount'], 2) : ''?>
                    </td>
                    <?php if ($index == $payment_count - 1): ?>
                        <td class="text-right">
                            <?= number_format($row['balance'], 2) ?>
                        </td>
                        <?php if(isset($row['month_total']) ):?>
                             <td><?= date('M Y', strtotime($row['date'])).': <span class="pull-right">'.number_format($row['month_total'], 2).'</span>'?></td>
                        <?php endif;?>
                    <?php else: ?>
                        <td>&nbsp;</td>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <td class="text-center"></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td class="text-right"></td>
                <td></td>
                <td class="text-right">
                    <?= number_format($row['balance'], 2) ?>
                </td>
                <?php if(isset($row['month_total']) ):?>
                    <td><?= date('M Y', strtotime($row['date'])).': <span class="pull-right">'.number_format($row['month_total'], 2).'</span>'?></td>
                <?php endif;?>
            <?php endif; ?>
            <?php $total_balance += $row['balance']; ?>
        </tr>
    <?php endforeach; ?>
    <tr><td colspan="12" class="no-border"></td><td class="text-center text-bold">TOTAL</td><td colspan="3" class="text-right text-bold"><?= number_format($total_balance, 2) ?></td></tr>
<?php else: ?>
    <tr><td colspan="15" class="text-center">Selected customer has no outstanding balance</td></tr>
<?php endif; ?>
<tr id="remove-me"><td colspan="15" class="no-border">Time elapsed: <?= $this->benchmark->elapsed_time();?>s</tr>
