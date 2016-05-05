<style type="text/css">
    div.t{
        border-bottom:1px solid black;
    }
</style>
<input type="hidden" name="data-link-to-unreceived-po" value="<?= base_url('purchases/orders/a_get_unreceived') ?>">
<input type="hidden" name="data-link-to-po-details" value="<?= base_url('purchases/orders/a_get') ?>">
<input type='hidden' name='data-bank-list-url' value='<?= base_url('accounting/bank_accounts/a_get') ?>'/>
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
                    <div class="col-sm-6">
                        <?php if(isset($receiving_prev_info)){ ?>
                        <?= ($receiving_prev_info) ? '<a href="'.$receiving_prev_info.'"><i class="fa fa-arrow-left"></i> Go to RR # '.$receiving_prev_id.'</a>' : '' ?>
                        <?php } ?>
                    </div>
                    <div class="col-sm-6 text-right">
                        <?php if(isset($receiving_next_info)){ ?>
                        <?= ($receiving_next_info) ? '<a href="'.$receiving_next_info.'">Go to RR # '.$receiving_next_id.' <i class="fa fa-arrow-right"></i></a>' : '' ?>
                        <?php } ?>
                    </div>
                </div>
                <?= ( (isset($receiving_prev_info) || isset($receiving_next_info)) && ( !empty($receiving_prev_info) || !empty($receiving_next_info) ) ) ? "<hr/>" : "" ?>
                <div class="row">
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="po-number">P.O. No.</label>
                            <?php if ($defaults['fk_purchase_order_id']): ?>
                                <input type="hidden" value="<?= $defaults['fk_purchase_order_id'] ?>" name="fk_purchase_order_id"/>
                                <p class="form-control-static">P.O. # <?= $defaults['fk_purchase_order_id'] ?></p>
                            <?php else: ?>
                                <select name="fk_purchase_order_id" class="form-control" id="po-number" required='required'>
                                    <option>Please select a supplier</option>
                                </select>
                            <?php endif; ?>
                        </div>  
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="pr-number">D.R. # / S.I. #</label>
                            <?= form_input(array('name' => 'pr_number', 'class' => 'form-control', 'id' => 'pr-number', 'value' => $defaults['pr_number'])); ?>
                        </div>  
                    </div>  


                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date">Date &amp; time</label>
                            <?= form_input(array('name' => 'date', 'class' => 'form-control datepicker', 'id' => 'date', 'required' => 'required', 'value' => $defaults['date'])); ?>
                        </div>  
                    </div>
                </div>
                <div class="row">


                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="date">Remarks</label>
                            <?= form_textarea(array('name' => 'remarks', 'class' => 'form-control', 'id' => 'remarks', 'rows' => 3, 'resizable-x' => 'none', 'value' => $defaults['remarks'])); ?>
                        </div>  
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12 table-responsive table-condensed">
                        <table class="table table-bordered" style="border-bottom: none;border-left: none;border-right: none;" id="receiving-line">
                            <thead>
                                <tr class="info">
                                    <th style="width:15%">Product</th>
                                    <th style="width:7%">Unit</th>
                                    <th style="width:10%">Ordered Qty</th>
                                    <th style="width:10%">Qty Received</th>
                                    <th style="width:15%">This Receive</th>
                                    <th style="width:8%">Unit Price</th>
                                    <th style="width:15%">Discount</th>
                                    <th style="width:10%">Gross Amt</th>
                                    <th style="width:10%">Net Amt</th>
                                </tr>
                            </thead>
                            <tfoot>

                                <tr><td class="no-border" colspan="6"></td><td class="info"><b>Total Amount</b></td><td class="info text-right" colspan="2"><span class="net-total-amount"><?= $defaults['total_amount'] ?></span></td></tr>
                            </tfoot>
                            <tbody id='receiving-details'>
                                <?php if (isset($defaults['details']) && is_array($defaults['details'])): ?>
                                    <?php foreach ($defaults['details'] as $detail): ?>
                                        <?php $gross_amount = $detail['this_receive']*$detail['unit_price_unformatted'];?>
                                        <tr>
                                            <td><?= $detail['description'] . (isset($detail['code']) ? ' (' . $detail['code'] . ')' : '') ?> 
                                                <input type="hidden" value="<?= $detail['id'] ?>" name="details[id][]">
                                                <input type="hidden" value="<?= $detail['fk_purchase_order_detail_id'] ?>" name="details[fk_purchase_order_detail_id][]">
                                            </td>
                                            <td>
                                                <div class="t">
                                                    <?= $detail['unit_description'] ?>
                                                </div>
                                                pcs
                                            </td>
                                            <td>
                                                <div class="t">
                                                    <?= $detail['quantity'] ?>
                                                </div>  
                                                <?= $detail['pieces'] ?>
                                            </td>
                                            <td>
                                                <div class="t">
                                                    <?= $detail['delivered_quantity'] ?>
                                                </div>
                                                <?= $detail['delivered_pieces'] ?>
                                            </td>
                                            <td>
                                                <div class="t">
                                                    <input value="<?= $detail['this_receive'] ?>" type="number" class="form-control this-receive do-calculation" name="details[this_receive][]" step="0.01">
                                                </div>
                                                <input value="<?= $detail['pieces_received'] ?>" type="number" class="form-control" name="details[pieces_received][]" step="0.01">
                                            </td>
                                            <td><span class="unit-price"><?= $detail['unit_price'] ?></span></td>
                                            <td><input name="details[discount][]" type="text" value="<?= number_format($detail['discount'], 2) ?>" class="form-control discount has-amount do-calculation"/></td>
                                            <td><span class="gross-amount"><?= number_format($gross_amount, 2) ?></span></td>
                                            <td><span class="net-amount"><?= number_format($gross_amount - $detail['discount'], 2) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td  colspan="9" class="text-center">Please select a supplier and the corresponding P.O. No.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr>
                <?php if ((int) $this->session->userdata('type_id') === (int) M_Account::TYPE_ADMIN): ?>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="status" value="<?= M_Status::STATUS_RECEIVED ?>" class="flat-red" 
                                   <?= (int) $defaults['status'] === (int) M_Status::STATUS_RECEIVED ? "checked" : "" ?>/>
                            Mark this purchase receiving as received
                        </label>
                    </div>
                <?php endif; ?>
            </div>
            <div class="box-footer clearfix">
                <?php if ((int) $defaults['status'] !== (int) M_Status::STATUS_RECEIVED || is_admin() || !$is_locked): ?>
                    <?= form_button(array('type' => 'submit', 'class' => 'btn btn-primary btn-flat', 'content' => 'Save')); ?>
                <?php endif; ?>
                <a href="<?= base_url('purchases/receiving') ?>" class="btn btn-warning pull-right btn-flat">Go back</a>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<script type='text/javascript'>
    $(document).ready(function () {
        var _mode = '<?= isset($mode) ? $mode : 'update'; ?>';
        function doCalculation() {
            var totalAmount = 0;
            $("tbody#receiving-details tr").each(function () {
                var thisReceive = parseFloat($(this).find("input.this-receive").val()),
                        unitPrice = parseFloat(numeral().unformat($(this).find("span.unit-price").text())),
                        discount = parseFloat(numeral().unformat($(this).find("input.discount").val())),
                        grossAmount = unitPrice * thisReceive,
                        netAmount = grossAmount - discount;
                totalAmount += netAmount;
                $(this).find("span.gross-amount").text(numeral(grossAmount).format('0,0.00'));
                $(this).find("span.net-amount").text(numeral(netAmount).format('0,0.00'));
            });
            $("span.net-total-amount").text(numeral(totalAmount).format('0,0.00'));
        }
        function initializePriceFormat(element) {
            element.priceFormat({prefix: ''});
        }
        function initializeDatepicker(element) {
            element.datetimepicker({
                timeFormat: 'hh:mm tt',
                'dateFormat': 'yy-mm-dd'
            });
            if (!element.val() || element.val() === '') {
                //   element.datepicker('setDate', new Date());
                element.datetimepicker({
                    timeFormat: 'hh:mm tt'
                }).datetimepicker('setDate', (new Date()));
            }
        }
        //end of functions
        initializePriceFormat($('.has-amount')); //initialize price format on fields
        initializeDatepicker($('.datepicker'));//initialize datepicker on fields
        //get p.o. per supplier
        $('select[name=fk_maintainable_supplier_id]').change(function () {
            var $val = $(this).val();
            $("tbody#receiving-details").html('<tr><td colspan="9" class="text-center">Please select a supplier and the corresponding P.O. No.</td></tr>');
            if (!$val) {
                return;
            }
            $.getJSON($("input[name=data-link-to-unreceived-po]").val(), {supplier_id: $val}).done(function (data) {
                if (!data.error_flag) {
                    var options = [];
                    options.push('<option value="">Please select P.O. No.</option>');
                    $(data.data).each(function (i) {
                        options.push('<option value=' + data.data[i] + '>P.O. No. ' + data.data[i] + '</option>');
                    });
                    $('select[name=fk_purchase_order_id]').html(options.join(""));
                } else {
                    $('select[name=fk_purchase_order_id]').html('<option>' + data.message + '</option>');
                }
            }).fail(function () {
                alert("Internal Server Error!");
            });
        });
        //get po details
        $('select[name=fk_purchase_order_id]').change(function () {
            var $val = $(this).val();
            $("tbody#receiving-details").html('<tr><td colspan="9" class="text-center">Please select a supplier and the corresponding P.O. No.</td></tr>');
            if (!$val || $val === 0) {
                return;
            }
            $.getJSON($("input[name=data-link-to-po-details]").val(), {order_id: $val, supplier_id: $('select[name=fk_purchase_supplier_id]').val()}).done(function (json) {
                $("tbody#receiving-details").html("");
                if (json.error_flag) {
                    $("tbody#receiving-details").html('<tr><td colspan="9" class="text-center">No available entries</td></tr>');
                    return;
                }
                var tableRow = [];
                $(json.data.details).each(function (i) {
                    var detail = json.data.details[i];
                    if(detail.quantity_received >= detail.quantity){
                        return;
                    }
                    var tableCell = [];
                    tableCell[0] = detail.description + '<input type="hidden" value="' + detail.id + '" name="details[fk_purchase_order_detail_id][]">';
                    tableCell[1] = '<div class="t">'+detail.unit_description+'</div>pcs';
                    tableCell[2] = '<div class="t">'+detail.quantity+'</div>'+detail.pieces;
                    tableCell[3] = '<div class="t">'+detail.quantity_received+'</div>0.00';
                    tableCell[4] = '<div class="t"><input value="0" type="number" class="form-control this-receive do-calculation" name="details[this_receive][]" step="0.01"/></div><input value="0" type="number" class="form-control" name="details[pieces_received][]" step="0.01"/>';
                    tableCell[5] = '<span class="unit-price">' + detail.unit_price + '</span>';
                    tableCell[6] = '<input name="details[discount][]" type="text" value="0.00" class="form-control discount has-amount do-calculation"/>';
                    tableCell[7] = '<span class="gross-amount">0.00</span>';
                    tableCell[8] = '<span class="net-amount">0.00</span>';
                    tableRow.push("<tr><td>" + tableCell.join("</td><td>") + "</td></tr>");
                });
                $("tbody#receiving-details").html(tableRow.join(""));
                initializePriceFormat($("tbody#receiving-details tr").find('.has-amount'));
            }).fail(function () {
                alert("Internal Server Error!");
            });
        });
        $('tbody#receiving-details').on('keyup', '.do-calculation', function () {
            doCalculation();
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
                            $.growl.notice({title: 'Success', message: 'Update success!'});
                        }
                    }
                },
                error: function (xhr, err) {
                    $.growl.error({title: 'Error', message: 'An unexpected error has occurred. Please try again later.'});
                },
                complete: function () {
                    $("[type=submit]").removeClass("disabled");
                }
            });
        });
    });
</script>