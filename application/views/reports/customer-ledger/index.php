<style type="text/css">
    th i{
        margin-left: 10px;
    }
    tbody td{
        padding: 5px;
        border: 1px solid #ddd;
    }
    tbody > tr > td:nth-child(4),td:nth-child(5),td:nth-child(7){
        text-align: right;
    }
    tbody > tr > td:nth-child(2){
        text-align: center;
    }
    thead > tr:nth-child(7) > th{
        padding: 5px;
        border:1px solid #ddd;
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
        padding-bottom:15px;
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
   <div class="box-body table-responsive">
        <div class="row">
            <div class="col-sm-12">
                <table style="width:100%">
                    <thead class="first-thead">
                        <tr class="noScreen">
                            <th colspan="10" class="text-center">
                                <h5 style="margin-bottom:0;font-weight: bold">
                                    ARDITEZZA POULTRY INTEGRATION CORPORATION
                                </h5>
                            </th>
                        </tr>
                        <tr class="noScreen">
                            <th colspan="10" class="text-center font-normal">
                                Ultima Residences Tower 3, Unit 1018, Osmena Blvrd., Cebu City<br>
                                Tel/Fax Nos.: (032) 253-4570 to 71 / 414-3312 / 512-3067
                            </th>
                        </tr>
                        <tr><th colspan="10" class="text-center"><h4 style="margin-bottom:0;text-decoration: underline;font-weight: bold">CUSTOMER LEDGER REPORT</h4></th></tr>
                        <tr><th colspan="10" class="text-center"><?= date_create($params['date'])->format('M d, Y')?> - <?= date('M d, Y')?></th></tr>
                        <tr>
                            <th class="text-right">Customer</th>
                            <th class="colon">:</th>
                            <th colspan="3" class="font-normal">
                                <a data-toggle="modal" data-target="#options">
                                    <?= isset($customer_info['company_name']) ? $customer_info['company_name'] : 'Please select a customer'?>
                                </a>
                            </th>
                            <th colspan="2">&nbsp;</th>
                            <th class="text-right">Credit Terms <span style="padding-left:4px; padding-right:6px;">:</span></th>
                            <th class="font-normal">
                                <?php if(isset($customer_info['credit_term'])):?>
                                    <?= (int)$customer_info['credit_term'] ? "{$customer_info['credit_term']} days(s)" : 'Cash on delivery'?>
                                <?php endif;?>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-right">Customer Code</th>
                            <th class="colon">:</th>
                            <th colspan="3" class="font-normal">
                                <?= isset($customer_info['customer_code']) ? $customer_info['customer_code'] : ''?>
                            </th>
                            <th colspan="2">&nbsp;</th>
                            <th class="text-right">Credit Limit <span style="padding-left:4px; padding-right:6px;">:</span></th>
                            <th class="font-normal">
                                <?= isset($customer_info['credit_limit']) ? number_format($customer_info['credit_limit'], 2) : ''?>
                            </th>
                        </tr>
                        <tr class="active">
                            <th colspan="2">DATE</th>
                            <th >DESCRIPTION</th>
                            <th>REF NO.</th>
                            <th>DEBIT AMOUNT</th> 
                            <th colspan="2">CREDIT AMOUNT</th>
                            <th>CLEAR DATE</th>
                            <th>RUNNING BALANCE</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php if(!isset($data)):?>
                            <tr><td colspan="7" class="text-center">Choose a customer and date to start</td></tr>
                        <?php else:?>
                            <?php 
                                
                                $amount_balance = $data['balance']; 
                                $sr_url = base_url('sales/receipts/update/');
                                $pl_url = base_url('sales/deliveries/update/')
                            ?>
                            <?php $counter=0; ?>
                            <?php foreach($data['ledger'] AS $row):?>
                                <?php 
                                    $now = date_create();
                                    $debit_amount = '';
                                    $credit_amount = '';
                                    $pdc = '';
                                    $note = '';
                                    if($row['description'] === 'PL'){
                                        $debit_amount =  number_format($row['amount'], 2);
                                        $amount_balance += $row['amount'];
                                        $url = "{$pl_url}/{$row['id']}";
                                    }else{
                                        $credit_amount =  number_format($row['amount'], 2);
                                        if($row['description'] === 'SR'){
                                            if($row['pdc'] <= 0){
                                                $amount_balance -= $row['amount'];
                                            }else{
                                                $pdc = 'style="background:#fcf8e3"';
                                                $clear_date = $now->modify("{$row['pdc']} days")->format('M d, Y');
                                                $note = $clear_date;
                                                $clear_date = $now->modify("{$row['pdc']} day")->format('M d, Y');
                                                $note = $clear_date;
                                            }
                                            $url = "{$sr_url}/{$row['id']}";
                                        }else{
                                            $amount_balance -= $row['amount'];
                                            $url = "{$pl_url}/{$row['id']}";
                                        }
                                    }
                                ?>
                                <tr <?= $pdc?> data-rownum="<?= ++$counter ?>">
                                    <td colspan="2"><?= date_create($row['date'])->format('M d, Y')?></td>
                                    <td>
                                        <a href="<?= $url?>" target="_blank">
                                            <?= "{$row['description']}# {$row['id']}"?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if($row['description'] === 'PL' && is_numeric($row['ref_number'])):?>
                                            <?= "SI # {$row['ref_number']}"?>
                                        <?php elseif($row['description'] === 'SR' && is_numeric($row['ref_number'])):?>
                                             <?= "CR # {$row['ref_number']}"?>
                                        <?php endif;?>
                                    </td>
                                    <td colspan="2">
                                        <?= $debit_amount;?>
                                    </td>
                                    <td>
                                        <?= $credit_amount ?> 
                                    </td >
                                    <td style="text-align: right">
                                        <?= $note ?>
                                    </td>
                                    <td ><?= number_format($amount_balance, 2)?></td>
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
                       <label>Start date</label>
                       <input type="text" class="form-control datepicker"  name="date" value="<?= is_valid_date($params['date']) ? $params['date']: ''?>"/>
                    </div>
                    <div class="form-group">
                       <label>Customer</label>
                        <?= form_dropdown('customer', $customers, $params['customer'], 'class="form-control"')?>
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