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
    <body style="width: 50%">
        <div>
            <table id='gen-info'>
                <tr><td colspan='2' style='text-align: center'><strong>PROVERA NUTRITIONAL SOLUTIONS CORPORATION</strong></td></tr>
                <tr><td colspan='2' style='text-align: center'>GY WAREHOUSE 1, A. BACALTOS SR. STREET, LAWAAN, TALISAY CITY</td></tr>
                <tr><td colspan='2' style='text-align: center'>TEL/FAX. NO. (032) 253-4570</td></tr>
                <tr><td colspan='2' style='text-align: center'><strong>CO U N T E R&nbsp;&nbsp;&nbsp;&nbsp;R E C E I P T</strong></td></tr>
                <tr><td colspan='2' style='text-align: left'>C.R. NO.: <strong><?= $contents['id'] ?></strong></td></tr>
                <tr><td colspan='2' style='text-align: left'>Date: <strong><?= $contents['formatted_date'] ?></strong></td></tr>
                <tr><td colspan='2' style='text-align: left'>Address: <strong><?= $contents['customer_address'] ?></strong></td></tr>
            </table>
            <table id='details'>
                <thead>
                    <tr>
                        <th>P.L. No.</th>
                        <th>Invoice No.</th>
                        <th>Date</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tfoot><tr><td colspan='2' style='border:none'></td>
                        <td style='text-align: right;border:none;padding-top:10px;padding-right: 20px;'>Total Amount:</td>
                        <td style='text-align: right;border:none;padding-top:10px;'><b> <?= number_format($contents['total_amount'], 2) ?></b></td></tr></tfoot>
                <tbody>
                    <?php for ($x = 0; $x < count($contents['details']['fk_sales_delivery_id']); $x++): ?>
                        <tr>
                            <td style='text-align: center'><?= $contents['details']['fk_sales_delivery_id'][$x] ?></td>
                            <td style='text-align: center'><?= "" ?></td>
                            <td style='text-align: center'><?= date('F d, Y', strtotime($contents['details']['date'][$x])) ?></td>
                            <td style='text-align: center'><?= number_format($contents['details']['amount'][$x], 2) ?></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
            <table>
                <tr><td style='width:20%;'>Prepared by:</td><td><?= $this->session->userdata('name') ?></td></tr>
                <tr><td>Received by:</td><td><?= $contents['company_name'] ?></td></tr>
            </table>
        </div>
    </body>
</html>
