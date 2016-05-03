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
                margin-top:-20px;
            }
            .content{
                width:45%;
            }
           


        </style>
    </head>
    <body class="content">
        <style type="text/css">
         .customer-info{
                padding: 0 0 0 10px;
            }
            .products table thead tr td{
                border:0!important;
                padding-top:7px;
                padding-bottom: 6px;
            }
            .products table td:nth-child(6){
                text-align: right;
            }
             .products table td:nth-child(4){
                text-align: right;
            }
            .products table td{
                border:0;
                padding: 2px auto;
            }
            #footer {
                bottom:0px;
                left: 0px;
                display: block;
            }
            .bot-10{
                margin-bottom: 10px;
            }
            table td{
                font-weight: bold;
                padding:0 5px;
            }
            #row-end,.labels{
                font-weight:normal!important;
            }</style>
        <?php $maxDetailsCount = 14; ?>
        <?php $detailsCursor = 0; ?>
        <?php $detailsCount = count($details['details']); ?>
        <?php $tempDetailsCount = $detailsCount; ?>
        <?php $pageTotal = 0; ?>

        <?php
            $pageTotal = (int)ceil($detailsCount / 14);
        ?>

        <?php $pageCounter = 1; ?>
        <?php $margin = '' ?>
        <?php $total = 0;?>
        <?php while ($pageCounter <= $pageTotal): ?>
            <?php if ($pageCounter > 1): ?>
                <?php $margin = "style='margin-top:20px;'" ?>
            <?php endif; ?>
            <div style="height:5in;">
                <div class="row header" <?= $margin ?>> 
                    <div class="col-xs-12 text-center">
                        <strong>ARDITEZZA POULTRY INTEGRATION CORPORATION</strong><br>
                        Ultima Residences Tower 3, Unit 1018, Osmena Blvrd., Cebu City<br>
                        Tel/Fax Nos.: (032) 253-4570 to 71 / 414-3312 / 512-3067<br>
                        <strong>COUNTER RECEIPT # <?= $details['id']?></strong><br>
                        <small>Page <?= "{$pageCounter}/{$pageTotal}" ?></small>
                    </div>
                </div> 
                <div class="row customer" style='border:1px solid #000;border-radius: 5px;margin-top:5px'>
                    <div class="col-xs-12">
                        <table style="width:100%">
                            <tbody>
                                <tr>
                                    <td class="labels">CUSTOMER</td>
                                    <td class='customer-info'>  
                                        <strong><?= $details['customer']; ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="labels">ADDRESS</td>
                                    <td class='customer-info' colspan="3">
                                        <strong><?= $details['customer_address'] ?></strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class='row products'>
                    <div class='col-xs-12'>
                        <table style="width:100%" >
                            <thead>
                                <tr>
                                    <td>DATE</td><td>DUE DATE</td><td>SI#</td><td>PRICE</td><td>KG/S</td><td>AMOUNT</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($innerCursor = 0; $innerCursor < $maxDetailsCount; $innerCursor++): ?>
                                    <?php if (isset($details['details'][$detailsCursor])): ?>
                                        <tr>
                                            <?php $pl_date =  date_create($details['details'][$detailsCursor]['date']); ?>
                                            <td><?= $pl_date->format('m/d/Y') ?></td>
                                            <td><?= $pl_date->modify("+ {$details['credit_term']} day")->format('m/d/Y') ?></td>
                                            <td><?= $details['details'][$detailsCursor]['invoice_number'] ?></td>
                                            <td><?= number_format($details['details'][$detailsCursor]['unit_price'], 2) ?></td>
                                            <td><?= number_format($details['details'][$detailsCursor]['quantity'], 2) ?></td>
                                            <?php $amount = $details['details'][$detailsCursor]['quantity'] * $details['details'][$detailsCursor]['unit_price']?>
                                            <td><?= number_format($amount, 2) ?></td>
                                            <?php $total += $amount;?>
                                        </tr>
                                        <?php if ($detailsCursor+1 == $detailsCount): ?>
                                            <tr><td colspan="6" class="text-center"><small>***NOTHING FOLLOWS***</small></td></tr>
                                            <tr ><td colspan="4"></td><td style="border:1px solid black;border-right: 0;">TOTAL</td><td class="text-right" style="border:1px solid black;border-left: 0"><?= number_format($total, 2)?></td></tr>
                                        <?php endif; ?>
                                        <?php $detailsCursor++; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4">&nbsp;</td></tr>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if ($pageCounter == $pageTotal): ?>
                    <div class='row'> 
                        <div class='col-xs-12'>
                            <table style="width:100%">
                                <tbody >
                                    <tr>
                                        <td >PREPARED BY:</td>
                                        <td><strong ><?= $this->session->userdata('name'); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top:15px">RECEIVED BY:</td>
                                        <td style='padding-top:15px;'>_______________________</strong></td>
                                    </tr>
                                     <tr>
                                        <td colspan="2" class="text-center" style="font-weight: normal;text-align: center;padding-top:10px;">Received the following original invoices/DR's/PO's for checking and/or verification</td>
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
