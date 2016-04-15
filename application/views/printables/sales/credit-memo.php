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
            .products table td:nth-child(6),td:nth-child(5){
                text-align: right;
            }
            .products table td{
                border:none!important;
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
        <?php $detailsCount = count($details['details']); ?>
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
                    <div class="col-xs-12 text-center">
                        <strong>PROVERA NUTRITIONAL SOLUTIONS CORPORATION</strong><br>
                        GY Warehouse Complex, Unit L3, A. Bacaltos Sr. St., Lawaan 1, Talisay City, Cebu<br>
                        Tel No.: 514-8890 | Fax No.: 491-3485 | Email: provera.feedmill@gmail.com<br>
                        <h4>C R E D I T&nbsp;&nbsp;M E M O</h4>
                    </div>
                </div> 
                <div class="row customer" style='border:1px solid #000;border-radius: 5px'>
                    <div class="col-xs-8">
                        <table>
                            <tbody>
                                <tr><td class="labels">NAME:</td><td class='customer-info'><strong><?= $details['customer']['company_name'] ?></strong></td></tr>
                                <tr><td class="labels">SHIPPING ADDRESS:</td><td class='customer-info'><strong><?= $details['customer']['address'] ?></strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xs-4">
                        <table>
                            <tbody>
                                <tr><td class="labels">P.L. No.:</td><td class='text-center customer-info'><strong ><?= $details['delivery_id'] ?></strong></td></tr>
                                <tr><td class="labels">P.L. Date:</td><td class='text-center customer-info'><strong><?= date('m/d/Y', strtotime($details['date'])) ?></strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class='row products'>
                    <div class='col-xs-12'>
                        <table style="width:100%">
                            <thead>
                                <tr>
                                    <td>QTY</td> <td>UNITS</td><td>ITEM</td><td>REMARKS</td><td>NET UNIT PRICE</td><td>AMOUNT</td>
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
                                <?php for ($innerCursor = 0; $innerCursor < $detailsToDisplay; $innerCursor++): ?>
                                    <tr>
                                        <?php $others = $details['details'][$detailsCursor]['quantity'] === '-';?>   
                                        <td><?= $others ? '-' : $details['details'][$detailsCursor]['quantity']?></td>
                                        <?php $details['details'][$detailsCursor]['quantity'] = $details['details'][$detailsCursor]['quantity'] !== '-' ? $details['details'][$detailsCursor]['quantity']: 1;?>
                                        <td><?= $details['details'][$detailsCursor]['product_unit_description']?></td>
                                        <td>
                                            <?= $details['details'][$detailsCursor]['product']?>
                                        </td>
                                        <td><?= $details['details'][$detailsCursor]['remarks']?></td>
                                        <?php $net_unit_price = $details['details'][$detailsCursor]['product_unit_price'] - $details['details'][$detailsCursor]['product_unit_discount'];?>
                                        <td><?=  $others ? '-' : number_format($net_unit_price, 2)?></td>
                                        <?php $amount = $net_unit_price * $details['details'][$detailsCursor]['quantity'];?>
                                        <td><?= number_format($amount, 2)?></td>
                                        <?php $pageTotalPrice+=$amount;?>
                                    </tr>
                                    <?php $detailsCursor++; ?>
                                <?php endfor; ?>
                                <tr><td id="row-end" colspan="6" class='text-center'>*************Nothing Follows*************</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id='footer'>
                    <div class='row'> 
                        <div class='col-xs-12'>
                            <p class='text-right'>TOTAL: <strong style='padding-left:20px'><?= number_format($pageTotalPrice, 2) ?></strong></p>
                        </div>
                    </div>
                    <div class='row'> 
                        <div class='col-xs-12'>
                            <table style='width:50%'>
                                <tbody>
                                    <tr>
                                        <td class="text-bold">Received by:</td>
                                    </tr>
                                    <tr>
                                        <td class="text-bold" style="padding-top:10px;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style='border-top:1px black solid;vertical-align: top;font-weight: normal!important;text-align: center;'>Print name and signature / Date</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-break"></div>
            <?php $pageCounter++; ?>
        <?php endwhile; ?>
    </body>
</html>
