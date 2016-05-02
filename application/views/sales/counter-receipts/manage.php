<?php $url = base_url('sales/counter_receipts'); ?>
<div class="box box-solid">
    <div class="box-header bg-light-blue-gradient" style="color:#fff">
        <h3 class="box-title"><?= $title ?></h3>
    </div><!-- /.box-header -->

    <form data-action="<?= $action ?>">
        <div class="box-body">
             <div class="callout callout-danger hidden">
                <ul class="list-unstyled"></ul>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="customer-name">Customer</label>
                        <?= form_dropdown('fk_sales_customer_id', ['' => 'Select customer'] + array_column($customers, 'company_name', 'id'), put_value($data, 'fk_sales_customer_id', ''), 'class="form-control"') ?>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="invoice-number">SI#</label>
                        <?= form_input(['name' => 'invoice_number', 'class' => 'form-control', 'value' => put_value($data, 'invoice_number', '')]); ?>
                    </div>
                </div>
                <div class="col-sm-3 col-sm-offset-2">
                    <div class="form-group">
                        <label for="date">Date</label>
                        <?= 
                            form_input([
                                'name' => 'date', 
                                'class' => 'form-control datepicker', 
                                'value' => date_create(put_value($data, 'date', NULL))->format('M-d-Y')
                            ]); 
                        ?>
                    </div>  
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="date">Remarks</label>
                        <?= form_textarea(['name' => 'remarks', 'class' => 'form-control', 'rows' => 3, 'resizable-x' => 'none', 'value' => put_value($data, 'remarks', '')]); ?>
                    </div>  
                </div>
            </div>
            <hr>
            <table class="table table-bordered" id="cr-table" style="border-bottom: none;border-left: none;border-right: none;">
                <thead><tr class="info"><th><label><input type="checkbox" id="toggle-all"/> PL #</label></th><th>SI #</th><th>PL Date</th><th>Amount</th></tr></thead>
                <tbody>
                    <?php $total = 0;?>
                    <?php if (isset($uncountered_packing_lists)): ?>
                        <?php $selected_packing_lists = array_column($data['details'], NULL, 'fk_sales_delivery_id'); ?>
                        <?php foreach($uncountered_packing_lists AS $x => $pl):?>
                            <?php 
                                if(isset($selected_packing_lists[$pl['id']])){
                                    $checked = 'checked="checked"';
                                    $total += $pl['amount'];
                                }else{
                                    $checked = '';
                                }
                            ?>
                            <tr>
                                <td>
                                    <?php if($checked):?>
                                        <input type="hidden" name="details[<?= $x?>][id]" value="<?= $selected_packing_lists[$pl['id']]['id']?>">
                                    <?php endif;?>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="select-pl" name="details[<?= $x?>][fk_sales_delivery_id]" value="<?= $pl['id']?>" <?= $checked ?>/> PL# <?= $pl['id']?>
                                        </label>
                                        <a target="_blank" style="margin-left:10px" href="<?= base_url("sales/deliveries/update/{$pl['id']}")?>">
                                            <i class="fa fa-link"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <?= $pl['invoice_number']?>
                                </td>
                                <td>
                                    <?= date_create($pl['date'])->format('M-d-Y')?>
                                </td>
                                <td class="text-right pl-amount" data-pl-amount="<?= $pl['amount']?>">
                                    <?= number_format($pl['amount'], 2)?>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">Please select a customer.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot><tr><td class="no-border" colspan="2"></td><td class="info">Total Amount</td><td class="info total-amount text-right"><?= number_format($total, 2)?></td></tr></tfoot>
            </table>
            <?php if(can_set_status()):?>
                <div class="checkbox">
                    <?php $approved = put_value($data, 'approved_by', FALSE) ? 'checked="checked"' :''?>
                    <label> <input type="checkbox" name="is_approved" <?= $approved?>/> Mark this counter receipt as <b>approved</b></label>
                </div>
            <?php endif;?>
        </div><!-- /.box-body -->  

        <div class="box-body">
            <button class="btn btn-flat btn-success <?= can_update($data) ? '' : 'disabled'?>">Submit</button>
            <a class="btn btn-flat btn-warning" id="cancel" href="<?= $url?>">Cancel</a>
        </div><!-- /.box-footer -->  
    </form>
</div>


<script type="text/javascript" src="<?= base_url('assets/js/numeral.js')?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/plugins/moment.min.js')?>"></script>
<script type="text/javascript">
    $(document).ready(function(){

        var totalAmount = <?= $total?>;
        var messageBox = $('.callout.callout-danger');

        $('.datepicker').datepicker({dateFormat: 'M-dd-yy'});

        $('select[name=fk_sales_customer_id]').change(function(){
            var customerId = $(this).val();
            
            $.getJSON('<?= base_url('sales/customer/get_uncountered_packing_list')?>/'+customerId)
            .done(function(response){
                var dataLength = response.data.length,
                    tableBody = '';
                for(x = 0; x<dataLength; x++){
                    var pl = response.data[x];
                    tableBody += '<tr><td><div class="checkbox"><label><input type="checkbox" class="select-pl" name="details['+x+'][fk_sales_delivery_id]" value="'+pl.id+'" /> PL# '+pl.id+'</label><a target="_blank" style="margin-left:10px" href="<?= base_url('sales/deliveries/update')?>/'+pl.id+'"><i class="fa fa-link"></i></a></div></td><td >'+pl.invoice_number+'</td><td>'+moment(pl.date).format('MMM-DD-YYYY')+'</td><td class="text-right pl-amount" data-pl-amount="'+pl.amount+'">'+numeral(pl.amount).format('0,0.00')+'</td></tr>'
                }
                $('#cr-table tbody').html(tableBody)
            })
            .fail(function(){
                alert('An unknown error has occured. Please try again later.');
            }) 
        });

         $('#cr-table').on('change', '.select-pl', function(){
            console.log('asd')
            var that = $(this),
                plAmount = parseFloat(that.closest('tr').find('.pl-amount').data('pl-amount'));
            if(that.prop('checked')){
                totalAmount += plAmount;
                $(this).closest('tr').find('input[type=hidden]').removeAttr('disabled');
            }else{
                totalAmount -= plAmount;
                $(this).closest('tr').find('input[type=hidden]').attr('disabled', 'disabled');
            }
            
            $('.total-amount').text(numeral(totalAmount).format('0,0.00'));
        });

         $('#toggle-all').change(function(){
            $('#cr-table tbody input[type=checkbox]').prop('checked', $(this).prop('checked'));
         });



        $('form').submit(function(e){

            e.preventDefault();

            var that = $(this);

            messageBox.addClass('hidden');

            $('[type=submit]').attr('disabled', 'disabled');

            $.post(that.data('action'), that.serialize())

            .done(function(response){
                if(response.error_flag){
                    messageBox.removeClass('hidden').find('ul').html('<li>'+response.message.join('</li><li>')+'</li>');
                    $('html, body').animate({scrollTop: 0}, 'slow');
                    return;
                }
                window.location.href = $('#cancel').attr('href');
            })
            .fail(function(){
                alert('An internal error has occured. Please try again in a few moment.');
            })
            .always(function(){
                $('[type=submit]').removeAttr('disabled');
            });
        });
    });
</script>