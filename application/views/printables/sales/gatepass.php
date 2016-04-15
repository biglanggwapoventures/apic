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
                padding: 0 10px;
                height: auto;
                min-height: 100%;
                position: relative;
                display: block;
                width:45%;
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
            .products table td{
                text-align: center;
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
            <div class="content">
                <div class="row header">
                    <div class="col-xs-12 text-center">
                        <b style="font-size:120%">PROVERA NUTRITIONAL SOLUTIONS CORPORATION</b><br>
                        GY Warehouse Complex, Unit L3, A. Bacaltos Sr. St., Lawaan 1, Talisay City, Cebu<br>
                        Tel No.: 514-8890 | Fax No.: 491-3485 | Email: provera.feedmill@gmail.com<br>
                        <h6>G A T E&nbsp;&nbsp;P A S S</h6>
                    </div>
                </div> 
                <div class="row customer" style='border:1px solid #000;border-radius: 5px'>
                    <div class="col-xs-7">
                        <table>
                            <tbody>
                                <tr><td class="labels">NAME:</td><td class='customer-info'><strong><?= $details['company_name'] ?></strong></td></tr>
                                <tr><td class="labels">DATE:</td><td class='customer-info'><strong><?= date('m/d/Y') ?></strong></td></tr>
                                <tr><td class="labels">TIME OUT:</td><td class='customer-info'><strong><?= date('h:i:s A', time()) ?></strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xs-5">
                        <table>
                            <tbody>
                                <tr><td class="labels">DRIVER</td><td class='text-center customer-info'><strong ><?= "{$trucking['driver']} ({$trucking['trucking_name']})"  ?></strong></td></tr>
                                <tr><td class="labels">PLATE NO.:</td><td class='text-center customer-info'><strong><?= $trucking['plate_number'] ?></strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class='row products'>
                    <div class='col-xs-12'>
                        <table style="width:100%;"  >
                            <thead>
                                <tr>
                                    <td style="width:10%">QTY</td > <td style="width:30%">UNITS</td><td style="width:60%">PRODUCT DESCRIPTION(CODE)</td>
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
                                            <td><?= "{$details['details']['prod_descr'][$detailsCursor]} [{$details['details']['prod_code'][$detailsCursor]}]" ?></td>
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
                </div>
            </div>
            <div class="page-break"></div>
            <?php $pageCounter++; ?>
        <?php endwhile; ?>
    </body>
</html>
