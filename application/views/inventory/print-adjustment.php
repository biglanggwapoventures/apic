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
                <tr><td colspan='2' style='text-align: right'>REQUEST NO.:&nbsp;<strong><?= $contents['general']['id'] ?></strong></td></tr>
                <tr><td colspan='2' style='text-align: center;'><strong>S T O C K&nbsp;&nbsp;&nbsp;&nbsp;A D J U S T M E N T&nbsp;&nbsp;&nbsp;&nbsp;R E Q U E S T</strong></td></tr>
                <tr>
                    <td style='width:50%;text-align: left'>REQUESTED BY:&nbsp;<strong><?= $contents['general']['FirstName'].' '.$contents['general']['LastName'] ?></strong></td>
                    <td style='width:50%;text-align: right'>DATE:&nbsp;<strong><?= date("F j, Y", strtotime($contents['general']['datetime'])); ?></strong></td>
                </tr>
                <tr>
                    <?php if ($contents['general']['status'] == M_Status::STATUS_DEFAULT OR $contents['general']['status'] == M_Status::STATUS_PENDING): ?>
                        <?php $status = 'PENDING' ?>
                    <?php elseif ($contents['general']['status'] == M_Status::STATUS_CANCELLED): ?>
                        <?php $status = 'CANCELLED' ?>
                    <?php else: ?>
                        <?php $status = 'APPROVED' ?>
                    <?php endif; ?>
                    <td style='width:50%;text-align: left'>REASON FOR REQUESTING:&nbsp;<strong><?= $contents['general']['reason'] ?></strong></td>
                    <td style='width:50%;text-align: right'>STATUS:&nbsp;<strong class="status"><?= $status ?></strong></td>
                </tr>
            </table>
            <table id='details'>
                <thead>
                    <tr>
                        <th>Product Code</th>
                        <th>Description</th>
                        <th>Current Stock on hand</th>
                        <th>Requested stock adjustment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($contents['details'] as $c): ?>
                        <tr>
                            <td><?= $c['code'] ?></td>
                            <td><?= $c['description'] ?></td>
                            <td><?= $c['stock'].' '.$c['packaging'] ?></td>
                            <td><?= $c['quantity'].' '.$c['packaging'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p style="f">
                <span style="float:left;">APPROVED BY: 
                        <span class="approved_by" style="text-decoration: underline">
                            <?= ($status != 'APPROVED')? '____________________' : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$contents['general']['approved_by']['Name'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' ?>
                </span>
            </p>

        </div>
    </body>
</html>
