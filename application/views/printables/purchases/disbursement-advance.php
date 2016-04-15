<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <?php include_css('bootstrap.min.css') ?>
        <style type="text/css">
            html, body{
                font-size: 10px!important;
                font-family: 'Tahoma';
                height: 100%!important; 
            }
            .content{
                height: auto;
                min-height: 100%;
                position: relative;
                display: block;
            }
            .customer-info{
                padding: 0 0 0 10px;
            }
            .products table thead tr td{
                text-decoration: underline;
                border:0!important;
                padding-top:7px;
                padding-bottom: 6px;
            }
            .products table td:nth-child(4),td:nth-child(5){
                text-align: right;
            }
            .products table td{
                border:none!important;
                padding: 5px auto;
            }


            #footer {
                bottom:0px;
                left: 0px;
                display: block;
            }
            .bot-10{
                margin-bottom: 10px;
            }
            table tbody td{
                font-weight: bold;
            }
            #row-end,.labels{
                font-weight:normal!important;
            }

            @media all {
                .page-break	{ display: none; }
            }

            @media print {
                .page-break	{ display: block; page-break-before: always; }
            }
        </style>
    </head>
    <body>
        <?php $maxDetailsCount = 10; ?>
        <?php $detailsCursor = 0; ?>
        <?php $detailsCount = count($details['po_line']['details']); ?>
        <?php $tempDetailsCount = $detailsCount; ?>
        <?php $pageTotal = 0; ?>
        <?php if ($detailsCount <= $maxDetailsCount): ?>
            <?php $pageTotal = 1; ?>
        <?php elseif (($detailsCount > $maxDetailsCount) && ($detailsCount % $maxDetailsCount !== 0)): ?>
            <?php $pageTotal = (int) ($detailsCount / $maxDetailsCount + 1); ?>
        <?php elseif (($detailsCount > $maxDetailsCount) && ($detailsCount % $maxDetailsCount === 0)): ?>
            <?php $pageTotal = (int) ($detailsCount / $maxDetailsCount); ?>
        <?php endif; ?>

        <?php $pageCounter = 1; ?>
        <?php while ($pageCounter <= $pageTotal): ?>
            <div class="container-fluid content">
                <div class="row header">
                    <div class="col-xs-8 col-xs-offset-2 text-center">
                        
                        <strong>PROVERA NUTRITIONAL SOLUTIONS CORPORATION</strong><br>
                        GY Warehouse Complex, Unit L3, A. Bacaltos Sr. St., Lawaan 1, Talisay City, Cebu<br>
                        Tel No.: 514-8890 | Fax No.: 491-3485 | Email: provera.feedmill@gmail.com<br>
                        <h4>C H E C K&nbsp;&nbsp;V O U C H E R</h4>
                    </div>
                    <div class="col-xs-2"><p class="text-right"> Page <?= "{$pageCounter} of {$pageTotal}" ?></p></div>
                </div> 
                <div class="row customer" style='border:1px solid #000;border-radius: 5px'>
                    <div class="col-xs-8">
                        <table>
                            <tbody>
                                <tr><td class="labels">Pay to:</td><td class='customer-info'><strong><?= $details['supplier'] ?></strong></td></tr>
                                <tr><td class="labels">Check No.:</td><td class='customer-info'><strong><?= "{$details['bank']}#{$details['check_number']}" ?></strong></td></tr>
                                <tr><td class="labels">Check Date:</td><td class='customer-info'><strong><?= $details['check_date'] ?></strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xs-4">
                        <table>
                            <tbody>
                                <tr><td class="labels">Voucher No.</td><td class='text-center customer-info'><strong ><?= str_pad($details['id'], 4, 0, STR_PAD_LEFT) ?></strong></td></tr>
                                <tr><td class="labels">Date:</td><td class='text-center customer-info'><strong><?= $details['date'] ?></strong></td></tr>
                                <tr><td class="labels">PO No.:</td><td class='text-center customer-info'><strong><?= $details['po_line']['id'] ?></strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class='row products'>
                    <div class='col-xs-12'>
                        <table style="width:100%">
                            <thead>
                                <tr>
                                    <td style="width:15%">QTY</td> <td style="width:25%">UNITS</td><td style="width:30%">PRODUCT DESCRIPTION(CODE)</td><td style="width:15%">UNIT PRICE</td><td style="width:20%">AMOUNT</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $pageTotalPrice = 0; ?>
                                <?php $detailsToDisplay = 0; ?>
                                <?php if ($tempDetailsCount > $maxDetailsCount): ?>
                                    <?php $detailsToDisplay = $maxDetailsCount; ?>
                                    <?php $tempDetailsCount = $tempDetailsCount - $maxDetailsCount; ?>
                                <?php else: ?>
                                    <?php $detailsToDisplay = $tempDetailsCount; ?>
                                <?php endif; ?>
                                <?php for ($innerCursor = 0; $innerCursor < $maxDetailsCount; $innerCursor++): ?>
                                    <?php if (array_key_exists($detailsCursor, $details['po_line']['details'])): ?>
                                        <tr>
                                            <?php $line = $details['po_line']['details'][$detailsCursor]; ?>
                                            <td><?= number_format($line['quantity'], 2) ?></td>
                                            <td><?= $line['unit_description'] ?></td>
                                            <td><?= $line['description'] . ($line['code'] ? ' (' . $line['code'] . ')' : '') ?></td>
                                            <td><?= number_format($line['unit_price_unformatted'], 2) ?></td>
                                            <?php $netAmount = $line['unit_price_unformatted'] * $line['quantity']; ?>
                                            <td><?= number_format($netAmount, 2) ?></td>
                                            <?php $pageTotalPrice+=($netAmount); ?>
                                            <?php $detailsCursor++; ?>
                                        </tr>
                                        <?php if ($detailsCursor === $detailsCount): ?>
                                            <tr><td colspan="5" class="text-center">*************NOTHING FOLLOWS*************</td></tr>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4">&nbsp;</td></tr>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id='footer' style="margin-top:20px">
                    <div class='row'> 
                        <div class='col-xs-8'>
                            <p>AMOUNT: <strong><?= convertCurrencyToWords($details['amount']); ?></strong></p>
                        </div>
                        <div class='col-xs-4'>
                            <p class='text-right'>ADVANCE PAYMENT(Php): <strong ><?= number_format($details['amount'], 2) ?></strong></p>
                        </div>
                    </div>

                    <div class='row'> 
                        <div class='col-xs-2'>
                            <div class="form-group">
                                <label>Prepared by:</label>
                                <p class="form-control-static"><?= $this->session->userdata('name') ?></p>
                            </div>
                        </div>
                        <div class='col-xs-2'>
                            <div class="form-group">
                                <label>Checked by:</label>
                                <p class="form-control-static  text-right" style="border-bottom:1px solid black;"></p>
                                <span class="help-block text-center">Mr Gerald Campos</span>
                            </div>
                        </div>
                        <div class='col-xs-2'>
                            <div class="form-group">
                                <label>Approved by:</label>
                                <p class="form-control-static  text-right" style="border-bottom:1px solid black;"></p>
                                <span class="help-block text-center">Mr Gilbert Yap</span>
                            </div>
                        </div>
                        <div class='col-xs-6'>
                            <div class="form-group">
                                <label>Received the full payment amount described above by</label>
                                <p class="form-control-static  text-right" style="border-bottom:1px solid black;"></p>
                                <span class="help-block text-center">Name & signature / date</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-break"></div>
            <?php $pageCounter++; ?>
        <?php endwhile; ?>
    </body>
</html>