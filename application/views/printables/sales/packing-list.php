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
            .products table td:nth-child(4),td:nth-child(5),td:nth-child(6),td:nth-child(7){
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
        <?php $detailsCount = count($details['details']['id']); ?>
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
                        <strong>ARDITEZZA POULTRY INTEGRATION CORPORATION</strong><br>
                        Ultima Residences Tower 3, Unit 1018, Osmena Blvrd., Cebu City<br>
                        Tel/Fax Nos.: (032) 253-4570 to 71 / 414-3312 / 512-3067<br>
                        <h4>P A C K I N G&nbsp;&nbsp;L I S T</h4>
                    </div>
                </div> 
                <div class="row customer" style='border:1px solid #000;border-radius: 5px'>
                    <div class="col-xs-8">
                        <table>
                            <tbody>
                                <tr><td class="labels">NAME:</td><td class='customer-info'><strong><?= $details['company_name'] ?></strong></td></tr>
                                <tr><td class="labels">SHIPPING ADDRESS:</td><td class='customer-info'><strong><?= $details['address'] ?></strong></td></tr>
                                <tr><td class="labels">REMARKS:</td><td class='customer-info'><strong><?= $details['remarks'] ?></strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xs-4">
                        <table>
                            <tbody>
                                <tr><td class="labels">P.L. No.:</td><td class='text-center customer-info'><strong ><?= $details['id'] ?></strong></td></tr>
                                <tr><td class="labels">P.L. Date:</td><td class='text-center customer-info'><strong><?= $details['formatted_date'] ?></strong></td></tr>
                                <tr><td class="labels">P.O. No.:</td><td class='text-center customer-info'><strong><?= $details['po_number'] ?></strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class='row products'>
                    <div class='col-xs-12'>
                        <table style="width:100%">
                            <thead>
                                <tr>
                                    <td>QTY</td> <td>UNITS</td><td>PRODUCT DESCRIPTION(CODE)</td><td>UNIT PRICE</td><td>DISCOUNT</td><td>NET UNIT PRICE</td><td>AMOUNT</td>
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
                                    <?php if ($details['details']['this_delivery'][$detailsCursor] > 0): ?>
                                        <tr>
                                            <td><?= number_format($details['details']['this_delivery'][$detailsCursor], 2) ?></td>
                                            <td><?= $details['details']['unit_description'][$detailsCursor] ?></td>
                                            <td><?= $details['details']['prod_descr'][$detailsCursor] ?></td>
                                            <td><?= number_format($details['details']['unit_price'][$detailsCursor], 2) ?></td>
                                            <?php $grossAmount = $details['details']['unit_price'][$detailsCursor] * $details['details']['this_delivery'][$detailsCursor]; ?>
                                            <?php $totalDiscount = $details['details']['discount'][$detailsCursor] * $details['details']['this_delivery'][$detailsCursor]; ?>
                                            <td><?= number_format($details['details']['discount'][$detailsCursor], 2) ?></td>
                                            <td><?= number_format($details['details']['unit_price'][$detailsCursor] - $details['details']['discount'][$detailsCursor], 2) ?></td>
                                            <td><?= number_format($grossAmount - $totalDiscount, 2) ?></td>
                                            <?php $pageTotalPrice+=($grossAmount - $totalDiscount); ?>
                                        </tr>
                                    <?php endif; ?>
                                    <?php $detailsCursor++; ?>
                                <?php endfor; ?>
                                <tr><td id="row-end" colspan="7" class='text-center'>*************Nothing Follows*************</td></tr>
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
                            <table style='width:100%'>
                                <tbody>
                                    <tr><td style='width:40%'>Delivered by:</td><td>Received the above goods in good order and condition.</td></tr>
                                    <tr><td><strong style='text-decoration: underline'><?= $details['trucking_name'] ?></strong></td>
                                        <td style='border-bottom:1px solid black;padding-top:30px;'></td></tr>
                                    <tr><td></td>
                                        <td style='vertical-align: top;font-weight: normal!important;text-align: center;'>Print name and signature / Date</td></tr>
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
