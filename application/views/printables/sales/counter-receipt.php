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
                width:3.6in;
                overflow;hidden;
            }
           


        </style>
    </head>
    <body class="content">
        <style type="text/css">
         
            .products table thead tr td{
                border:0!important;
                padding-top:7px;
                padding-bottom: 6px;
            }
            .products table td:nth-child(3){
                text-align: right;
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
            }
            .products table td{
                font-size:12px;
            }
            #row-end,.labels{
                font-weight:normal!important;
            }</style>
        <?php $maxDetailsCount = 10; ?>
        <?php $detailsCursor = 0; ?>
        <?php $detailsCount = count($details['details']); ?>
        <?php $tempDetailsCount = $detailsCount; ?>
        <?php $pageTotal = 0; ?>

        <?php
            $pageTotal = (int)ceil($detailsCount / $maxDetailsCount);
        ?>

        <?php $pageCounter = 1; ?>
        <?php $margin = '' ?>
        <?php $total = 0;?>
        <?php while ($pageCounter <= $pageTotal): ?>
            <?php if ($pageCounter > 1): ?>
                <?php $margin = "style='margin-top:35px;'" ?>
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
                <div class="row customer" style='border-top:1px solid #000;border-bottom:1px solid #000;margin-top:5px'>
                    <div class="col-xs-12">
                        <table style="width:100%;">
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
                                 <tr>
                                    <td class="labels">CREDIT TERMS</td>
                                    <td class='customer-info' colspan="3">
                                        <strong><?= $details['credit_term'] ?> day/s</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class='row products'>
                    <div class='col-xs-12'>
                        <table style="width:100%;" >
                            <thead>
                                <tr>
                                    <td class="text-center" style="width:30%">DATE</td>
                                    <td class="text-center" style="width:10%">SI#</td>
                                    <td class="text-right"  style="width:25%">AMOUNT</td>
                                    <td class="text-center" style="width:35%">DUE DATE</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($innerCursor = 0; $innerCursor < $maxDetailsCount; $innerCursor++): ?>
                                    <?php if (isset($details['details'][$detailsCursor])): ?>
                                        <tr>
                                            <?php $pl_date =  date_create($details['details'][$detailsCursor]['date']); ?>
                                            <td class="text-center"><?= $pl_date->format('m/d/Y') ?></td>
                                            <td class="text-center"><?= $details['details'][$detailsCursor]['invoice_number'] ?></td>
                                            <?php $amount = $details['details'][$detailsCursor]['quantity'] * $details['details'][$detailsCursor]['unit_price']?>
                                            <td class="text-right"><?= number_format($amount, 2) ?></td>
                                            <td class="text-center"><?= $pl_date->modify("+ {$details['credit_term']} day")->format('m/d/Y') ?></td>
                                            <?php $total += $amount;?>
                                        </tr>
                                        <?php if ($detailsCursor+1 == $detailsCount): ?>
                                            <tr><td colspan="6" class="text-center"><small>***NOTHING FOLLOWS***</small></td></tr>
                                            <tr >
                                                <td colspan="2"></td>
                                                <td style="border:1px solid black;border-right: 0;padding:0">TOTAL</td>
                                                <td style="border:1px solid black;border-left: 0;padding:0;text-align:center!important;">
                                                    <?= number_format($total, 2)?>
                                                </td>
                                            </tr>
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
