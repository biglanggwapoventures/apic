<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style type="text/css">
            html{
                font-size: 130%!important;
                color: black!important;
                font-weight: bold!important;
            }
        </style>
    </head>
    <body style="padding:0;margin:0;width:8in;height: 3in">
        <span class="date" style="position: absolute;left:7.6in;top: 0.8in;width: 100%;"> <?= date('F d, Y', strtotime($date))?></span>
        <span style="position: absolute; top: 1.2in;  left: 0.3in; width:100%;"><?= $pay_to?></span>
        <span style="position: absolute; top: 1.2in;  left: 7.7in; width:100%;"><?= number_format($amount, 2)?></span>
        <span style="position: absolute; top: 1.7in; left: 0.5in; width:100%;"><?= convertCurrencyToWords($amount)?></span>
    </body>
</html>
