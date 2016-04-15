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
                font-size: 12px;
            }
            table{
                border-collapse:collapse;
            }
            table, td, th
            {
                border:1px solid black;
            }
            table.no-border, table.no-border td{
                border:none;
            }
        </style>
    </head>
    <body>
        <div style="text-align:center;display:block;">
            <p style='text-align: center'>
                <strong>PROVERA NUTRITIONAL SOLUTIONS CORPORATION</strong><br>
                <small>
                    GY WAREHOUSE 1, A. BACALTOS SR. STREET, LAWAAN, TALISAY CITY</br>
                    TEL/FAX. NO. (032) 253-4570
                </small>
            </p>
            <p><strong>&nbsp;&nbsp;R E C E I V I N G&nbsp;&nbsp;R E P O R T</strong></p>

            <table style='width:100%' class='no-border'>
                <tr>
                    <td style='width:50%;text-align: left;'>RECEIVED FROM:&nbsp;<strong><?= $contents['supplier'] ?></strong></td>
                    <td style='width:50%;text-align: right'>DATE:&nbsp;<strong><?= $contents['formatted_date'] ?></td>
                </tr>
                <tr><td style='width:50%;text-align: left;'>P.O. #:&nbsp;<strong><?= $contents['fk_purchase_order_id'] ?></strong></td></tr>
                <tr><td style='width:50%;text-align: left;'>R.R. #:&nbsp;<strong><?= $contents['id'] ?></strong></td></tr>
            </table>
            
            <table style='width:100%;margin-top:20px;'>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr></tr>
                </tfoot>
                <tbody>
                    <?php foreach ($contents['details'] as $details): ?>
                        <tr>
                            <td><?= $details['product'] ?></td>
                            <td><?= "{$details['delivered_quantity']} {$details['unit_description']}" ?></td>
                            <td><?= $details['amount'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <br/><br/>
            <p>
                <span style="float:right">TOTAL: <strong style='font-size:120%;'><?= $contents['total_amount'] ?></strong></span>
                <span style="float:left;margin-right:120px">RECEIVED BY:</span>
            </p>
        </div>
    </body>
    <pre style='display:none'>
        <?php print_r($contents) ?>
    </pre>

</html>
