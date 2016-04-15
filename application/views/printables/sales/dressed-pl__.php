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
            table:not(.normal) tbody td{
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
                    <div class="col-xs-12 text-center clearfix">
                         <strong>ARDITEZZA POULTRY INTEGRATION CORPORATION</strong><br>
                        Ultima Residences Tower 3, Unit 1018, Osmena Blvrd., Cebu City<br>
                        Tel/Fax Nos.: (032) 253-4570 to 71 / 414-3312 / 512-3067<br>
                        <h4>P A C K I N G&nbsp;&nbsp;L I S T<small class="pull-right">P.L. #<?= $details['id']?></small></h4>
                    </div>
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
                                <?php for ($innerCursor = 0; $innerCursor < $detailsToDisplay; $innerCursor++): ?>
                                    <tr>
                                        <td><?= $detailsCursor+1 ?></td>
                                        <td>
                                            <?php $pieces = $details['details'][$detailsCursor]['delivered_units']?>
                                            <?= number_format($pieces,2) ?>
                                        </td>
                                        <td>
                                            <?php $kgs = $details['details'][$detailsCursor]['this_delivery']?>
                                            <?= number_format($kgs,2) ?>
                                        </td>
                                        <td><?= $pieces ? number_format($pieces / $kgs, 2) : '' ?></td>
                                        <?php $detailsCursor++; ?>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if ($pageCounter === $pageTotal): ?>
                    <div id='footer'>
                        <p class="text-center">*************NOTHING FOLLOWS*************<p>
                        <table style="width:100%;margin-top:5px" class="normal">
                            <tbody>
                                <tr>
                                    <td>TOTAL NO. OF PIECES: <strong><?= number_format(array_sum(array_column($details['details'], 'delivered_units')), 2)?></strong></td>
                                    <td>TOTAL NO. OF KILOGRAMS: <strong><?= number_format($total_kgs = array_sum(array_column($details['details'], 'this_delivery')), 2)?></strong></td>
                                    <td>PRICE PER KILOGRAM: <strong><?= number_format($unit_price, 2)?></strong></td>
                                    <td>TOTAL AMOUNT PAYABLE: <strong style="font-size:130%;border:1px solid black;padding:2px;"><?= number_format($unit_price * $total_kgs, 2)?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class='row' style="margin-top:10px;"> 
                            <div class='col-xs-8'>
                                <div class="form-group">
                                    <label>PREPARED BY:</label>
                                    <p class="form-control-static"><?=$this->session->userdata('name')?></p>
                                </div>
                            </div>
                            <div class='col-xs-4'>
                                <div class="form-group">
                                    <label>CHECKED AND RECEIVED BY:</label>
                                    <p class="form-control-static  text-right" style="border-bottom:1px solid black;"></p>
                                    <span class="help-block text-center">Print name and signature</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-center"><small><em>Note: Sales Invoice will be issued once this delivery is completed</em></small></p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="page-break"></div>
            <?php $pageCounter++; ?>
        <?php endwhile; ?>
    </body>
</html>