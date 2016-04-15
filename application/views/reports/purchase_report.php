<style type='text/css'>tbody tr td{vertical-align: middle!important;}</style>
<input type='hidden' name='data-url-for-printing' value='<?= base_url('reports/purchase_report/generate') ?>'>
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-body no-padding">
                <table class='table table-hover' >
                    <tbody data-action-url='<?= base_url('production/job_orders/a_do_order') ?>'>
                        <tr><th>Date</th><th>DR#</th><th>Customer</th><th>Check Number</th><th>Bank/Branch</th><th>Check Date</th><th>Amount</th></tr>
                            <?php 
                                $hide_notification = 'hidden'; 
                                if($purchase_receiving_list){
                            ?>
                            <?php foreach ($purchase_receiving_list as $item): ?>
                                <tr data-pk='<?= $item['id'] ?>'>
                                    <td><?= $item['date'] ?></td>
                                    <td><?= $item['id'] ?></td>
                                    <td><?= $item['supplier'] ?></td>
                                    <?php 
                                    
                                    ?>
                                    <td>
                                    <?php
                                    if(count($item["payment_detail"]) > 0){
                                        foreach($item["payment_detail"] as $payment){
                                            if(count($payment["check_list"]) > 0){
                                                foreach($payment['check_list'] as $check){
                                                    echo $check["check_number"]."<br>";
                                                }
                                            }else{
                                            //    echo "Cash<br>";
                                            }
                                        }
                                    }
                                    ?>
                                    </td>
                                    <td>
                                    <?php
                                    if(count($item["payment_detail"]) > 0){
                                        foreach($item["payment_detail"] as $payment){
                                            if(count($payment["check_list"]) > 0){
                                                foreach($payment['check_list'] as $check){
                                                    echo $check["bank_name"]." / ".$check["bank_branch"]."<br>";
                                                }
                                            }else{
                                             //   echo "Cash<br>";
                                            }
                                        }
                                    }
                                    ?>
                                    </td>
                                    <td>
                                    <?php
                                    if(count($item["payment_detail"]) > 0){
                                        foreach($item["payment_detail"] as $payment){
                                            if(count($payment["check_list"]) > 0){
                                                foreach($payment['check_list'] as $check){
                                                    echo $check["check_date"]."<br>";
                                                }
                                            }else{
                                             //   echo "Cash<br>";
                                            }
                                        }
                                    }
                                    ?>
                                    
                                    </td>
                                    <?php
                                    ?>
                                    <td style="vertical-align: top!important;"><?= $item['total_amount'] ?></td>
                                <tr>
                                <?php endforeach; 
                                    }else{
                                ?>
                                    <tr> <td class='text-center <?=$hide_notification?> table-notification' colspan='5'>There are currently no items to receive</td></tr>
                                    <?php }?>
                                <?php $hide_notification = ''; ?>
                        
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div>
    </div>
</div>