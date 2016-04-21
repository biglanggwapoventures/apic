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
        <div class="container-fluid content" style="height:5in;">
            <div class="row header"> 
                <div class="col-xs-8 col-xs-offset-2 text-center">
                    <strong>ARDITEZZA POULTRY INTEGRATION CORPORATION</strong><br>
                    Ultima Residences Tower 3, Unit 1018, Osmena Blvrd., Cebu City<br>
                        Tel/Fax Nos.: (032) 253-4570 to 71 / 414-3312 / 512-3067<br>
                    <h4>C H E C K&nbsp;&nbsp;V O U C H E R</h4>
                </div>
            </div> <!-- end .row header -->
            <div class="row customer" style='border:1px solid #000;border-radius: 5px'>
                <div class="col-xs-8">
                    <table>
                        <tbody>
                            <tr><td class="labels">Pay to:</td><td class='customer-info'><strong><?= $payee ?></strong></td></tr>
                            <tr><td class="labels">Check No.:</td><td class='customer-info'><strong><?= "{$bank}#{$check_number}" ?></strong></td></tr>
                            <tr><td class="labels">Check Date:</td><td class='customer-info'><strong><?= $check_date ?></strong></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-xs-4">
                    <table>
                        <tbody>
                            <tr><td class="labels">Voucher No.</td><td class='text-center customer-info'><strong ><?= str_pad($id, 4, 0, STR_PAD_LEFT) ?></strong></td></tr>
                            <tr><td class="labels">Date:</td><td class='text-center customer-info'><strong><?= $date ?></strong></td></tr>
                        </tbody>
                    </table>
                </div>
            </div> <!-- end .row customer -->
            <div class='row products'>
                <div class='col-xs-12'>
                    <table style="width:100%">
                        <thead>
                            <tr>
                                <td>Date</td><td>Account</td><td>Description</td><td>Amount</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                for($x=0; $x<count($liquidation); $x++){
                                    echo "<tr>";
                                        echo "<td>".$liquidation[$x]['date']."</td>";
                                        echo "<td>".$liquidation[$x]['account']."</td>";
                                        echo "<td>".$liquidation[$x]['description']."</td>";
                                        echo "<td>".number_format($liquidation[$x]['amount'], 2)."</td>";
                                    echo "</tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div> <!-- end .row products -->
            <div id='footer' style="margin-top:20px">
                <?php if (!empty($liquidation)): ?>
                    <div class='row'> 
                        <div class='col-xs-9'>
                            <p>AMOUNT: <strong><?= convertCurrencyToWords(number_format($amount, 2, '.', '')); ?></strong></p>
                        </div>
                        <div class='col-xs-3'>
                            <p class='text-right'>TOTAL(Php): <strong ><?= number_format($amount, 2) ?></strong></p>
                        </div>
                    </div>
                    <div class='row'> 
                        <div class='col-xs-2'>
                            <div class="form-group">
                                <label>Prepared by:</label>
                                <p class="form-control-static"><?= $this->session->userdata('name') ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div> <!-- end #footer -->
        </div> <!-- end .container-fluid content -->
    </body>
</html>
