<input type='hidden' name='data-url-for-printing' value='<?= base_url('warehousing/product_receiving/generate') ?>'>
<style type='text/css'>tbody tr td{vertical-align: middle!important;}</style>
<div class="row">
    <div class="col-md-8">
        <div class='btn-toolbar'>
            <a role='button' href='<?= base_url('warehousing/product_receiving/manage/0') ?>' class='btn btn-success'>
                <i class='fa fa-plus'></i> Add new product receiving 
            </a>
        </div>
        <div class="box box-info">
            <div class="box-body no-padding">
                <table class='table table-hover' >
                    <tbody data-action-url='<?= base_url('warehousing/product_receiving/a_do_receive') ?>'>
                        <tr><th>ID</th><th>Date & Time</th><th>Remarks</th><th>Products</th><th>Action(s)</th></tr>
                            <?php $hide_notification = 'hidden'; 
                                if($product_receiving){
                            ?>
                            <?php foreach ($product_receiving as $item): ?>
                                <tr data-pk='<?= $item['id'] ?>'>
                                    <td><a target="_blank" href='<?= base_url("purchases/receiving/manage?do=update-purchase-receiving&id={$item['id']}") ?>'><?= $item['ID'] ?></a></td>
                                    <td><?= $item['datetime'] ?></td>
                                    <td><?= $item['remarks'] ?></td>
                                    <td>
                                        <?php 
                                            if($item["products"] && isset($item["products"])){ 
                                                foreach($item["products"] as $products){
                                        ?>
                                            <?=$products["description"]." (".$products["quantity"]." ".$products["unit_description"].")"?><br>
                                        <?php 
                                                }
                                            }
                                        ?>
                                    </td>
                                    
                                    <td>
                                        <a href='javascript:void(0)' data-pk='<?= $item['ID'] ?>' title='Print' role='button' class='print-doc'>
                                            <span class='badge bg-teal'><i class='fa fa-print'></i></span>
                                        </a>
                                    </td>
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