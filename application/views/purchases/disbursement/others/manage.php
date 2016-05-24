<style>
    table#charts thead tr th:nth-child(1){
        width:48%
    }
    table#charts thead tr th:nth-child(2){
        width:48%
    }
    table#charts tbody tr td:nth-child(3){
        text-align: center;
    }
</style>
<?php $url = base_url('purchases/other_disbursements/'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff!important;">
                <h3 class="box-title"><?= $form_title; ?></h3>
            </div> 
            <form class="form" id="add-voucher" method="POST" action="<?= $action ?>">
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
                    <?= ( (isset($disbursement_prev_info) || isset($disbursement_next_info)) && ( !empty($disbursement_prev_info) || !empty($disbursement_prev_info) ) ) ? "<hr/>" : "" ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Payee</label>
                                <input type="text" name="payee" value="<?= isset($data['payee']) ? $data['payee'] : '' ?>" class="form-control"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Voucher Reference</label>
                                <input type="text" name="vreference" value="<?= isset($data['vreference']) ? $data['vreference'] : '' ?>" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea class="form-control" name="remarks"><?= isset($data['remarks']) ? $data['remarks'] : '' ?></textarea>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table" id="charts">
                                <thead><tr class="info"><th style="width:25%">Date</th><th style="width:25%">Chart of accounts</th><th style="width:25%">Description</th><th style="width:20%">Amount</th><th></th></tr></thead>
                                <tbody>
                                    <?php $liquidation = [[]]; ?>
                                    <?php if (isset($data['liquidation'])): ?>
                                        <?php $liquidation = $data['liquidation']; ?>
                                    <?php endif; ?>
                                    <?php foreach ($liquidation as $key => $l): ?>
                                        <tr>
                                            <td>
                                                <?php if (isset($l['id'])): ?>
                                                    <input type="hidden" name="line[<?= $key ?>][id]" value="<?= $l['id']?>"/>
                                                <?php endif; ?>
                                                <input value="<?= isset($l['date']) ? $l['date'] : '' ?>" type="text" class="form-control has-index datepicker" name="line[<?= $key ?>][date]" data-name="line[x][date]" required="required"/>
                                            </td>
                                            <td><?= generate_dropdown("line[{$key}][account_id]", $charts, isset($l['account_id']) ? $l['account_id'] : FALSE, 'class="form-control has-index" data-name="line[x][account_id]" required="required"') ?></td>
                                            <td><input value="<?= isset($l['description']) ? $l['description'] : '' ?>" type="text" class="form-control has-index" name="line[<?= $key ?>][description]" data-name="line[x][description]" required="required"/></td>
                                            <td><input value="<?= isset($l['amount']) ? number_format($l['amount'], 2) : '' ?>" type="text" class="form-control has-index price-format subtotal" name="line[<?= $key ?>][amount]" data-name="line[x][amount]" required="required"/></td>
                                            <td><a class="btn btn-flat btn-sm btn-danger remove-line"><i class="fa fa-times"></i></a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-left"><a id="add-chart" class="btn btn-default  btn-sm btn-flat"><i class="fa fa-plus"></i> Add new line</a></td>
                                        <td colspan="2" class="clearfix text-right">Total amount:</td><td class="text-right"><span id="total-amount" class="text-bold">0.00</span></td><td></td>

                                    </tr>
                                </tfoot>
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
                                        <?= form_dropdown('payment_type', ['cash' => 'Cash', 'check' => 'Check'], isset($data['payment_type']) ? $data['payment_type'] : FALSE, 'class="form-control" id="pay-opt"') ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Amount</label>
                                        <input value="<?= isset($data['amount']) ? number_format($data['amount'], 2) : '' ?>" type="text" class="form-control price-format" name="amount" id="payment-amount"/>
                                    </div>
                                    <?php $disabled = ' disabled="disabled"' ?>
                                    <?php if (isset($data['fk_accounting_bank_account_id']) && $data['fk_accounting_bank_account_id']): ?>
                                        <?php $bank = $data['fk_accounting_bank_account_id']; ?>
                                        <?php $disabled = ''; ?>
                                    <?php endif; ?>
                                    <div class="form-group">
                                        <label>Account</label>
                                        <?= generate_dropdown('fk_accounting_bank_account_id', $bank_accounts, isset($bank) ? $bank : FALSE, 'class="form-control pay-opt-cash-disabled" required="required"' . $disabled) ?>
                                    </div>
                                    <?php if (isset($data['check_number']) && $data['check_number']): ?>
                                        <?php $check_number = $data['check_number']; ?>
                                    <?php endif; ?>
                                    <div class="form-group">
                                        <label>Check Number</label>
                                        <input value="<?= isset($check_number) ? $check_number : '' ?>" type="text" class="form-control pay-opt-cash-disabled" required="required" name="check_number" <?= $disabled ?>/>
                                    </div>
                                    <?php if (isset($data['check_date']) && $data['check_date']): ?>
                                        <?php $check_date = $data['check_date']; ?>
                                    <?php endif; ?>
                                    <div class="form-group">
                                        <label>Check Date</label>
                                        <input value="<?= isset($check_date) ? $check_date : '' ?>" type="text" class="form-control pay-opt-cash-disabled datepicker" required="required" name="check_date" <?= $disabled ?>/>
                                    </div>
                                    <?php $crossed = isset($data['crossed']) &&  (int)$data['crossed'] ? 'checked="checked"' : '' ?>
                                    <div class="checkbox"><label><input type="checkbox" class="pay-opt-cash-disabled" name="crossed" value="1" <?=$crossed.$disabled?>/> <b>Cross checked?</b></label></div>
                                    <div class="checkbox">
                                        <label>
                                           <?php 
                                                $hide_check_date_on_print = '';
                                                if(isset($data['print_check_date']) && (int)$data['print_check_date'] === 0){
                                                    $hide_check_date_on_print ='checked="checked"';
                                                }
                                            ?>
                                            <input class="pay-opt-cash-disabled" type="checkbox" name="hide_check_date_on_print" value="1" <?= $hide_check_date_on_print.$disabled?> /> <b>Hide check date on print?</b> 
                                        </label>
                                    </div>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                    </div>
                    <?php if ($this->session->userdata('type_id') == M_Account::TYPE_ADMIN): ?>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="status" value="<?= M_Status::STATUS_APPROVED ?>"
                                       <?= isset($data['status']) && $data['status'] == M_Status::STATUS_APPROVED ? 'checked="checked"' : "" ?>/>
                                Mark this check voucher as approved
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="box-footer clearfix">
                    <button type="submit" class="btn btn-success btn-flat">Save</button>
                    <a role="button" class="btn btn-warning btn-flat pull-right" href="<?= $url ?>">Go back</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var _mode = '<?= isset($mode) ? $mode : 'update'; ?>';
        var nextIndex = <?= count($liquidation) + 1;?>;
        var priceFormatOptions = {
            prefix: '',
            allowNegative: true
        };
        var getTotal = function () {
            var total = 0;
            $.each($('.subtotal'), function () {
                total += parseFloat(numeral().unformat($(this).val()));
            });
            $("span#total-amount").text(numeral(total).format('0,0.00'));
            $("input#payment-amount").val(numeral(total).format('0,0.00'));
        };
        $('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd'
        });
        $('.price-format').priceFormat(priceFormatOptions);
        $('#pay-opt').change(function () {
            $(this).val() === 'cash' ? $('.pay-opt-cash-disabled').attr('disabled', 'disabled') : $('.pay-opt-cash-disabled').removeAttr('disabled');
        });
        $('#add-chart').click(function () {
            var row = $('#charts tbody tr:first').clone(); //clone the first row
            row.find('select,input').val(''); //erase all values
            //update the index
            row.find('.has-index').attr('name', function () {
                return $(this).data('name').replace('x', nextIndex);
            });
            row.find('.datepicker').removeAttr('id').removeClass('hasDatepicker').datepicker({
                dateFormat: 'yy-mm-dd'
            });
            row.find('.price-format').priceFormat(priceFormatOptions);//attach price format
            row.find('input[type=hidden]').remove();
            row.appendTo('#charts tbody'); //insert
            nextIndex++;
        });
        $('#charts').on('click', '.remove-line', function () {
            var row = $(this).closest('tr');
            if ($('#charts tbody tr').index(row) === 0 && $('#charts tbody tr').length === 1) {
                row.find('input[type=hidden]').remove();
                row.find('select,input').val('');
            } else {
                row.remove();
            }
            getTotal();
        });
        $('#charts').on('keyup', '.subtotal', function () {
            getTotal();
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
                    $.growl.error({title: 'Unexpected error', message: 'Internal server error. Please try again'});
                },
                complete: function () {
                    $("[type=submit]").removeClass("disabled");
                }
            });
        });
    });
</script>