<style type="text/css">
    th i{
        margin-left: 10px;
    }
    tbody td{
        padding: 3px;
        border: 1px solid black;
    }
    tbody > tr > td:nth-child(6){
        text-align: right;
    }
    tbody > tr > td:nth-child(7){
        text-align: center;
    }
    thead > tr:nth-child(5) > th{
        padding: 3px;
        font-weight: bold;
        border:1px solid black;
        text-transform:uppercase;
        text-align: center;
    }
    thead > tr:nth-child(5) > th{
        padding-bottom: 5px;
    }
    thead th{
        background: white;
    }
    @media screen {
        .noPrint{}
        .noScreen{display:none;}
    }
    @media print {
      a[href]:after {
        content: none !important;
      }
      .noPrint{display:none;}
      .noScreen{}
      table {page-break-after:always;}
    }
    tfoot td:not(:first-child){
        border: 1px solid black;
        padding: 3px;
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
                <table class="table-striped" style="width:100%">
                    <thead class="first-thead">
                        <tr class="noScreen">
                            <th colspan="10" class="text-center font-normal">
                                ARDITEZZA POULTRY INTEGRATION CORPORATION<br>
                                <div style="font-weight:normal">
                                    Ultima Residences Tower 3, Unit 1018, Osmena Blvrd., Cebu City<br>
                                    Tel/Fax Nos.: (032) 253-4570 to 71 / 414-3312 / 512-3067
                                </div>
                            </th>
                        </tr>
                        <tr><th colspan="8" class="text-center"><h4 style="margin-bottom:0">SALES AGENT INCENTIVES</h4></th></tr>
                        <tr>
                            <th colspan="8" class="text-center text-primary" style="padding-bottom:10px">
                                <a data-toggle="modal" data-target="#select-date">
                                <?= "{$params['start_date']} - {$params['end_date']}"?>
                                </a>
                            </th>
                        </tr>
                        <tr id="adjust-font-size">
                            <th class="text-right"><span>Sales Agent : </span></th>
                            <th class="text-center">
                                <a  data-toggle="modal" data-target="#select-date" >
                                    <span><?= isset($params['sales_agent']) ? $params['sales_agent']['name'] : '' ?></span>
                                </a>
                            </th>
                            <th colspan="3"></th>
                            <th class="text-right"><span>Commission Rate : </span></th>
                            <th class="text-center">
                               <span>
                                    <?= isset($params['sales_agent']) ? $params['sales_agent']['commission_rate'] : '' ?>
                                </span>
                            </th>
                        </tr>
                        <tr class="active">
                            <th><span>SR#</span></th><th><span>DEPOSIT DATE</span></th><th><span>CUSTOMER</span></th><th><span>PL#</span></th><th><span>PL DATE</span></th><th><span>AMOUNT</span></th><th><span>DAYS COLLECTED</span></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!isset($params['sales_agent'])):?>
                        <tr><td colspan="7" class="text-center">Please select a sales agent to start</td></tr>
                    <?php endif;?>
                        <?php 
                            $qualified_days_threshold = 60;
                            $total = 0;
                            $total_qualified = 0;
                            $sr_url = base_url('sales/receipts/update');
                            $pl_url = base_url('sales/deliveries/update');
                        ?>
                        <?php foreach($data AS $row):?>
                            <?php 
                                $total += $row['amount'];
                                if($row['days_collected'] <= 60){
                                    $total_qualified += $row['amount'];
                                }
                            ?>
                            <tr>
                                <td><a target="_blank" href="<?= "{$sr_url}/{$row['id']}"?>">SR# <?= $row['id']?></a></td>
                                <td><?= date_create($row['deposit_date'])->format('M d, Y')?></td>
                                <td><?= $row['customer_name']?></td>
                                <td><a target="_blank" href="<?= "{$pl_url}/{$row['fk_sales_delivery_id']}"?>">PL# <?= $row['fk_sales_delivery_id']?></a></td>
                                <td><?= date_create($row['delivery_date'])->format('M d, Y')?></td>
                                 <td><?= number_format($row['amount'], 2)?></td>
                                <td><?= $row['days_collected']?> day(s)</td>
                            </tr>   
                        <?php endforeach;?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"></td>
                            <td colspan="4" class="text-center text-bold" style="background:#eee">SUMMARY</td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td colspan="2">TOTAL COLLECTION</td>
                            <td class="active text-bold text-center " colspan="2"><span><?= number_format($total, 2)?></span></td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td colspan="2">TOTAL QUALIFIED COLLECTION</td>
                            <td class="active text-center text-bold" colspan="2"><span><?= number_format($total_qualified, 2)?></span></td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td colspan="2">TOTAL INCENTIVE</td>
                            <td class="active text-center text-bold" colspan="2">
                                    <?= isset($params['sales_agent']) ? number_format($total_qualified * $params['sales_agent']['commission_rate'], 2) : '0.00' ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <span class="">Time elapsed: <?= $this->benchmark->elapsed_time();?>s</span>
            </div>
        </div>
   </div>
</div>

<div class="modal fade" id="select-date" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Select date range</h4>
            </div>
            <form action="<?= current_url()?>" method="GET">
                <div class="modal-body">
                    <div class="form-group">
                       <label>Sales agent</label>
                        <?= form_dropdown('sales_agent', $agents, $this->input->get('sales_agent'), 'class="form-control"')?>
                    </div>
                    <div class="form-group">
                       <label>Start date</label>
                       <input type="text" class="form-control datepicker"  name="start_date" value="<?= is_valid_date($params['start_date'], 'M d, Y') ? date_create($params['start_date'])->format('Y-m-d'): ''?>"/>
                    </div>
                    <div class="form-group">
                       <label>End date</label>
                       <input type="text" class="form-control datepicker" name="end_date" value="<?= is_valid_date($params['end_date'], 'M d, Y') ? date_create($params['end_date'])->format('Y-m-d'): ''?>"/>
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
<script type="text/javascript">
    (function($){
        $(document).ready(function(){
            $('table').stickyTableHeaders({fixedOffset: $('.content-header')});
            $('.datepicker').datepicker({dateFormat:'yy-mm-dd'});
            
        })
    })(jQuery)
</script>