<?php $url = base_url('accounting/print_checks'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff">
                <h3 class="box-title"><?= $form_title?></h3>
            </div><!-- /.box-header -->
            <form data-action="<?= $form_action?>">
                <div class="box-body">
                    <div class="callout callout-danger hidden"><ul class="list-unstyled"></ul></div><div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Check type</label>
                                <?= form_dropdown('check_type', ['' => '', 'rcbc' => 'RCBC', 'xrcbc' => 'RCBC (Cross check)', 'mb' => 'Metrobank', 'xmb' => 'Metrobank (Cross check)'], isset($c['check_type']) ? $c['check_type'] : FALSE, 'class="form-control"')?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Pay to</label>
                                <input value="<?= isset($c['payee']) ? $c['payee'] : ''?>" type="text" class="form-control" required="required" name="payee"/>
                            </div>
                        </div>
                    </div>
                    
                    <hr/>
                    <table class="table">
                        <thead><tr><th>Bank Account</th><th>Check Number</th><th>Check Date</th><th>Amount</th></tr></thead>
                        <tbody>
                            <tr>
                                <td><?= arr_group_dropdown('bank_account', $accounts, 'id', 'bank_name', isset($c['bank_account']) ? $c['bank_account'] : FALSE, FALSE, 'class="form-control" required="required"')?></td>
                                <td><input value="<?= isset($c['check_number']) ? $c['check_number'] : ''?>" type="text" class="form-control" name="check_number" required="required"/></td>
                                <td><input value="<?= isset($c['check_date']) ? date('m/d/Y', strtotime($c['check_date'])) : '' ?>"  type="text" class="form-control datepicker" name="check_date" required="required"/></td>
                                <td><input value="<?= isset($c['amount']) ? number_format($c['amount'],2) : ''?>" type="text" class="form-control price" name="check_amount" required="required"/></td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->  
                <div class="box-footer clearfix">
                    <button type="submit" class="btn btn-success btn-flat">Submit</button>
                    <a href="<?= $url?>" class="btn btn-warning btn-flat pull-right cancel">Go back</a>
                </div><!-- /.box-body --> 
            </form>
        </div>
    </div>
</div>