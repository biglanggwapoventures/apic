<style type="text/css">
    th i{
        margin-left: 10px;
    }
    tbody td{
        padding: 5px;
        border: 1px solid black;
    }
    tbody > tr > td:nth-child(7){
        text-align: right;
    }
    thead > tr:nth-child(3) > th{
        padding: 5px;
        font-weight: bold;
        border:1px solid black;
        text-transform:uppercase;
    }
    thead th{
        background: white;
    }
    @media print {
      a[href]:after {
        content: none !important;
      }
    }
    tfoot td:not(:first-child){
        border: 1px solid black;
        padding: 6px;
    }
</style>
<div class="box box-solid">
   <div class="box-body table-responsive">
        <div class="row">
            <div class="col-sm-12">
                <table class="table-striped" style="width:100%">
                    <thead>
                        <tr><th colspan="8" class="text-center"><h4 style="margin-bottom:0">SALES AGENT INCENTIVES</h4></th></tr>
                        <tr>
                            <th colspan="8" class="text-center text-primary" style="padding-bottom:20px">
                                <a data-toggle="modal" data-target="#select-date">
                                <?= "{$params['start_date']} - {$params['end_date']}"?>
                                </a>
                            </th>
                        </tr>
                        
                        <tr class="active">
                            <th>SR#</th><th>DEPOSIT DATE</th><th>CUSTOMER</th> <th>PL#</th> <th>PL DATE</th>  <th>DAYS COLLECTED</th><th>AMOUNT</th>
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
                                <td><?= $row['days_collected']?> day(s)</td>
                                <td><?= number_format($row['amount'], 2)?></td>
                                
                            </tr>   
                        <?php endforeach;?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"></td>
                            <td colspan="4" class="text-center text-bold" style="background:#eee"><span style="font-size:110%">SUMMARY</span></td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td colspan="2"><span style="font-size:110%">TOTAL COLLECTION</span></td>
                            <td class="active text-bold text-center " colspan="2"><span style="font-size:110%"><?= number_format($total, 2)?></span></td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td colspan="2"><span style="font-size:110%">TOTAL QUALIFIED COLLECTION</span></td>
                            <td class="active text-center text-bold" colspan="2"><span style="font-size:110%"><?= number_format($total_qualified, 2)?></span></td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td colspan="2"><span style="font-size:110%">TOTAL INCENTIVE</span></td>
                            <td class="active text-center text-bold" colspan="2">
                                <span style="font-size:110%">
                                    <?= isset($params['sales_agent']) ? number_format($total_qualified * $params['sales_agent']['commission_rate'], 2) : 0 ?>
                                </span>
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
<script type="text/javascript">
    (function($){
        $(document).ready(function(){
            $('table').stickyTableHeaders({fixedOffset: $('.content-header')});
            $('.datepicker').datepicker({dateFormat:'yy-mm-dd'});
            
        })
    })(jQuery)
</script>