<style type="text/css">
    tr:not(.static) td:not(:first-child){
        text-align: right;
    }
/*    tr:last-child td:not(:first-child){
       font-weight: bold;
    }*/
    tr > th{
        border: 0px;
    }
    .noScreen{
        border: 0;
    }
    @media screen
    {
        .noPrint{}
        .noScreen{display:none;}
    }

    @media print
    {
        .noPrint{display:none;}
        .noScreen{}
        .print {page-break-inside:avoid;}
    }
</style>
<div class="row" id="print-area">
    <div class="col-sm-12">
        <div class="box box-solid">
            <div class="box-header">
                <div class="box-tools" style="position: absolute;width: 100%;z-index: 999">
                    <a id="print-report" href="#"><i class="fa fa-print"></i> Print page</a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div id="table-header">
                        <div class="col-sm-12 text-center text-bold text-underline font-16">
                            AGING OF RECEIVABLES
                        </div>
                        <div class="col-sm-12 no-padding text-center" style="font-weight:bold">As of: <?= date("F d, Y") ?></div>
                    </div>
                    <div class="col-sm-12">
                        <table id="aorr" class="table table-bordered borderless table-condensed" >
                            <tbody>
                                <!-- <tr class="borderless static"><td colspan="8" class="text-center text-bold text-underline font-16">AGING OF RECEIVABLES</td></tr> -->
                                <!-- <tr class="borderless static"><td colspan="8" class="no-padding text-center">As of: <?= date("F d, Y") ?></td></tr> -->
                                <tr class="borderless static"><td colspan="1"><input type="text" class="form-control hidden" placeholder="Filter customer name"/></td></tr>
                                <tr class="static active">
                                    <td class="text-center">CUSTOMER</td>
                                    <td class="text-center">0-30 DAYS</td>
                                    <td class="text-center">31-60 DAYS</td>
                                    <td class="text-center">61-90 DAYS</td>
                                    <td class="text-center">OVER 90 DAYS</td>
                                    <td class="text-center">TOTAL</td>
                                    <td class="text-center">OVERPAYMENT</td>
                                    <td class="text-center">TOTAL OUTSTANDING</td>
                                </tr>
                                <?php $thirty_total = 0; ?>
                                <?php $sixty_total = 0; ?>
                                <?php $ninety_total = 0; ?>
                                <?php $plus_total = 0; ?>
                                <?php $overpayment_total = 0; ?>
                                <?php $overall = 0; ?>
                                <?php foreach ($data as $customer => $balances): ?>
                                    <?php $thirty = 0; ?>
                                    <?php $sixty = 0; ?>
                                    <?php $ninety = 0; ?>
                                    <?php $plus = 0; ?>
                                    <?php $overpayment = 0; ?>
                                    <tr class="b">
                                        <td><?= $customer ?></td>
                                        <td>
                                            <?php if (isset($balances['30'])): ?>
                                                <?= number_format($balances['30'], 2) ?>
                                                <?php $thirty = $balances['30'] ?>
                                                <?php $thirty_total += $balances['30']; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($balances['60'])): ?>
                                                <?= number_format($balances['60'], 2) ?>
                                                <?php $sixty = $balances['60'] ?>
                                                <?php $sixty_total += $balances['60']; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($balances['90'])): ?>
                                                <?= number_format($balances['90'], 2) ?>
                                                <?php $ninety_total += $balances['90']; ?>
                                                <?php $ninety = $balances['90'] ?>
                                            <?php endif; ?>
                                        </td>
                                        <td >
                                            <?php if (isset($balances['90+'])): ?>
                                                <?= number_format($balances['90+'], 2) ?>
                                                <?php $plus_total += $balances['90+']; ?>
                                                <?php $plus = $balances['90+'] ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php $total = $thirty + $sixty + $ninety + $plus ?>
                                            <?= number_format($total, 2) ?>
                                        </td>
                                         <td class="warning">
                                            <?php if (isset($balances['overpayment']) && $balances['overpayment'] > 0): ?>
                                                <?= number_format($balances['overpayment'], 2) ?>
                                                <?php $overpayment = $balances['overpayment']; ?>
                                                <?php $overpayment_total += $balances['overpayment']; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php $total = $thirty + $sixty + $ninety + $plus - $overpayment ?>
                                            <?= number_format($total, 2) ?>
                                            <?php $overall+=$total ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td class="no-border"></td>
                                    <td class="active"><?= number_format($thirty_total, 2) ?></td>
                                    <td class="active"><?= number_format($sixty_total, 2) ?></td>
                                    <td class="active"><?= number_format($ninety_total, 2) ?></td>
                                    <td class="active"><?= number_format($plus_total, 2) ?></td>
                                    <td class="active text-bold" style="font-size:130%"><?= number_format($overall, 2) ?></td>
                                </tr>
                                <tr>
                                    <td class="no-border"></td>
                                    <td class="active"><?= round(($thirty_total/$overall) * 100 ,1) ?>%</td>
                                    <td class="active"><?= round(($sixty_total/$overall) * 100,1) ?>%</td>
                                    <td class="active"><?= round(($ninety_total/$overall) * 100,1) ?>%</td>
                                    <td class="active"><?= round(($plus_total/$overall) * 100,1) ?>%</td>
                                </tr>
                                <tr id="remove-me"><td colspan="6" class="no-border">Time elapsed: <?= $this->benchmark->elapsed_time();?>s</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="print-div" class="hidden">
    <div class="row">
        <div class="col-sm-12 text-center">
            <h5 style="margin-bottom:0;font-weight: bold">
                ARDITEZZA POULTRY INTEGRATION CORPORATION
            </h5>
        </div>
        <div class="col-sm-12 text-center">
            <th colspan="10" class="text-center font-normal">
                Ultima Residences Tower 3, Unit 1018, Osmena Blvrd., Cebu City<br>
                Tel/Fax Nos.: (032) 253-4570 to 71 / 414-3312 / 512-3067
            </th>
        </div>
    </div><br/>
    <div id="table-dummy"></div>
</div>