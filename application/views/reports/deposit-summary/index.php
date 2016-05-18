<style type="text/css">
    tbody td{
        padding: 3px;
        border: 1px solid black;
    }
    table tbody > tr > td:nth-child(2){ text-align: right; }
    table tbody > tr > td:nth-child(1),td:nth-child(3),td:nth-child(4),td:nth-child(5),td:nth-child(6){ text-align: center; }
    thead tr.repeated th{
        padding: 3px;
        border:1px solid black;
        text-transform:uppercase;
        text-align: center;
    }
    thead th{ background: white; }
    .colon{
        padding-right: 3px;
        padding-left: 3px;
        text-align: center;
    }
    .font-normal{ font-weight: normal; }
    table thead > tr:nth-last-child(2) > th{ padding-bottom:5px; }
    @media print {
      a[href]:after {
        content: none !important;
      }
    }
    @media screen {
        .noPrint{}
        .noScreen{display:none;}
    }
    @media print {
        .noPrint{display:none;}
        .noScreen{}
        table {page-break-after: always;}
    }
</style>
<div class="box box-solid">
    <div class="box-header">
        <div class="box-tools" style="position: absolute;width: 100%;z-index: 999">
            <a id="print-report" href="#"><i class="fa fa-print"></i> Print page</a>
        </div>
    </div>
   <div class="box-body" data-edit-url="<?= base_url('reports/check_master_list/update_check_number')?>">
        <div class="row">
            <div class="col-sm-12">
                <table style="width:100%" class="table-striped">
                    <thead class="first-thead">
                        <tr class="noScreen">
                            <th colspan="7" class="text-center">
                                <h5 style="margin-bottom:0;font-weight: bold">
                                    ARDITEZZA POULTRY INTEGRATION CORPORATION
                                </h5>
                            </th>
                        </tr>
                        <tr class="noScreen">
                            <th colspan="7" class="text-center font-normal">
                                Ultima Residences Tower 3, Unit 1018, Osmena Blvrd., Cebu City<br>
                                Tel/Fax Nos.: (032) 253-4570 to 71 / 414-3312 / 512-3067
                            </th>
                        </tr>
                        <tr><th colspan="7" class="text-center"><h4 style="margin-bottom:0;text-decoration:underline">Deposit Summary</h4></th></tr>
                         <tr>
                            <th colspan="7" class="text-center" style="padding-bottom:10px">
                                <a data-toggle="modal" data-target="#options">
                                    <?= isset($params['bank_account_details']) ? $params['bank_account_details']['bank_name'] : 'Click to choose bank'?>
                                </a>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-right">Deposit Date: </th>
                            <th class="text-center">
                                <?= date_create($params['date'])->format('M d, Y') ?>
                            </th>
                        </tr>
                        <tr class="repeated">
                            <th rowspan="2">customer</th>
                            <th rowspan="2">amount</th>
                            <th rowspan="2">payment<br>type</th>
                            <th colspan="2">check details</th> 
                            <th rowspan="2">depositor bank</th>
                            <th rowspan="2">validation code</th>
                        </tr>
                        <tr class="repeated">
                            <th>check no.</th><th>check date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!isset($params['bank_account_details'])):?>
                            <tr><td colspan="7" class="text-center">Choose a bank acount and date to start</td></tr>
                        <?php elseif(empty($data)):?>
                            <tr><td colspan="7" class="text-center">No results to show.</td></tr>
                        <?php else:?>
                            <?php foreach($data AS $row):?>
                                <tr>
                                    <td><?= $row['customer']?></td>
                                    <td><?=  number_format(($row['check_amount'] ? $row['check_amount'] : $row['cash_amount']), 2)?></td>
                                    <td><?= $row['payment_method']?></td>
                                    <td><?= $row['check_number']?></td>
                                    <td><?= $row['check_date'] ? date_create($row['check_date'])->format('M d, Y ') : ''?></td>
                                    <td><?= $row['depositor_bank']?></td>
                                    <td></td>
                                </tr>
                        <?php endforeach;?>
                        <?php endif;?>
                    </tbody>
                </table>
                <small>Process time: <?= $this->benchmark->elapsed_time();?> second(s)</small>
            </div>
        </div>
   </div>
</div>

<div class="modal fade" id="options" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Select check no. range</h4>
            </div>
            <form action="<?= current_url()?>" method="GET">
                <div class="modal-body">
                    <div class="form-group">
                       <label>Bank Account</label>
                       <?= form_dropdown('bank_account', $banks_dropdown, $params['bank_account'], 'class="form-control"') ?>
                    </div>
                    <div class="form-group">
                       <label>Check No. Start</label>
                       <?= form_input('date', $params['date'], 'class="form-control datepicker"') ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-flat">Generate</button>
                    <button type="button" data-dismiss="modal" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="hidden" id="table-dummy">
    
</div>