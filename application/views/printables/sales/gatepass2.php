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
                width:46%;
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
            .products table td:nth-child(3){
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


        </style>
    </head>
    <body>
        <?php $maxDetailsCount = 20; ?>
        <?php $detailsCursor = 0; ?>
        <?php $detailsCount = count($details['items']); ?>
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
            <div class="content" style="height:5in;">
                <div class="row header" <?= $margin ?>> 
                    <div class="col-xs-12 text-center">
                        <strong>PROVERA NUTRITIONAL SOLUTIONS CORPORATION</strong><br>
                        GY Warehouse Complex, Unit L3, A. Bacaltos Sr. St., Lawaan 1, Talisay City, Cebu<br>
                        Tel No.: 514-8890 | Fax No.: 491-3485 | Email: provera.feedmill@gmail.com<br>
                        <strong>G A T E P A S S</strong><br>
                        <small>Page <?= "{$pageCounter}/{$pageTotal}" ?></small>
                    </div>
                </div> 
                <div class="row customer" style='border:1px solid #000;border-radius: 5px;margin-top:5px'>
                    <div class="col-xs-8">
                        <table>
                            <tbody>
                                <tr><td class="labels">ISSUED FOR:</td><td class='customer-info'><strong><?= $details['type'] === 'pl' ? $details['customer'] :  $details['issued_to']?></strong></td></tr>
                                <tr><td class="labels">CREATED AT:</td><td class='customer-info'><strong><?= date('m/d/Y h:i:s A', strtotime($details['created_at'])) ?></strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xs-4">
                        
                    </div>
                </div>
                <div class='row products'>
                    <div class='col-xs-12'>
                        <table style="width:100%">
                            <thead>
                                <tr>
                                <?php if($details['type'] === 'pl'):?>
                                    <td>PL #</td><td>PRODUCT</td><td>QTY</td><td>UNITS</td>
                                <?php else:?>
                                    <td>Quantity</td><td>Description</td>
                                <?php endif;?>
                                </tr>
                            </thead>
                            <tbody>
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
                                    <?php if (isset($details['items'][$detailsCursor])): ?>
                                        <tr>
                                        <?php if($details['type'] === 'pl'):?>
                                            <td><?= $details['items'][$detailsCursor]['pl_id'] ?></td>
                                            <td><?= $details['items'][$detailsCursor]['description'] ?></td>
                                            <td><?= $details['items'][$detailsCursor]['this_delivery'] ?></td>
                                            <td><?= $details['items'][$detailsCursor]['unit_description'] ?></td>
                                        <?php else:?>
                                                <td><?= $details['items'][$detailsCursor]['quantity'] ?></td>
                                                <td><?= $details['items'][$detailsCursor]['description'] ?></td>
                                        <?php endif;?>
                                        </tr>
                                        <?php $detailsCursor++; ?>
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
                <?php if($pageCounter === $pageTotal):?>
                    <div class='row'> 
                        <div class='col-xs-12'>
                            <table style='width:100%'>
                                <tbody>
                                    <tr>
                                        <td style='width:40%'>PREPARED BY:</td>
                                    </tr>
                                    <tr>
                                        <td style='text-decoration: underline;padding-top:10px'><strong ><?= $this->session->userdata('name'); ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif;?>
            </div>
            <?php $pageCounter++; ?>
        <?php endwhile; ?>
    </body>
</html>
