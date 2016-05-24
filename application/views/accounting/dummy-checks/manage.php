<?php $url = base_url('accounting/dummy_checks'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff">
                <h3 class="box-title"><?= $form_title?></h3>
            </div><!-- /.box-header -->
            <form data-action="<?= $form_action?>">
                <div class="box-body">
                    <div class="callout callout-danger hidden"><ul class="list-unstyled"></ul></div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Prepare date</label>
                                <input value="<?= isset($dc['date']) ? date('m/d/Y', strtotime($dc['date'])) : date('m/d/Y')?>" type="text" class="form-control datepicker" required="required" name="date"/>
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="form-group">
                                <label>Payee</label>
                                <input value="<?= isset($dc['payee']) ? $dc['payee'] : ''?>" type="text" class="form-control" required="required" name="payee"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea class="form-control" name="remarks"><?= isset($dc['remarks']) ? $dc['remarks'] : ''?></textarea>
                    </div>
                  <div class="row">
                    <div class="col-sm-4">
                        <div class="well well-sm">
                            <?php $checked = isset($dc['crossed']) &&  (int)$dc['crossed'] ? 'checked="checked"' : '' ?>
                            <div class="checkbox"><label><input type="checkbox" name="crossed" value="1" <?=$checked?>/> <b>Cross checked?</b></label></div>
                            <?php $hide_check_date_on_print = isset($dc['print_check_date']) &&  (int)$dc['print_check_date'] ? '' : 'checked="checked"' ?>
                            <div class="checkbox"><label><input type="checkbox" name="hide_check_date_on_print" value="1" <?=$hide_check_date_on_print?>/> <b>Hide check date on print?</b></label></div>
                       </div>
                    </div>
                  </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table" style="table-layout:fixed">
                                <thead><tr class="active"><th>Bank Account</th><th>Check Number</th><th>Check Date</th><th>Amount</th></tr></thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo arr_group_dropdown('bank_account', $accounts, 'id', 'bank_name', isset($dc['bank_account']) ? $dc['bank_account'] : FALSE, FALSE, 'class="form-control" required="required"')?></td>
                                        <td><input value="<?= isset($dc['check_number']) ? $dc['check_number'] : ''?>" type="text" class="form-control" name="check_number" required="required"/></td>
                                        <td><input value="<?= isset($dc['check_date']) && $dc['check_date'] ? date_create($dc['check_date'])->format('m/d/Y') : '' ?>"  type="text" class="form-control datepicker" name="check_date"/></td>
                                        <td><input value="<?= isset($dc['check_amount']) ? number_format($dc['check_amount'],2) : ''?>" type="text" class="form-control price" name="check_amount" required="required"/></td>   
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!-- /.box-body -->  
                <div class="box-footer clearfix">
                    <button type="submit" class="btn btn-success btn-flat">Submit</button>
                    <a href="<?= $url?>" class="btn btn-warning btn-flat pull-right cancel">Go back</a>
                </div><!-- /.box-body --> 
            </form>
        </div>
    </div>
</div>

<div class="hidden" id="print-check"></div>