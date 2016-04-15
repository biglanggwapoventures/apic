<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <?php include_css('bootstrap.min.css') ?>
        <style type="text/css">
            html, body{
                font-size: 12px!important;
                font-family: 'Tahoma';
                height: 100%!important; 
            }
            .content{
                height: auto;
                min-height: 100%;
                position: relative;
                display: block;
            }
            .header h4,p{
                margin-bottom: 0px;
            }
            .f14px{
                font-size: 14px;
            }
            .mb-5{
                margin-bottom:10px;
            }
            tbody tr td:nth-child(5), td:nth-child(6), td:nth-child(7), td:nth-child(8){
                text-align: right;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid content">
            <div class="row header">
                <div class="col-xs-12 text-center">
                    <h4>PROVERA NUTRITIONAL SOLUTIONS CORPORATION</h4>
                    <p>GY Warehouse 1, A. Bacaltos Sr. St., Lawaan 1, Talisay City</p>
                    <p class="mb-5">Tel No.: 514-8890 | Fax No.: 491-3485 | Email: provera.feedmill@gmail.com</p>
                    <strong class="f14px">Statement of Accounts</strong>
                    <h4><?= $customer[0]['company_name']?></h4>
                    <p><?= $customer[0]['address']?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 clearfix">
                    <p class="pull-left">As of: <?=date("F d, Y")?></p>
                    <p class="pull-right">Credit Terms: <?= $customer[0]['credit_term'] == 0? 'COD' :$customer[0]['credit_term'] .' Days' ?></p>
                    <table class="table">
                        <thead><tr><th style="width:15%">DATE</th><th>PL#</th><th>QTY</th><th>PRODUCT</th><th>UNIT PRICE</th><th>AMOUNT</th><th>PAID/CM</th><th>BALANCE</th></tr></thead>
                        <tbody>
                            <?php $total_payable = 0; ?>
                            <?php foreach ($data as $pl): ?>
                                <?php $balance = (double)$pl['total_amount'] - (double)$pl['total_paid']?>
                                <?php $total_payable += $balance ?>
                                <?php foreach ($pl['line'] as $index => $line): ?>
                                    <tr>
                                        
                                        <?php if ($index === 0): ?>
                                        <td><?= $pl['date'] ?></td>
                                        <td><?= $pl['fk_sales_delivery_id'] ?></td>
                                        <?php else: ?>
                                        <td></td>
                                        <td></td>
                                        <?php endif; ?>
                                        
                                        <td><?= $line['this_delivery'] ?></td>
                                        <td><?= $line['description'] . ($line['code'] ? ' (' . $line['code'] . ')' : '') ?></td>
                                        <td><?= number_format($line['unit_price'], 2) ?></td>
                                        <td><?= number_format($line['amount'], 2) ?></td>
                                        
                                        <?php if ($index === count($pl['line']) - 1): ?>
                                        <td><?= (double) $pl['total_paid'] ? number_format($pl['total_paid'], 2) : '' ?></td>
                                        <td><?= number_format($balance, 2) ?></td>
                                        <?php else: ?>
                                        <td></td>
                                        <td></td>
                                        <?php endif; ?>
                                        
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr><td colspan="5"></td><td colspan="2">Total Payable:</td><td class="text-right" colspan="2"><?= number_format($total_payable, 2)?></td></tr>
                            <tr><td colspan="7" style="border:0">Make all checks payable to: <strong style="text-decoration: underline">PROVERA NUTRITIONAL SOLUTIONS CORPORATION</strong></td></tr>
                            <tr><td colspan="2" style="border:0">Noted by:</td></tr>
                            <tr><td style="border:0"></td><td  style="border:0;padding-top:0;padding-bottom:0" colspan="4">Mark Vincent Chua</td></tr>
                            <tr><td style="border:0"></td><td style="border:0;vertical-align: top;padding-top:0;padding-bottom:0" colspan="4">Plant Manager</td></tr>
                            <tr><td style="border:0" colspan="6">Prepared by: <?= $this->session->userdata('name')?></td></tr>
                            <tr><td style="border:0" colspan="4">Checked and received the original copies of P.L. by:</td><td colspan="4" style="border:0;border-bottom:1px solid black"></td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
