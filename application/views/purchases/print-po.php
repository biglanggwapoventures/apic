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
                <tr><td colspan='2' style='text-align: right'>P.O. NO.: <strong><?= $contents['id'] ?></strong></td></tr>
                <tr><td colspan='2' style='text-align: center'><strong>P U R C H A S E&nbsp;&nbsp;&nbsp;&nbsp;O R D E R</strong></td></tr>
                <tr>
                    <td style='width:50%;text-align: left'>SUPPLIER:&nbsp;<strong><?= $contents['supplier'] ?></strong></td>
                    <td style='width:50%;text-align: right'>DATE:&nbsp;<strong><?= $contents['formatted_date'] ?></strong></td>
                </tr>
            </table>
            <table id='details'>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Formulation Code</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr><td colspan='4'rowspan="2"  style='text-align: right;vertical-align: middle!important'>TOTAL:</td><td style='text-align: center;vertical-align: middle!important'> <strong><?= $contents['total_amount'] ?></strong></td></tr>
                </tfoot>
                <tbody>
                    <?php foreach ($contents['details'] as $details): ?>
                        <tr>
                            <td><?= $details['product'] ?></td>
                            <td><?= $details['formulation_code'] ?></td>
                            <td><?= $details['quantity'] ?></td>
                            <td><?= $details['unit_price'] ?></td>
                            <td><?= $details['amount'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p>
                <span>PREPARED BY:</span>
            </p>
        </div>
    </body>
</html>
