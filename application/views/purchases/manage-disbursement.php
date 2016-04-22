<style type="text/css">
    #disbursement-details tr td:nth-child(7){
        text-align: right;
    }
</style>
<input type="hidden" name="data-link-undisbursed-po" value="<?= base_url('purchases/orders/ajax_for_advanced_disbursement') ?>">
<input type="hidden" name="data-link-to-undisbursed-rr" value="<?= base_url('purchases/receiving/a_get_undisbursed') ?>">
<input type="hidden" name="data-link-to-rr" value="<?= base_url('purchases/receiving/manage?do=update-purchase-receiving&id=') ?>">
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <?= form_open($action, array('role' => 'form')); ?>
            <div class="box-header bg-light-blue-gradient" style="color:#fff">
                <h3 class="box-title"><?= isset($form_title) ? $form_title : '' ?></h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="callout callout-danger hidden">
                    <h4>Error!</h4>
                    <ul class="list-unstyled"></ul>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?php if(isset($disbursement_prev_info)) { ?>
                        <?= ($disbursement_prev_info) ? '<a href="'.$disbursement_prev_info.'"><i class="fa fa-arrow-left"></i> Go to CV # '.$disbursement_prev_id.'</a>' : '' ?>
                        <?php } ?>
                    </div>
                    <div class="col-sm-6 text-right">
                        <?php if(isset($disbursement_next_info)){ ?>
                        <?= ($disbursement_next_info) ? '<a href="'.$disbursement_next_info.'">Go to CV # '.$disbursement_next_id.' <i class="fa fa-arrow-right"></i></a>' : '' ?>
                        <?php } ?>
                    </div>
                </div>
                <?= ( (isset($disbursement_prev_info) && isset($disbursement_next_info)) && (!empty($disbursement_prev_info) || !empty($disbursement_next_info)) ) ? "<hr/>" : "" ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for='payment-type'>Disbursement Type</label>
                            <?php if ($defaults['disbursement_type'] === 'rr'): ?>
                                <p class="form-control-static">RR Payment</p>
                                <?= form_hidden('disbursement_type', $defaults['disbursement_type']) ?>
                            <?php else: ?>
                                <?= generate_dropdown('disbursement_type', ['rr' => 'R.R. Payment', 'advance' => 'Advance R.R. Payment'], $defaults['disbursement_type'], 'class="form-control" id="disbursement-type" required="required"', 'disbursement type') ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for='supplier'>Supplier</label>
                            <?php if ($defaults['fk_maintainable_supplier_id']): ?>
                                <p class="form-control-static"><?= $defaults['supplier'] ?></p>
                            <?php else: ?>
                                <?php $attr = 'class="form-control" id="supplier"  required="required"' ?>
                                <?= generate_dropdown('fk_maintainable_supplier_id', $suppliers, $defaults['fk_maintainable_supplier_id'], $attr, 'supplier') ?>
                            <?php endif; ?>
                        </div>  
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for='supplier'>PO No.</label>
                             <?php if ($defaults['fk_purchase_order_id']): ?>
                                <p class="form-control-static"><?= $defaults['fk_purchase_order_id'] ?></p>
                            <?php else: ?>
                                <select class="form-control" disabled="disabled" name="fk_purchase_order_id" required="required"></select>
                            <?php endif; ?>
                            
                        </div>  
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for='date'>Date</label>
                            <input type='text' class='datepicker form-control has-current-date' value="<?= $defaults['date'] ?>" name="date" id='date' required>
                        </div>  
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for='date'>Payee</label>
                            <input type='text' class=' form-control' value="<?= $defaults['payee'] ?>" name="payee" id='payee'>
                            <span class="help-block">Supplier name will be used when empty</span>
                        </div>  
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <?= form_textarea(array('name' => 'remarks', 'class' => 'form-control', 'id' => 'remarks', 'rows' => 3, 'value' => $defaults['remarks'])); ?>
                        </div>  
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8 table-responsive">
                        <table class="table table-bordered" style="border-bottom: none;border-left: none;border-right: none;">
                            <thead ><tr class="info"><th style="width:15%">RR #</th><th >PO #</th><th  style="width:10%">DR/SI#</th><th>Chart</th><th>Date</th><th>Amount</th></tr></thead>
                            <tbody id="disbursement-details">
                                <?php if (isset($defaults['line']) && !empty($defaults['line'])): ?>
                                    <?php foreach ($defaults['line'] as $line): ?>
                                        <tr>

                                            <td>
                                                <input name="disbursement_details[id][]" type="hidden" value="<?= $line['id'] ?>" checked> 
                                                <div class="checkbox">
                                                    <label>
                                                        <input name="disbursement_details[fk_purchase_receiving_id][]" class="do-calculation" type="checkbox" value="<?= $line['fk_purchase_receiving_id'] ?>" checked="checked"/> 
                                                        <?= $line['fk_purchase_receiving_id'] ?>
                                                    </label> 
                                                    <a style="margin-left:10px;" target="_blank" href="<?= base_url("purchases/receiving/manage?do=update-purchase-receiving&id={$line['fk_purchase_receiving_id']}") ?>">
                                                        <i class="fa fa-link"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            <td><a target="_blank" href='<?= base_url("purchases/orders/manage?do=update-purchase-order&id={$line['fk_purchase_order_id']}") ?>'><?= $line['fk_purchase_order_id'] ?></a></td>
                                            <td><?= $line['pr_number'] ?></td>
                                            <td><?= form_dropdown('disbursement_details[chart_id][]', ['' => ''] + $charts, $line['chart_id'], 'class="form-control"')?></td>
                                            <td><?= $line['receiving_date'] ?></td><td class="text-right"><span class="contains-line-amount"><?= number_format($line['amount'], 2) ?></span></td></tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center">Please select a supplier and a corresponding P.O. No.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <div class="box box-solid box-primary">
                            <div class="box-header">
                                <h3 class="box-title">Payment Details</h3>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label>Payment Type</label>
                                    <?= form_dropdown('payment_type', ['check' => 'Check', 'cash' => 'Cash'], $defaults['payment_type'], 'class="form-control" id="pay-opt"') ?>
                                </div>
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="text" value='<?= number_format($defaults['amount'], 2) ?>' class="form-control price-format has-amount" id="total-payment" required="required" name="amount"/>
                                </div>
                                <?php $add_disabled = $defaults['payment_type'] === 'cash' ? 'disabled="disabled"' : '' ?>
                                <div class="form-group">
                                    <label>Check Type</label>
                                    <?= select_cheque('check_type', isset($defaults['check_type']) ? $defaults['check_type'] : FALSE, 'class="form-control pay-opt-cash-disabled" required="required"'.$add_disabled)?>
                                   
                                </div>
                                <div class="form-group">
                                    <label>Account</label>
                                    <?= generate_dropdown('fk_accounting_bank_account_id', $bank_accounts, $defaults['fk_accounting_bank_account_id'], 'class="form-control pay-opt-cash-disabled" required="required"' . $add_disabled) ?>
                                </div>
                                <div class="form-group">
                                    <label>Check Number</label>
                                    <input type="text" class="form-control pay-opt-cash-disabled" value="<?= $defaults['check_number'] ?>" required="required" name="check_number" <?= $add_disabled ?>/>
                                </div>
                                <div class="form-group">
                                    <label>Check Date</label>
                                    <input type="text" class="form-control pay-opt-cash-disabled datepicker" value="<?= $defaults['check_date'] ?>" required="required" name="check_date" <?= $add_disabled ?>/>
                                </div>
                            </div><!-- /.box-body -->
                        </div>
                    </div>
                </div>
                <?php if ((int) $this->session->userdata('type_id') === (int) M_Account::TYPE_ADMIN): ?>
                    <hr>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="status" value="<?= M_Status::STATUS_APPROVED ?>"
                                               <?= (int) $defaults['status'] === (int) M_Status::STATUS_APPROVED ? "checked" : "" ?>/>
                                        Mark this purchase disbursement as approved
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="box-footer">
                <?php if ((int) $defaults['status'] !== (int) M_Status::STATUS_APPROVED || is_admin() || !$is_locked): ?>
                    <?= form_button(array('type' => 'submit', 'class' => 'btn btn-success btn-flat', 'content' => 'Save')); ?>
                <?php endif; ?>
                <a href="<?= base_url('purchases/disbursements') ?>" class="btn btn-warning pull-right btn-flat">Go back</a>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<?= form_dropdown('disbursement_details[chart_id][]', ['' => ''] + $charts, FALSE, 'class="hidden form-control _include" disabled="disabled" id="chart-template"')?>
<script type="text/javascript">
    $(document).ready(function () {
        var _mode = '<?= isset($mode) ? $mode : 'update'; ?>';
        var currentSupplier = '<?= $defaults['fk_maintainable_supplier_id'] ? $defaults['fk_maintainable_supplier_id'] : '' ?>';
        var $paymentFor = $('select[name=payment_for]'), $supplier = $('select[name=fk_maintainable_supplier_id]'),
                $poNumber = $('select[name=fk_purchase_order_id]'), $disbursedAmount = $('input[name=disbursed_amount]'),
                $disbursementDetails = $("tbody#disbursement-details");
        //functions
        function getPO() {
            var request = $.getJSON($('[name=data-link-undisbursed-po]').val(), {supplier_id: $supplier.val()});
            request.done(function (response) {
                var options = ['<option></option>'];
                $.each(response.data, function (index, value) {
                    options.push('<option data-amount="' + numeral(value.total_amount).format('0,0.00') + '" value="' + value.id + '">PO #' + value.id + '</option>');
                });
                $('[name=fk_purchase_order_id]').removeAttr('disabled').html(options.join(''));
            });
        }
        function generateLinktoRR(rrId) {
            return $("input[name=data-link-to-rr]").val() + rrId;
        }
        function generateLinktoPO(rrId) {
            var url = "<?= base_url('purchases/orders/manage?do=update-purchase-order&id=') ?>" + rrId;
            return '<a target="_blank" href="' + url + '">PO# ' + rrId + '</a>';
        }
        function doCalculation() {
            var total = numeral();
            $("tbody#disbursement-details tr").each(function (i) {
                var $this = $(this);
                if ($this.find("input[type=checkbox]").is(":checked")) {
                    total.add(numeral().unformat($this.find("span.contains-line-amount").text()));
                }
            });
            return numeral(total).format("0,00.00");
        }
        function initializePriceFormat(element) {
            element.priceFormat({prefix: ''});
        }
        function initializeDatepicker(element) {
            element.datepicker({dateFormat: 'yy-mm-dd'});
            if ($('.datepicker.has-current-date').val().length === 0) {
                $('.datepicker.has-current-date').datepicker('setDate', new Date());
            }
        }
        function getUndisbursed(supplierId) {
            $("tbody#disbursement-details").html('<tr><td colspan="5" class="text-center">Loading R.R.</td></tr>');
            $.getJSON($("input[name=data-link-to-undisbursed-rr]").val(), {supplier_id: supplierId}).done(function (json) {
                if (json.error_flag) {
                    $("tbody#disbursement-details").html('<tr><td colspan="6" class="text-center">No available entries</td></tr>');
                    return;
                }
                var tableRow = [];
                var chart = $('#chart-template').clone().removeAttr('id').removeClass('hidden').prop('outerHTML');
                $(json.data).each(function (i) {
                    var tableCell = [];
                    tableCell[0] = '<div class="checkbox"><label><input name="disbursement_details[fk_purchase_receiving_id][]" class="do-calculation _rr" type="checkbox" value="' + json.data[i].id + '"/> RR# ' + json.data[i].id + '</label>' +
                            '<a style="margin-left:10px;" target="_blank" href=' + generateLinktoRR(json.data[i].id) + '><i class="fa fa-link"></i></a></div>';
                    tableCell[1] = generateLinktoPO(json.data[i].fk_purchase_order_id);
                    tableCell[2] = json.data[i].pr_number;
                    tableCell[3] = chart;
                    tableCell[4] = json.data[i].date;
                    tableCell[5] = '<span class="contains-line-amount text-right">' + json.data[i].total_amount + '</span>';
                    tableRow.push("<tr><td>" + tableCell.join("</td><td>") + "</td></tr>");
                });
                $("tbody#disbursement-details").html(tableRow.join(""));
            }).fail(function () {
                alert("Internal Server Error!");
            });
        }
        //end of functions
        initializePriceFormat($('.has-amount, .price-format')); //initialize price format on fields
        initializeDatepicker($('.datepicker'));//initialize datepicker on fields
        $('select#disbursement-type').change(function () {
            if ($(this).val() === 'advance') {
                getPO();
                $('select[name=fk_purchase_order_id]').html('');
                $disbursementDetails.html('<tr><td colspan="5" class="text-center">Please select a supplier and a corresponding P.O. No.</td></tr>');
            } else {
                var supplier = $supplier.val();
                getUndisbursed(currentSupplier ? currentSupplier : supplier);
                $('[name=fk_purchase_order_id]').html('').attr('disabled', 'disabled');
            }
        });
        $('[name=fk_purchase_order_id]').change(function () {
            $('[name=amount]').val($(this).find('option:selected').data('amount'));
        });
        $('select[name=fk_maintainable_supplier_id]').change(function () {
            var disbursementType = $('#disbursement-type').val();
            if (disbursementType === 'advance') {
                getPO();
            } else {
                getUndisbursed($(this).val());
            }
        });
        $('tbody').on('change', '.do-calculation', function () {
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
                        $("[type=submit]").removeClass("disabled");
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
                    $.growl.error({title: 'Unexpected error', message: 'Internal server error. Please try again'});
                },
                complete: function () {
                    $("[type=submit]").removeClass("disabled");
                }
            });
            return false;
        });
        $('#pay-opt').change(function () {
            $(this).val() === 'cash' ? $('.pay-opt-cash-disabled').attr('disabled', 'disabled') : $('.pay-opt-cash-disabled').removeAttr('disabled');
        });
        $('#disbursement-details').on('change', '._rr', function () {
            if ($(this).is(':checked')) {
                $(this).closest('tr').find('select._include').removeAttr('disabled');
            } else {
                $(this).closest('tr').find('select._include').attr('disabled', 'disabled');
            }
            $('#total-payment').val(doCalculation());
        });
    });
</script>