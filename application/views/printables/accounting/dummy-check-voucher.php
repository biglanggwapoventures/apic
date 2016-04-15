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
                .page-break	{ display: none; }
            }

            @media print {
                .page-break	{ display: block; page-break-before: always; }
            }
        </style>
    </head>
    <body>
        <div class="container-fluid content" style="height:5in;">
            <div class="row header"> 
                <div class="col-xs-8 col-xs-offset-2 text-center">
                    <strong>PROVERA NUTRITIONAL SOLUTIONS CORPORATION</strong><br>
                    GY Warehouse Complex, Unit L3, A. Bacaltos Sr. St., Lawaan 1, Talisay City, Cebu<br>
                    Tel No.: 514-8890 | Fax No.: 491-3485 | Email: provera.feedmill@gmail.com<br>
                    <h4>C H E C K&nbsp;&nbsp;V O U C H E R</h4>
                </div>
            </div>
            <div class="row customer" style='border:1px solid #000;border-radius: 5px'>
                <div class="col-xs-8">
                    <table>
                        <tbody>
                            <tr><td class="labels">Pay to:</td><td class='customer-info'><strong><?= $data['payee'] ?></strong></td></tr>
                            <tr><td class="labels">Check No.:</td><td class='customer-info'><strong><?= "{$account['bank_name']}#{$data['check_number']}" ?></strong></td></tr>
                            <tr><td class="labels">Check Date:</td><td class='customer-info'><strong><?= $data['check_date'] ?></strong></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-xs-4">
                    <table>
                        <tbody>
                            <tr><td class="labels">Voucher No.</td><td class='text-center customer-info'><strong>(Ref.) DC #<?= str_pad($data['id'], 4, 0, STR_PAD_LEFT) ?></strong></td></tr>
                            <tr><td class="labels">Date:</td><td class='text-center customer-info'><strong><?= $data['date'] ?></strong></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class='row products'>
                <div class='col-xs-12'>
                    <table style="width:100%">
                        <tbody>
                            <?php for($x = 0; $x<7; $x++):?>
                                <tr><td>&nbsp;</td></tr>
                            <?php endfor;?>
                            <tr>
                                <td class="text-center"><?= $data['remarks']?></td>
                            </tr>
                            <?php for($x = 0; $x<7; $x++):?>
                                <tr><td>&nbsp;</td></tr>
                            <?php endfor;?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id='footer' style="margin-top:20px">
                <div class='row'> 
                    <div class='col-xs-9'>
                        <p>AMOUNT: <strong><?= convertCurrencyToWords(number_format($data['check_amount'], 2, '.', '')); ?></strong></p>
                    </div>
                    <div class='col-xs-3'>
                        <p class='text-right'>TOTAL(Php): <strong ><?= number_format($data['check_amount'], 2) ?></strong></p>
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
            </div>
        </div>
    </body>
</html>
