<style type="text/css">
    div.t{
        border-bottom:1px solid black;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff">
                <h3 class="box-title"><?= isset($form_title) ? $form_title : '' ?></h3>
            </div><!-- /.box-header -->
            <?= form_open($action, array('role' => 'form')); ?>
            <div class="box-body">
                <div class="callout callout-danger hidden">
                    <h4>Error!</h4>
                    <ul class="list-unstyled"></ul>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="customer-name">Supplier</label>
                            <?php if ($defaults['fk_maintainable_supplier_id']): ?>
                                <input type="hidden" name="fk_maintainable_supplier_id" value="<?= $defaults['fk_maintainable_supplier_id'] ?>"/>
                                <p class="form-control-static"><?= $defaults['supplier'] ?></p>
                            <?php else: ?>
                                <?php $attr = 'class="form-control" id="supplier-name" required="required"'; ?>
                                <?= generate_dropdown('fk_maintainable_supplier_id', $suppliers, FALSE, $attr, 'supplier') ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-3">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <?= form_input(array('name' => 'date', 'class' => 'form-control datepicker', 'id' => 'date', 'required' => 'required', 'value' => $defaults['date'])); ?>
                        </div>  
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="date">Remarks</label>
                            <?= form_textarea(array('name' => 'remarks', 'class' => 'form-control', 'id' => 'remarks', 'rows' => 3, 'resizable-x' => 'none', 'value' => $defaults['remarks'])); ?>
                        </div>  
                    </div>
                </div>
                <div class="row" style="margin-top:20px;">
                    <div class="col-md-12 table-responsive">
                        <fieldset>
                            <legend>Product List</legend>
                            <table class="table table-condensed table-bordered" id="order-line" style="border-bottom:0;border-left:0;border-right:0;">
                                <thead>
                                    <tr class="active">
                                        <th style="width:25%">Product</th>
                                        <th style="width:7%">Units</th>
                                        <th style="width:20%">Qty Ordered</th>
                                        <th style="width:13%">Qty Received</th>
                                        <th style="width:20%">Unit Price</th>
                                        <th style="width:20%">Amount</th>
                                        <th style="width:5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $product_total = 0;?>
                                <?php $disabled = $defaults['type'] && $defaults['type'] === 'lcl' ? 'disabled="disabled"': ''?>
                                    <?php $data_attribute = ['attr' => ['name' => 'unit', 'value' => 'unit_description'], 'text' => 'description'];?>
                                    <?php foreach ($defaults['details'] as $index => $detail): ?>
                                        <tr>
                                            <td>
                                                <?php if (isset($detail['id'])): ?>
                                                    <input type="hidden" name="details[id][]" class="do-remove" value="<?= $detail['id'] ?>">
                                                <?php endif; ?>
                                                <?php if (isset($products)): ?>
                                                    <?= arr_group_dropdown('details[fk_inventory_product_id][]', $products, 'p_id', $data_attribute, $detail['fk_inventory_product_id'], FALSE, 'class="form-control product-listing"') ?>
                                                <?php else: ?>
                                                    <select class="hidden"></select>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="t">
                                                    <span class="contains-unit needs-reset">
                                                        <?= $detail['unit_description'] ?>
                                                    </span>
                                                </div>
                                                pcs
                                            </td>
                                            <td>
                                                <div class="t">
                                                    <input name="details[quantity][]" value="<?= $detail['quantity'] ?>" type="number" class="form-control needs-reset has-quantity do-calculation" step="0.01" required>
                                                </div>
                                                <input name="details[pieces][]" value="<?= $detail['pieces'] ?>" type="number" class="form-control needs-reset has-quantity" step="0.01" required>
                                            </td>
                                            <td class="text-right">
                                                <div class="t">
                                                    <span class="">
                                                        <?= $detail['quantity_received'] ?>
                                                    </span>
                                                </div>
                                                <?= isset($detail['received_pieces']) ? number_format($detail['received_pieces'], 2) : '0.00' ?>
                                            </td>
                                            
                                            <td><input name="details[unit_price][]" value="<?= $detail['unit_price'] ?>" type="text" class="form-control has-amount needs-reset do-calculation"></td>
                                            <td class="contains-line-total text-right">
                                                <?=$detail['amount']?>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm remove-line btn-flat"><i class="fa fa-times"></i></button>
                                            </td>
                                        </tr>
                                        <?php if($defaults['type'] === 'imt'):?>
                                            <?php $product_total+=$detail['amount'];?>
                                        <?php endif;?>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot style="border:0">
                                    <?php if($defaults['type'] === 'lcl'):?>
                                        <?php $product_total = $defaults['total_amount']?>
                                    <?php endif;?>
                                    <tr style="border:0">
                                        <td colspan="3" style="border:0">
                                            <a href="javascript:void(0)" class="add-line btn btn-default btn-flat btn-sm <?= $defaults['type'] === 'imt' ? 'hidden' : ''?>"> <i class="glyphicon glyphicon-plus"></i> Add new line</a>
                                        </td>
                                        <td style="border:0" colspan="2"></td>
                                        <td class="active text-right">
                                            <span class="contains-total-amount text-bold" style="font-size:110%"><?= number_format($product_total, 2) ?></span>
                                        </td>
                                        <td style="border:0"></td>
                                        </tr>
                                </tfoot>
                            </table>
                        </fieldset>
                    </div>
                </div>
                <div class="row <?= $disabled ? 'hidden' :'' ?> shown-on-import" style="margin-top:20px;">
                    <div class="col-md-12 table-responsive">
                        <fieldset>
                            <legend>Other Fees</legend>
                            <table class="table" id="other-fees">
                                <thead><tr class="active"><th>Description</th><th>Amount</th><th style="width:5%"></th></tr></thead>
                                
                                <tbody>
                                    <?php $fees = isset($other_fees) && $other_fees ? $other_fees : [[]]; ?>
                                    <?php $fees_total = 0;?>
                                    <?php foreach($fees AS $fee):?>
                                        <tr>
                                            <?php $fees_total+=isset($fee['amount']) ? $fee['amount'] : 0;?>
                                            <td><input value="<?= isset($fee['description']) ? $fee['description'] : ''?>" type="text" class="form-control" name="others[desc][]" required="required" <?=$disabled?>/></td>
                                            <td><input value="<?= isset($fee['amount']) ? number_format($fee['amount'], 2) : ''?>" type="text" class="form-control has-amount" name="others[amount][]"  required="required" <?=$disabled?>/></td>
                                            <td><a class="btn btn-sm btn-flat btn-danger remove-line"><i class="fa fa-times"></i></a></td>
                                        </tr>
                                    <?php endforeach;?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>
                                            <a href="javascript:void(0)" class="add-fees btn btn-default btn-flat btn-sm"> <i class="glyphicon glyphicon-plus"></i> Add new line</a>
                                        </td>
                                        <td class="active clearfix">
                                            <span class="pull-right contains-others-total text-bold" style="font-size:110%">
                                            <?= number_format($fees_total, 2)?></span>
                                        </td>
                                        <td class="active"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </fieldset>
                    </div>
                </div>
                <div class="row hidden">
                    <div class="col-md-6 col-md-offset-6">
                        <p class="text-center bg-red" style="margin-top:30px;padding:5px">
                            Total amount: <strong style="font-size:110%"><?= number_format($product_total + $fees_total, 2)?></strong>
                        </p>
                    </div>
                </div>
                <div class="row <?= $disabled ? 'hidden' :'' ?> shown-on-import" style="margin-top:20px;">
                    <div class="col-md-12 ">
                        <fieldset>
                            <legend>Issued Checks</legend>
                            <?php $checks = isset($issued_checks) && $issued_checks ? $issued_checks : [[]]; ?>
                            <table class="table" id="issued-checks">
                                <thead><tr class="active"><th>Check type</th><th>Account</th><th>Check #</th><th>Check Date</th><th >Remarks</th><th >Amount</th><th></th></tr></thead>
                                <tfoot>
                                    <tr><td colspan="7"><a href="javascript:void(0)" class="add-check btn btn-default btn-flat btn-sm"> <i class="glyphicon glyphicon-plus"></i> Add new line</a></td></tr>
                                </tfoot>
                                <tbody>
                                    
                                    <?php foreach($checks AS $c):?>
                                        <tr>
                                            <td><?= select_cheque('check[check_type][]', isset($c['check_type']) ? $c['check_type'] : FALSE, 'class="form-control"')?></td>
                                            <td><?= arr_group_dropdown('check[account][]', $accounts, 'id', 'bank_name', isset($c['bank_account']) ? $c['bank_account'] : FALSE, FALSE, 'class="form-control" required="required" '.$disabled)?></td>
                                            <td><input value="<?= isset($c['check_number']) ? $c['check_number'] : ''?>"  type="text" class="form-control" name="check[check_number][]" <?=$disabled?> required="required"/></td>
                                            <td><input value="<?= isset($c['check_date']) ? $c['check_date'] : ''?>"  type="text" class="form-control datepicker" name="check[check_date][]" <?=$disabled?> required="required"/></td>
                                            <td><input value="<?= isset($c['remarks']) ? $c['remarks'] : ''?>" type="text" class="form-control" name="check[remarks][]" <?=$disabled?>/></td>
                                            <td><input value="<?= isset($c['amount']) ? number_format($c['amount'], 2) : ''?>"  type="text" class="form-control has-amount" name="check[amount][]" <?=$disabled?> required="required"/></td>
                                            <td>
                                                <?php if(isset($c['id'])):?>
                                                    <a data-toggle="modal" data-target="#check-options" data-payment-id="<?= $c['id'] ?>" class="btn btn-sm btn-flat btn-primary print-voucher"><i class="fa fa-cog"></i></a>
                                                <?php endif;?>
                                                <a class="btn btn-sm btn-flat btn-danger remove-line"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </fieldset>
                    </div>
                </div>
                <?php if ((int) $this->session->userdata('type_id') === (int) M_Account::TYPE_ADMIN): ?>
                    <hr>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="force_close" value="1"
                                   <?= $defaults['force_close'] == 1 ? "checked" : "" ?>/>
                            Mark this purchase order as <b>closed</b>
                        </label>
                    </div>
                <?php endif; ?>
            </div>
            <div class="box-footer clearfix">
                <?php if ((int) $defaults['status'] !== (int) M_Status::STATUS_APPROVED || (int) $this->session->userdata('type_id') === (int) M_Account::TYPE_ADMIN || !$is_locked): ?>
                    <?= form_button(array('type' => 'submit', 'class' => 'btn btn-success btn-flat', 'content' => 'Save')); ?>
                <?php endif; ?>
                <a href="<?= base_url('purchases/orders') ?>" class="btn btn-warning pull-right btn-flat">Go back</a>
            </div>
            <?= form_close(); ?>
        </div>

    </div>
</div>
<div class="modal fade" id="check-options" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mySmallModalLabel">Options</h4>
            </div>
            <form id="advanced-search">
                <div class="modal-body">
                    <a data-url="<?= base_url('purchases/orders/ajax_print_voucher')?>/" class="btn btn-default btn-flat btn-block print">Print voucher</a>
                    <a data-url="<?= base_url('purchases/orders/ajax_print_check')?>?payment_id="class="btn btn-default btn-flat btn-block print">Print check</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var doCalculation = function () {
            var totalAmount = 0;
            // var type = $('#po-type').val();
            var type = 'lcl';
            $("#order-line tbody tr").each(function () {
                if(type === 'imt'){
                    var lineTotal = parseFloat(numeral().unformat($(this).find(".contains-line-total").val() || 0));
                    totalAmount += lineTotal;
                }else{
                    var unitPrice = numeral().unformat($(this).find("input.has-amount").val() || 0),
                        quantity = numeral().unformat($(this).find("input.has-quantity").val() || 0),
                        lineQuantity = parseFloat(quantity),
                        lineAmount = parseFloat(unitPrice),
                        lineTotal = lineQuantity*lineAmount;
                    totalAmount += lineTotal;
                    $(this).find(".contains-line-total").text(numeral(lineTotal).format('0,0.00'));
                    // $(this).find(".contains-line-total").val(numeral(lineTotal).format('0,0.00'));
                }
                
            });
            $("span.contains-total-amount").text(numeral(totalAmount).format('0,0.00'));
            $('p.bg-red > strong').text(function(){
                var otherFeesTotal = numeral().unformat($(".contains-others-amount").text() || 0),
                    total = parseFloat(totalAmount) + parseFloat(otherFeesTotal);
                return numeral(total).format('0,0.00');
            });
        }
        var calculateOtherFees = function(){
            var total = 0;
            $.each($('#other-fees tbody tr'), function(i,v){
                total+= parseFloat(numeral().unformat($(v).find('.has-amount').val()));
            });
            $('.contains-others-total').text(numeral(total).format('0,0.00'))
            $('p.bg-red > strong').text(function(){
                var productTotal = numeral().unformat($(".contains-total-amount").text() || 0),
                    totalAmount = parseFloat(productTotal) + total;
                return numeral(totalAmount).format('0,0.00');
            });
        };  
        var initializePriceFormat = function (element) {
            element.priceFormat({prefix: ''});
        }
        var initializeDatepicker = function (element) {
            element.datepicker({dateFormat: 'yy-mm-dd'});
            if (!element.val() || element.val() === '') {
                element.datepicker('setDate', new Date());
            }
        }
        initializePriceFormat($('.has-amount'));
        initializeDatepicker($('.datepicker'));
        var _mode = '<?= isset($mode) ? $mode : 'update'; ?>';
        $('#supplier-name').change(function () {
            var request = $.get('<?= base_url('purchases/orders/ajax_get_assigned_supplies') ?>/' + $(this).val());
            request.done(function (response) {
                var suppyList = $(response);
                suppyList.attr('name', 'details[fk_inventory_product_id][]').attr('required', 'required');
                $('#order-line tbody tr:first td:first select').replaceWith(suppyList);
            });
        });
        $("form").submit(function (e) {
            e.preventDefault();
            $("[type=submit]").addClass("disabled");
            $(".callout-danger ul li").remove();
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                dataType: 'json',
                data: $(this).serialize(),
                success: function (data) {
                    if (data.error_flag) {
                        $(".callout-danger").removeClass("hidden");
                        if (typeof data.message === 'string') {
                            $(".callout-danger ul").append("<li>" + data.message + "</li>");
                        } else {
                            $(".callout-danger ul").append("<li>" + data.message.join("</li><li>") + "</li>");
                        }
                        $("input[type=submit]").removeClass("disabled");
                        $("html, body").animate({scrollTop: 0}, "slow");
                    } else {
                        if (_mode === 'new') {
                            window.location.href = data.data.redirect;
                        } else {
                            window.location.reload();
                            //$.growl.notice({title: 'Success', message: 'Update success!'});
                        }
                    }
                },
                error: function (xhr, err) {
                    $.growl.error({title: 'Unexpected error', message: 'Internal server error. Please try again'});
                },
                complete: function () {
                    $("[type=submit]").removeClass("disabled");
                }
            });
        });
        var addLine = function(table){
            var template = $(table+' tbody tr:first').clone().removeClass('hidden');
            template.find('input,select').removeAttr('disabled');
            template.find('select,input').val('');
            template.find('span').text('');
            template.find('[type=hidden]').remove();
            template.find('.has-amount').priceFormat({prefix: ''});
            template.find('.datepicker').removeAttr('id').removeClass('hasDatepicker').datepicker({dateFormat: 'yy-mm-dd'});
            if($('#po-type').val() === 'lcl'){
                template.find('.disabled-local').attr('disabled','disabled');
            }
            if(table === '#issued-checks'){
                template.find('td:last a:first').remove();
            }
            template.appendTo(table+' tbody');

        }
        //add-line function
        $('.add-line').click(function () {addLine('#order-line')});
        $('.add-fees').click(function () {addLine('#other-fees')});
        $('.add-check').click(function () {addLine('#issued-checks')});
        //shows the unit
        $('tbody').on('change', 'select.product-listing', function () {
            var unit = $(this).find('option:selected').data('unit');
            $(this).closest('tr').find('span.contains-unit').text(unit);
        });
        $('#order-line tbody').on('keyup input', '.do-calculation', function () {
            doCalculation();
        });
        $('#other-fees').on('keyup input', '.has-amount', function () {
            calculateOtherFees();
        });
        $('#order-line, #other-fees, #issued-checks').on('click', '.remove-line', function () {
            var table = $(this).closest('table').attr('id');
            var row = $(this).closest('tr');
            if ($(this).closest('tbody').find('tr').length === 1) {
                row.find('select,input').val('').attr('disabled','disabled');
                row.find('span').text('');
                row.addClass('hidden');
            } else {
                row.remove();
            }
            if(table === 'other-fees'){
                calculateOtherFees();
                return;
            }else if(table === 'order-line'){
                doCalculation();
            }
            
        });

        $('#po-type').change(function(){
            var val = $(this).val();
            if(val === 'lcl'){
                $('#order-line tr').find('.contains-line-total, .xrate').attr('disabled', 'disabled');
                $('.shown-on-import').addClass('hidden').find('input,select').attr('disabled', 'disabled');
                $('.add-line').removeClass('hidden');
            }else{
                $('#order-line > tbody > tr:not(:first)').remove();
                $('.shown-on-import').removeClass('hidden').find('input,select').removeAttr('disabled', 'disabled');
                $('#order-line tr').find('.contains-line-total, .xrate').removeAttr('disabled');
                $('.add-line').addClass('hidden');
            }
        });


        var paymentId;
        $('#check-options').on('show.bs.modal', function(e){
            paymentId = $(e.relatedTarget).data('payment-id');
            var printBtns = $('.print');
            printBtns.attr('href', function(){
                return $(this).data('url')+paymentId;
            });
            printBtns.unbind().printPage();
        });
    });
</script>