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
                .page-break { display: none; }
            }

            @media print {
                .page-break { display: block; page-break-before: always; }
            }
        </style>
    </head>
    <body>
        <?php $maxDetailsCount = 20; ?>
        <?php $detailsCursor = 0; ?>
        <?php $detailsCount = count($details['line']); ?>
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
        <?php $margin = '' ?>
        <?php while ($pageCounter <= $pageTotal): ?>
            <?php if ($pageCounter > 1): ?>
                <?php $margin = "style='margin-top:5px;'" ?>
            <?php endif; ?>
            <div class="container-fluid content" style="height:5in;">
                <div class="row header" <?= $margin ?>> 
                    <div class="col-xs-8 col-xs-offset-2 text-center">
                        <strong>PROVERA NUTRITIONAL SOLUTIONS CORPORATION</strong><br>
                        GY Warehouse Complex, Unit L3, A. Bacaltos Sr. St., Lawaan 1, Talisay City, Cebu<br>
                        Tel No.: 514-8890 | Fax No.: 491-3485 | Email: provera.feedmill@gmail.com<br>
                        <h4>C H E C K&nbsp;&nbsp;V O U C H E R</h4>
                    </div>
                    <div class="col-xs-2"><p class="text-right"> Page <?= "{$pageCounter} of {$pageTotal}" ?></p></div>
                </div> 
                <div class="row customer" style='border:1px solid #000;border-radius: 5px'>
                    <div class="col-xs-5">
                        <table>
                            <tbody>
                                <tr><td class="labels">CUSTOMER</td><td> : </td><td class='customer-info'><strong style="font-size:130%"><?= $details['company_name'] ?></strong></td></tr>
                                <tr><td class="labels">ADDRESS</td><td> : </td><td class='customer-info'><strong><?= $details['address'] ?></strong></td></tr>
                                <tr><td class="labels">DEPARTURE TIME</td><td> : </td><td class='customer-info'><strong><?= date_create($details['date'])->format('d-M-Y h:i A') ?></strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xs-3">
                        <table>
                            <tbody>
                                <tr><td class="labels">CUST CODE</td><td> : </td><td class='customer-info'><strong ><?= $details['customer_code'] ?></strong></td></tr>
                                <tr><td class="labels">CUST P.O. NO.</td><td> : </td><td class=' customer-info'><strong><?= $details['po_number'] ?></strong></td></tr>
                                <tr><td class="labels">PLATE NO.</td><td> : </td><td class='customer-info'><strong><?= $details['plate_number'] ?></strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xs-4">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="labels">DRIVER</td><td>:</td>
                                    <td class='customer-info'><strong><?= $details['driver'] ?></strong></td>
                                </tr>
                                <tr>
                                    <td class="labels">ASSISTANT</td><td>:</td>
                                    <td class='customer-info'><strong><?= $details['assistant'] ?></strong></td>
                                </tr>
                                <tr>
                                    <td class="labels">INVOICE NO.</td><td>:</td>
                                    <td class='customer-info'><strong><?= $details['invoice_number'] ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row customer" style="margin-top:5px;">
                    <?php $products = array_column($sales_order['items_ordered'], NULL, 'id')?>
                    <?php $product = explode('] ', $products[$details['details'][0]['fk_sales_order_detail_id']]['product_description'])?>
                    <?php $unit_price = $products[$details['details'][0]['fk_sales_order_detail_id']]['unit_price']?>
                    <div class="col-xs-5">
                        <table>
                            <tbody>
                                <tr><td class="labels">PRODUCT CODE</td><td> : </td><td class='customer-info'><strong></strong><?= str_replace('[', '', $product[0])?></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xs-5">
                        <table>
                            <tbody>
                                <tr><td class="labels">PRODUCT DESCRIPTION</td><td> : </td><td class='text-center customer-info'><strong ><?= $product[1]?></strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class='row products'>
                    <div class='col-xs-12'>
                        <table style="width:100%">
                            <thead>
                                <tr>
                                    <td>WEIGH NO.</td>
                                    <td>NO. OF PIECES</td>
                                    <td>KILOGRAMS</td>
                                    <td>AVERAGE</td>
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
                                <?php if ($pageCounter === $pageTotal && ($detailsCount % 20 <= 14 || $detailsCount % 20 <= 0 || $detailsCount - 20 <= 0)): ?>
                                    <?php $ctr = 14 ?>
                                <?php else: ?>
                                    <?php $ctr = $maxDetailsCount ?>
                                <?php endif; ?>
                                <?php for ($innerCursor = 0; $innerCursor < $ctr; $innerCursor++): ?>
                                    <?php if (array_key_exists($detailsCursor, $details['line'])): ?>
                                        <tr>
                                            <td><?= str_pad($details['line'][$detailsCursor]['fk_purchase_receiving_id'], 4, 0, STR_PAD_LEFT) ?></td>
                                            <td><?= $details['line'][$detailsCursor]['pr_number'] ?></td>
                                            <td><?= $details['line'][$detailsCursor]['receiving_date'] ?></td>
                                            <?php $netAmount = $details['line'][$detailsCursor]['amount']; ?>
                                            <td><?= number_format($netAmount, 2) ?></td>
                                            <?php $pageTotalPrice+=($netAmount); ?>
                                            <?php $detailsCursor++; ?>
                                        </tr>
                                        <?php if ($detailsCursor === $detailsCount): ?>
                                            <tr><td colspan="4" class="text-center">*************NOTHING FOLLOWS*************</td></tr>
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
                    <?php if ($pageCounter === $pageTotal): ?>
                        <div class='row'> 
                            <div class='col-xs-9'>
                                <p>AMOUNT: <strong><?= convertCurrencyToWords(number_format($details['amount'], 2, '.', '')); ?></strong></p>
                            </div>
                            <div class='col-xs-3'>
                                <p class='text-right'>TOTAL(Php): <strong ><?= number_format($details['amount'], 2) ?></strong></p>
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
                    <?php endif; ?>
                </div>
            </div>
            <?php $pageCounter++; ?>
        <?php endwhile; ?>
    </body>
</html>
