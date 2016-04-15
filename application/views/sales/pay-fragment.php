<table class='table table-condensed table-bordered' style="border:0">
    <thead class=''><tr class="active"><th style="width:15%">Date</th><th style="width:10%">Tracking # Type</th><th style="width:15%">Tracking #</th><th style="width:15%">Payment Method</th><th style="width:15%">This Payment</th><th style='width:5%'></th></tr></thead>
    <tfoot class=''><tr><td class="no-border" colspan="5"></td><td class='text-center '><a class='btn btn-info btn-flat payment add-line' role='button'><i class='fa fa-plus'></i></a></td></tr></tfoot>
    <tbody id='pl-transactions' >
        <?php
        if (!empty($payment)):
            $entryCtr = 0;
            ?>
            <?php foreach ($payment as $transaction): ?>
                <tr class="paymentDetailEntry" paymentid="<?= $entryCtr ?>" >
                    <td>
                        <input type='text' name='transaction[date][]' class='form-control payment-datepicker' value='<?= $transaction['date'] ?>'/>
                        <input type='hidden' name='transaction[entry_indicator][]' value='<?= $entryCtr ?>' >
                    </td>
                    <td><?= form_dropdown('transaction[tracking_number_type][]', array('CR' => 'C.R.', 'PTN' => 'P.T.N'), $transaction['tracking_number_type'], "class='form-control'") ?></td>
                    <td><input name='transaction[tracking_number][]' type='text' class='form-control' value='<?= $transaction['tracking_number'] ?>'/></td>
                    <td><?= form_dropdown('transaction[payment_type][]', array('Cash' => 'Cash', 'Check' => 'Check'), $transaction['payment_method'], "class='form-control payment-types'") ?></td>
                    <td><input name='transaction[amount][]' type='text' <?= ($transaction['payment_method'] == "Cash") ? false : "readonly" ?> class='form-control has-amount' value='<?= number_format($transaction['amount'], 2) ?>'/></td>
                    <td class='text-center'><a class='btn btn-danger btn-flat payment remove-line' role='button'><i class='fa fa-times'></i></a></td>
                </tr>
                <?php if (count($transaction['check_list']) > 0): ?>

                    <tr class="_c">
                        <td style='border:none'></td>
                        <td colspan="5">
                            <table paymentid="<?= $entryCtr ?>" class="table no-border">
                                <thead>
                                    <tr class="active">
                                        <th style="width:20%">Bank Account</th>
                                        <th style="width:10%">Check Number</th>
                                        <th style="width:15%">Check Date</th>
                                        <th style="width:15%">Deposit Date</th>
                                        <th style="width:14%">Amount</th>
                                        <th style="width:5%">
                                            <a class="btn btn-info btn-flat payment add-check-line" role="button"><i class="fa fa-plus"></i></a>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody id="check-details">
                                    <?php foreach ($transaction['check_list'] as $checkKey => $check): ?>
                                        <tr>
                                            <td><input name="check[<?= $entryCtr ?>][bank_account][]" type="text" value='<?= $check['bank_account'] ?>' class="form-control"></td>
                                            <td><input name="check[<?= $entryCtr ?>][check_number][]" type="text" value='<?= $check['check_number'] ?>' class="form-control"></td>
                                            <td><input name="check[<?= $entryCtr ?>][check_date][]" type="text" value='<?= $check['check_date'] ?>' class="form-control check-datepicker"></td>
                                            <td><input name="check[<?= $entryCtr ?>][deposit_date][]" type="text" value='<?= $check['deposit_date'] ?>' class="form-control check-datepicker"></td>
                                            <td><input name="check[<?= $entryCtr ?>][amount][]" type="text" value='<?= number_format($check['amount'], 2) ?>' class="form-control has-amount checkAmount"></td>
                                            <td>
                                                <?php
                                                if ($checkKey != 0)
                                                {
                                                    ?>
                                                    <a class="btn btn-danger btn-flat payment remove-line" role="button"><i class="fa fa-times"></i></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>

                <?php endif; ?>
                <?php
                $entryCtr++;
            endforeach;
            ?>
        <?php else: ?>
            <tr class='empty-notif'><td colspan='6' class='text-center'>No on-delivery transactions made.</td></tr>
        <?php endif; ?>
    </tbody>
</table>