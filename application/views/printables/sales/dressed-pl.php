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
                border:1px solid black;
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
            #row-end,.labels{
                font-weight:normal!important;
            }

            .summary{
                font-size:130%;
                border:1px solid black;
                padding:1px;margin:2px
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
        <?php $margin = '' ?>
        <?php while ($pageCounter <= $pageTotal): ?>
            <?php if ($pageCounter > 1): ?>
                <?php $margin = "style='margin-top:10px;'" ?>
            <?php endif; ?>
            <div class="container-fluid content" style="height:5in;">
                <div class="row header" <?= $margin ?>> 
                    <div class="col-xs-8 col-xs-offset-2 text-center">
                        <strong>ARDITEZZA POULTRY INTEGRATION CORPORATION</strong><br>
                        Ultima Residences Tower 3, Unit 1018, Osmena Blvrd., Cebu City<br>
                        Tel/Fax Nos.: (032) 253-4570 to 71 / 414-3312 / 512-3067<br>
                        <h4>P A C K I N G&nbsp;&nbsp;L I S T</h4>
                    </div>
                    <div class="col-xs-2">
                        <p class="text-right"> Page <?= "{$pageCounter} of {$pageTotal}" ?></p>
                        <p class="text-right" style="font-size:130%;margin-top:30px"> P.L. # <?= $details['id']?></p>
                    </div>
                </div> 
                <div class="row customer" style='border:1px solid #000;border-radius: 5px'>
                    <div class="col-xs-5">
                        <table>
                            <tbody>
                                <tr><td class="labels">CUSTOMER</td><td> : </td><td class='customer-info' style="font-size:130%"><?= $details['company_name'] ?></td></tr>
                                <tr><td class="labels">ADDRESS</td><td> : </td><td class='customer-info'><?= $details['address'] ?></td></tr>
                                <tr><td class="labels">DEPARTURE TIME</td><td> : </td><td class='customer-info'><?= date_create($details['date'])->format('d-M-Y h:i A') ?></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xs-3">
                        <table>
                            <tbody>
                                <tr><td class="labels">CUST CODE</td><td> :</td><td class='customer-info'><?= $details['customer_code'] ?></td></tr>
                                <tr><td class="labels">CUST P.O. NO.</td><td> : </td><td class=' customer-info'><?= $details['po_number'] ?></td></tr>
                                <tr><td class="labels">PLATE NO.</td><td> : </td><td class='customer-info'><?= $details['plate_number'] ?></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xs-4">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="labels">DRIVER</td><td>:</td>
                                    <td class='customer-info'><?= $details['driver'] ?></td>
                                </tr>
                                <tr>
                                    <td class="labels">ASSISTANT</td><td>:</td>
                                    <td class='customer-info'><?= $details['assistant'] ?></td>
                                </tr>
                                <tr>
                                    <td class="labels">INVOICE NO.</td><td>:</td>
                                    <td class='customer-info'><?= $details['invoice_number'] ?></td>
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
                                <tr><td class="labels">PRODUCT DESCRIPTION</td><td> : </td><td class='text-center customer-info'><?= $product[1]?></td></tr>
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
                                        <td><?= $pieces ? number_format($kgs / $pieces, 2) : '' ?></td>
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
                                    <td>TOTAL NO. OF PIECES: <br>
                                        <span class="summary">
                                            <?= number_format(array_sum(array_column($details['details'], 'delivered_units')), 2)?>
                                        </span>
                                    </td>
                                    <td>TOTAL NO. OF KILOGRAMS: <br>
                                        <span class="summary">
                                            <?= number_format($total_kgs = array_sum(array_column($details['details'], 'this_delivery')), 2)?>
                                        </span> 
                                    </td>
                                    <td>PRICE PER KILOGRAM: <br>
                                        <span class="summary">
                                            <?= number_format($unit_price, 2)?>
                                        </span>
                                    </td>
                                    <td>TOTAL AMOUNT PAYABLE: <br>
                                        <span class="summary">
                                            <?= number_format($unit_price * $total_kgs, 2)?>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class='row' style="margin-top:10px;"> 
                            <div class='col-xs-8'>
                                <div class="form-group">
                                    <label style="font-weight:normal">PREPARED BY:</label>
                                    <p class="form-control-static"><?=$this->session->userdata('name')?></p>
                                    <p style="margin:0;"><small><em>Note: Sales Invoice will be issued once this delivery is completed</em></small></p>
                                </div>
                            </div>
                            <div class='col-xs-4'>
                                <div class="form-group">
                                    <label style="font-weight:normal">CHECKED AND RECEIVED BY:</label>
                                    <p class="form-control-static  text-right" style="border-bottom:1px solid black;"></p>
                                    <span class="help-block text-center">Print name and signature</span>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                <?php endif; ?>
            </div>
            <?php $pageCounter++; ?>
        <?php endwhile; ?>
    </body>
</html>
