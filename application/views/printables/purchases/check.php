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
            table tbody td{
                font-weight: bold;
            }
            #reference{
                font-size: 10px;
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
    <body style="padding-top:3px;">
        <br/><br/><br/><br/>
        <div class="container" style="padding-top:12px;">
            <div class="row">
                <div class="col-xs-offset-1 col-xs-7">
                    <table>
                        <tr><td><?=$id?></td></tr>
                        <tr><td><?=$date?></td></tr>
                        <tr><td><?=number_format($amount, 2)?></td></tr>
                        <tr><td style="padding-top:5px;"><?=(!empty(trim($payee)) ? trim($payee) : trim($supplier))?></td></tr>
                    </table>
                </div>
                <div class="col-xs-4">
                    <table>
                        <tr><td>&nbsp;</td></tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr><td style="padding-left:30px;"><?= ($print_check_date) ? $check_date : "" ?></td></tr>
                    </table>
                </div>
            </div> <!-- end .row -->
            <br/><br/><br/><br/><br/><br/><br/><br/><br/>
            <div class="row">
                <div class="col-xs-offset-1 col-xs-5" style="padding-left:30px; padding-top:3px;">
                    <table>
                        <tr><td><?=$id?></td></tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr><td><?=number_format($amount, 2)?></td></tr>
                        <tr><td><?=$remarks?:'&nbsp;'?></td></tr>
                    </table>
                </div>
                <div class="col-xs-4">
                    <table>
                        <tr><td>&nbsp;</td></tr>
                        <tr><td><?=$date?></td></tr>
                    </table>
                </div>
            </div> <!-- end .row -->
        </div> <!-- end .container -->
        <br/><br/><br/>
        <div class="container-fluid" style="height: 303px;">
            <div class="row"  style="padding-top:-3px;">
                <table style="width:100%" id="reference">
                    <tr>
                        <td width="9%"></td>
                        <td width="10%"></td>
                        <td width="17%"></td>
                        <td width="17%"></td>
                        <td width="9%"></td>
                        <td width="9%"></td>
                        <td width="9%"></td>
                    </tr>
                    <?php
                        $total = 0;
                        for($x=0; $x<count($line); $x++){
                            $raw_date = $line[$x]['receiving_date'];
                            $formatted_date = date_format(date_create($raw_date), "Y-m-d");
                            echo "<tr>";
                                echo "<td>RR# ".$line[$x]['id']."</td>";
                                echo "<td>".$line[$x]['pr_number']."</td>";
                                echo "<td class='text-center'>".$formatted_date."</td>";
                                echo "<td class='text-center'>".number_format($line[$x]['amount'], 2)."</td>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td>".number_format($line[$x]['amount'], 2)."</td>";
                            echo "</tr>";
                            $total += $line[$x]['amount'];
                        }
                    ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="border-top:1px solid black;"><?=number_format($total, 2)?></td>
                    </tr>
                </table>
            </div>
        </div> <!-- end .container-fluid -->
        <div class="container">
            <div class="row">
                <div class="col-xs-offset-1 col-xs-7">*** <?=(!empty(trim($payee)) ? trim($payee) : trim($supplier))?> ***</div>
                <div class="col-xs-1"><div style="padding-left:25px; padding-top:3px;"><?=number_format($amount, 2)?></div></div>
            </div>
            <br/>
            <div class="row">
                <div class="col-xs-11"><div style="padding-left:25px; padding-top:-3px;">*** <?=convertCurrencyToWords("{$amount}")?> ***</div></div>
            </div>
        </div>
    </body>
</html>