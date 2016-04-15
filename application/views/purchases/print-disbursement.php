<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <style>

            html, body{
                font-family: 'Calibri';
                font-size: 12px;
            }table{
                border-collapse:collapse;
            }
            table, td, th
            {
                border:1px solid black;
            }
            table.b-less{
                border: none;
            }
            table.b-less td{
                border:none!important;
            }
            table.b-less  tr:first-child{
                width:10%
            }
            table.b-less  tr:last-child{
                width:10%
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
            <p><strong>&nbsp;&nbsp;P A Y M E N T&nbsp;&nbsp;V O U C H E R</strong></p>
            <table style='width:100%' class='no-border'>
                <tr>
                    <td style='width:50%;text-align: left;'>PAY TO:&nbsp;
                        <strong>
                            <?php if ($contents['payment_type'] == M_Purchase_Disbursement::PAYMENT_TYPE_RR): ?>
                                <?= $contents['supplier'] ?>
                            <?php else: ?>
                                <?= M_Purchase_Disbursement::get_chart_of_accounts($contents['coa_code']) ?>
                            <?php endif; ?>
                        </strong>
                    </td>
                    <td style='width:50%;text-align: right'>DATE:&nbsp;<strong><?= $contents['formatted_date'] ?></td>
                </tr>
                <tr>
                    <td style='width:50%;text-align: left;'>ADDRESS:&nbsp;<strong><?= $contents['supplier_address'] ?></strong></td>
                    <td style='width:50%;text-align: right;'>VOUCHER NO.:&nbsp;<strong><?= $contents['id'] ?></strong></td>
                </tr>
            </table>
            <table width="100%">
                <thead>
                <th style="text-align: left!important" colspan="3">PAYMENT FOR</th>
                </thead>
                <tbody>
                    <tr rowspan="3">
                        <td colspan="3" style="text-align: left;padding:10px;">
                            <?= $contents['other_details'] ?>
                        </td>
                    </tr>
                </tbody>
            </table><br>
            <?php if (isset($contents['check_transactions'])): ?>
                <table width="100%">
                    <thead>
                        <tr>
                            <th style="text-align:left" colspan="5">
                                <strong> BANK TRANSACTIONS</strong>
                            </th>
                        </tr>
                        <tr>
                            <th><strong>Bank Name</strong></th>
                            <th><strong>Check Number</strong></th>
                            <th><strong>Check Date</strong></th>
                            <th><strong>Deposit Date</strong></th>
                            <th><strong>Amount</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contents['check_transactions'] as $bank): ?>
                            <tr>
                                <td><?= $bank['bank_name'] ?></td>
                                <td><?= $bank['check_number'] ?></td>
                                <td><?= $bank['check_date'] ?></td>
                                <td><?= $bank['deposit_date'] ?></td>
                                <td><?= $bank['amount'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <table border="0" class="b-less" width="100%">
                <tr>
                    <td colspan="4"</td>
                    <td style="text-align: right">Php: <strong><?= $contents['disbursed_amount'] ?></strong></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:20%;text-align:left">PREPARED BY:</td>
                    <td style="width:20%;text-align: center;border-bottom: 1px solid black!important;"><span style="text-transform: uppercase;font-weight: bold"><?= $this->session->userdata('name') ?></span></td>
                    <td style="width:60%" colspan="3">&nbsp;</td>
                </tr>
                <tr><td style="height:10px;"></td></tr>
                <tr>
                    <td style="width:20%;text-align:left">CHECKED BY:</td>
                    <td  style="width:30%;text-align: center;border-bottom: 1px solid black!important;"><span style="font-weight: bold">GERALD N. CAMPOS</span></td>
                    <td style="width:20%;text-align:right">RECEIVED BY:</td>
                    <td colspan="2"style="width:30%;text-align:left;border-bottom: 1px solid black!important">&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td style="text-align: center;vertical-align: top;">Vice President</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="text-align: center;vertical-align: top;">Signature over printed name</td>
                </tr>
                <tr><td style="height:10px;"></td></tr>
                <tr>
                    <td style="width:20%;text-align:left">APPROVED BY:</td>
                    <td style="width:20%;text-align: center;border-bottom: 1px solid black!important;"><span style="font-weight: bold">GILBERT C. YAP</span></td>
                    <td style="width:20%" colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td style="text-align: center">President</td>
                    <td colspan="3">
                </tr>
            </table>
        </div>
        <pre>
            <?php print_r($contents) ?>
        </pre>
    </body>
</html>
