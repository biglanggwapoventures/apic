<style type="text/css">
    th i{
        margin-left: 10px;
    }
    tbody td{
        padding: 3px;
        border: 1px solid black;
    }
    tbody > tr > td:nth-child(4){
        text-align: right;
    }
    tbody > tr > td:nth-child(1),td:nth-child(2),td:nth-child(3),td:nth-child(5){
        text-align: center;
    }
    thead > tr:nth-child(6) > th{
        padding: 3px;
        border:1px solid black;
        text-transform:uppercase;
        text-align: center;
    }

    thead th{
        background: white;
    }
    .colon{
        padding-right: 3px;
        padding-left: 3px;
        text-align: center;
    }
    .font-normal{
        font-weight: normal;
    }
    thead > tr:nth-last-child(2) > th{
        padding-bottom:5px;
    }
    @media print {
      a[href]:after {
        content: none !important;
      }
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
        table {page-break-after: always;}
    }
</style>
<div class="box box-solid">
    <div class="box-header">
        <div class="box-tools" style="position: absolute;width: 100%;z-index: 999">
            <a id="print-report" href="#"><i class="fa fa-print"></i> Print page</a>
        </div>
    </div>
   <div class="box-body">
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
                        <tr><th colspan="7" class="text-center"><h4 style="margin-bottom:0;text-decoration:underline">CHECK MASTER LIST</h4></th></tr>
                         <tr>
                            <th colspan="7" class="text-center" style="padding-bottom:10px">
                                <a data-toggle="modal" data-target="#options">
                                    <?= isset($params['bank_account_details']) ? $params['bank_account_details']['bank_name'] : 'Click to choose bank'?>
                                </a>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-right">Start Check No: </th>
                            <th class="text-center">
                                <?= $params['check_number_start'] ? $params['check_number_start'] : '-'?>
                            </th>
                            <th colspan="3"></th>
                            <th>End Check No: </th>
                            <th> <?= $params['check_number_end'] ? $params['check_number_end'] : '-'?></th>
                        </tr>
                        <tr class="active repeated">
                            <th>CHECK NO.</th>
                            <th>CHECK DATE</th>
                            <th>PAYEE</th>
                            <th>AMOUNT</th> 
                            <th>TYPE</th>
                            <th>RELEASE DATE</th>
                            <th>RECEIVED BY</th>
                        </tr>
                    </thead>
                    <tbody>

                        
                        <?php if(!isset($params['bank_account_details'])):?>
                            <tr><td colspan="7" class="text-center">Choose a bank acount and date to start</td></tr>
                        <?php elseif(empty($data)):?>
                            <tr><td colspan="7" class="text-center">Choose a bank acount and date to start</td></tr>
                        <?php else:?>
                            <?php foreach($data AS $row):?>
                                <tr>
                                    <td><?= $row['check_number']?></td>
                                    <td><?= date_create($row['check_date'])->format('M d, Y')?></td>
                                    <td><?= $row['payee']?></td>
                                    <td><?= number_format($row['amount'], 2)?></td>
                                    <td><?= "{$row['purpose']} # {$row['purpose_id']}"?></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                        <?php endforeach;?>
                        <?php endif;?>
                        
                    </tbody>
                    <tfoot class="noScreen">
                        <tr class="_">
                            <td colspan="7"  style="padding-top:15px">Note: This will serve as your statement of account</td>
                        </tr>
                        <tr class="_">
                            <td colspan="7">Make all checks payable to: <span style="text-decoration: underline;">ARDITEZZA POULTRY INTEGRATION CORP.</span></td>
                        </tr>
                        <tr class="_">
                            <td colspan="7" style="padding-top:20px">Prepared by: <b><?= $this->session->userdata('name') ?></b></td>
                        </tr>
                        <tr class="_">
                            <td colspan="7" style="padding-top:20px">Received by: ____________________________________________</td>
                        </tr>
                        <tr class="_">
                            <td colspan="7" style="padding-top:10px;"><small>Printed on: <?= date('M d, Y h:i A')?></small></td>
                        </tr>
                    </tfoot>
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
                <h4 class="modal-title" id="myModalLabel">Select date range</h4>
            </div>
            <form action="<?= current_url()?>" method="GET">
                <div class="modal-body">
                    <div class="form-group">
                       <label>Bank Account</label>
                       <?= form_dropdown('bank_account', $banks_dropdown, $params['bank_account'], 'class="form-control"') ?>
                    </div>
                    <div class="form-group">
                       <label>Bank Account</label>
                       <?= form_input('check_number_start', $params['check_number_start'], 'class="form-control"') ?>
                    </div>
                    <div class="form-group">
                       <label>Bank Account</label>
                       <?= form_input('check_number_end', $params['check_number_end'], 'class="form-control"') ?>
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