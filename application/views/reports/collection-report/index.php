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
        font-weight: normal;
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
</style>
<div class="box box-solid">
   <div class="box-body table-responsive">
        <div class="row">
            <div class="col-sm-12">
                <table class="table-striped" style="width:100%">
                    <thead>
                        <tr><th colspan="8" class="text-center"><h4 style="margin-bottom:0">COLLECTION REPORT</h4></th></tr>
                        <tr>
                            <th colspan="8" class="text-center text-primary" style="padding-bottom:20px">
                                <a data-toggle="modal" data-target="#select-date">
                                <?= "<i class=\"fa fa-clock-o\"></i> {$params['start_date']} - {$params['end_date']} <i class=\"fa fa-shopping-cart\"></i> Customer: {$params['customer']} <i class=\"fa fa-user\"></i> Sales Agent: {$params['sales_agent']}"?>
                                </a>
                            </th>
                        </tr>
                        
                        <tr class="active">
                            <th>SR#</th><th>AGENT</th><th>DEPOSIT<br>DATE</th><th>CUSTOMER</th> <th>PL#</th> <th>PL DATE</th> <th>AMOUNT</th> <th>DAYS COLLECTED</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $total = 0;
                            $sr_url = base_url('sales/receipts/update');
                            $pl_url = base_url('sales/deliveries/update');
                        ?>
                        <?php foreach($data AS $row):?>
                            <?php $total += $row['amount'] ?>
                            <tr>
                                <td><a target="_blank" href="<?= "{$sr_url}/{$row['id']}"?>">SR# <?= $row['id']?></a></td>
                                <td>
                                    <a class="editable" data-pk="<?= $row['so_id']?>"><?= $row['sales_agent'] ?: (TOGGLE_SALES_AGENT ? '<em class="text-warning"><small>None</em></small>': '')?></a>
                                </td>
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
                        <tr><td colspan="5"></td><td class="text-center active ">TOTAL AMOUNT</td><td class="text-right active text-bold"><?= number_format($total, 2)?></td></tr>
                    </tfoot>
                </table>
                Time elapsed: <?= $this->benchmark->elapsed_time();?>s
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
                       <label>Start date</label>
                       <input type="text" class="form-control datepicker"  name="start_date" value="<?= is_valid_date($params['start_date'], 'M d, Y') ? date_create($params['start_date'])->format('Y-m-d'): ''?>"/>
                    </div>
                    <div class="form-group">
                       <label>End date</label>
                       <input type="text" class="form-control datepicker" name="end_date" value="<?= is_valid_date($params['end_date'], 'M d, Y') ? date_create($params['end_date'])->format('Y-m-d'): ''?>"/>
                    </div>
                    <div class="form-group">
                       <label>Customer</label>
                        <?= form_dropdown('customer', $customers, $this->input->get('customer'), 'class="form-control"')?>
                    </div>
                    <div class="form-group">
                       <label>Sales agent</label>
                        <?= form_dropdown('sales_agent', $agents, $this->input->get('sales_agent'), 'class="form-control"')?>
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

            var agentSrc = [];
            $('[name=sales_agent] option').each(function(){
                var id = $(this).attr('value'),
                    text = $(this).text();
                if(id){
                    agentSrc.push({
                        value: id,
                        text: text
                    });
                }
            });

            $('table').stickyTableHeaders({fixedOffset: $('.content-header')});
            $('.datepicker').datepicker({dateFormat:'yy-mm-dd'});
            $('.editable').editable({
                type: 'select',
                source: agentSrc,
                url: "<?= base_url('sales/orders/set_agent')?>",
                success: function(response){
                    if(response.error_flag){
                        return response.message[0];
                    }
                },
                placement:'right'
            });
        })
    })(jQuery)
</script>