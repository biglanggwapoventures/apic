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
    <body style="padding:0;margin:0;width:10in;height: 3in">
        <span class="date" style="position: absolute;left:7.8in;top: 0.8in;width: 100%;"> <?= date('F d, Y', strtotime($date))?></span>
        <span style="position: absolute; top: 1.3in;  left: 0.2in;"><?= $pay_to?></span>
        <span style="position: absolute; top: 1.3in;  left: 7.9in;"><?= number_format($amount, 2)?></span>
        <span style="position: absolute; top: 1.8in; left: 0.2in; width:10in;"><?= convertCurrencyToWords($amount)?></span>
    </body>
</html>
