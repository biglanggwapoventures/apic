<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <style>
            html, body{
                font-family: 'Calibri';
                font-size: 10px;
            }
            table{
                width:100%;
                border-collapse:collapse;
            }
            table#details tbody{
                border:1px solid black;
                text-align: center;
            }
            table#details tbody td{
                border:1px solid black;
                text-align: center;
            }
            table#details thead th{
                border:1px solid black;
                text-align: center;
            }
            table#gen-info, table#gen-info td{
                border:none;
                border:none;
            }
        </style>
    </head>
    <body>
        <div>
             <table id='gen-info'>
                <tr><td colspan='2' style='text-align: center'><strong>PROVERA NUTRITIONAL SOLUTIONS CORPORATION</strong></td></tr>
                <tr><td colspan='2' style='text-align: center'>GY WAREHOUSE 1, A. BACALTOS SR. STREET, LAWAAN, TALISAY CITY</td></tr>
                <tr><td colspan='2' style='text-align: center'>TEL/FAX. NO. (032) 253-4570</td></tr>
                <tr><td colspan='2' style='text-align: right'>D.R. NO.:&nbsp;<strong><?= $contents['id'] ?></strong></td></tr>
                <tr><td colspan='2' style='text-align: center;'><strong>P A C K I N G&nbsp;&nbsp;&nbsp;&nbsp;L I S T</strong></td></tr>
                <tr>
                    <td style='width:50%;text-align: left'>CUSTOMER:&nbsp;<strong><?= $contents['company_name'] ?></strong></td>
                    <td style='width:50%;text-align: right'>DATE DELIVERED:&nbsp;<strong><?= $contents['formatted_date'] ?></strong></td>
                </tr>
                <tr>
                    <td style='width:50%;text-align: left'>ADDRESS:&nbsp;<strong><?= $contents['address'] ?></strong></td>
                    <td style='width:50%;text-align: right'>P.O. NO:&nbsp;<strong><?= $contents['order_id'] ?></strong></td>
                </tr>
            </table>
            <table id='details'>
                <thead>
                    <tr>
                        <th>Quantity</th>
                        <th>Product</th>
                        <th>Unit Price</th>
                        <th>Discount</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                 <tfoot>
                    <tr><td colspan='4'rowspan="2" style='text-align: right;vertical-align: middle!important'>TOTAL:</td><td style='text-align: center;vertical-align: middle!important'> <strong><?= $contents['total_amount'] ?></strong></td></tr>
                </tfoot>
                <tbody>
                    <?php for ($x = 0; $x < count($contents['details']['id']); $x++): ?>
                        <tr>
                            <td><?= "{$contents['details']['this_delivery'][$x]} {$contents['details']['unit_description'][$x]}" ?></td>
                            <td><?= $contents['details']['product_description'][$x] ?></td>
                            <td><?= number_format($contents['details']['unit_price'][$x], 2) ?></td>
                            <td><?= number_format($contents['details']['discount'][$x], 2) ?></td>
                            <?php $amount = ($contents['details']['unit_price'][$x]*$contents['details']['this_delivery'][$x])-($contents['details']['unit_price'][$x]*$contents['details']['this_delivery'][$x])?>
                            <td><?= number_format($contents['details']['amount'][$x], 2) ?></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
            <p style="f">
                <span style="float:left">PREPARED BY: <?= $this->session->userdata('name') ?></span>
                <span style="float:right;">RECEIVED BY:&nbsp;&nbsp;<span style="text-decoration: underline"><?= $contents['company_name'] ?></span></span><br/><br/>
                <span style="float:left">DELIVERED BY:&nbsp;&nbsp;<strong><?= $contents['trucking_name'] ?></strong></span>
            </p>

        </div>

    </body>
</html>
