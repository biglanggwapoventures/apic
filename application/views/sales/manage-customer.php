<div class="row">
    <div class="col-md-9">
        <div class="box box-success">
            <form class="pm-inventory-form"  method="post" action="<?= $url ?>">
                <div class="box-header">
                    <h3 class="box-title"><?= $form_title ?></h3>
                </div>
                <div class="box-body">
                    <?php if (isset($validation_errors)): ?>
                        <div class="callout callout-danger">
                            <h4>Errors!</h4>
                            <ul class="list-unstyled"><?= $validation_errors ?></ul>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($form_submission_success)): ?>
                        <div class="callout callout-info">
                            <h4>Success</h4>
                            <ul class="list-unstyled"><?= $form_submission_success ?></ul>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-6">

                            <?= form_open($url, array('role' => 'form')) ?>
                            <div class="form-group">
                                <label for="customer-code">Customer Code</label>
                                <?= form_input(array('name' => 'customer_code', 'class' => 'form-control', 'id' => 'customer-code', 'required' => 'required', 'value' => $defaults['customer_code'])); ?>
                            </div>  
                            <div class="form-group">
                                <label for="company-name">Company Name</label>
                                <?= form_input(array('name' => 'company_name', 'class' => 'form-control', 'id' => 'company-name', 'required' => 'required', 'value' => $defaults['company_name'])); ?>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <?= form_input(array('name' => 'address', 'class' => 'form-control', 'id' => 'address', 'required' => 'required', 'value' => $defaults['address'])); ?>
                            </div>
                            <div class="form-group">
                                <label for="contact-number">Contact Number</label>
                                <?= form_input(array('name' => 'contact_number', 'class' => 'form-control', 'id' => 'contact-number', 'required' => 'required', 'value' => $defaults['contact_number'])); ?>
                            </div>
                            <div class="form-group">
                                <label for="contact-person">Contact Person</label>
                                <?= form_input(array('name' => 'contact_person', 'class' => 'form-control', 'id' => 'contact-person', 'required' => 'required', 'value' => $defaults['contact_person'])); ?>
                            </div>
                            <div class="form-group">
                                <label for="credit-limit">Credit Limit</label>
                                <?= form_input(array('name' => 'credit_limit', 'class' => 'form-control', 'id' => 'credit-limit', 'required' => 'required', 'value' => $defaults['credit_limit'])); ?>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Credit Terms</label>
                                    <?php $credit_term = isset($defaults['credit_term']) ? $defaults['credit_term'] : 0 ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control" id="ct-id" name="credit_terms" required="required">
                                                    <option value="1">COD</option>
                                                    <option value="0" <?= $credit_term > 0 ? 'selected="selected"' : '' ?>>Days</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input type="number" name="credit_term" id="ct-value" value="<?= isset($defaults['credit_term']) ? $defaults['credit_term'] : "" ?>" class="form-control" id="credit-term-days">
                                                    <div class="input-group-addon">days</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="well well-sm">
                                <div class="row">
                                    <div class="col-md-10">
                                        <p class="text-success" style="text-decoration: underline">OTHER INFORMATION</p>
                                        <div class="form-group">
                                            <label for="tin-number">TIN Number</label>
                                            <?= form_input(array('name' => 'tin_number', 'class' => 'form-control', 'id' => 'tin-number', 'value' => $defaults['tin_number'])); ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="fax-number">Fax Number</label>
                                            <?= form_input(array('name' => 'fax_number', 'class' => 'form-control', 'id' => 'fax-number', 'value' => $defaults['fax_number'])); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="text-success" style="text-decoration: underline">BANK INFORMATION</p>
                                        <table class="table table-condensed table-hover no-border" id="bank-accounts-group">
                                            <thead>
                                            <th>Bank Name</th>
                                            <th>Bank Account Number</th>
                                            <th>    &nbsp;</th>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-left">
                                                        <button id="bank-account-add" type="button" class="btn btn-info btn-sm"><i class="fa fa-plus"></i> Add line</button>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                            <tr id="template">
                                                <td>
                                                    <?= form_input(array('name' => 'bank[name][]', 'class' => 'form-control input-sm', 'value' => $defaults['bank']['name'][0])); ?>
                                                </td>
                                                <td>
                                                    <?= form_input(array('name' => 'bank[account_number][]', 'class' => 'form-control input-sm', 'value' => $defaults['bank']['account_number'][0])); ?>
                                                </td><td>
                                                </td>
                                            </tr>
                                            <?php for ($x = 1; $x < count($defaults['bank']['name']); $x++): ?>
                                                <tr>
                                                    <td>
                                                        <?= form_input(array('name' => 'bank[name][]', 'class' => 'form-control input-sm', 'value' => $defaults['bank']['name'][$x])); ?>
                                                    </td>
                                                    <td>
                                                        <?= form_input(array('name' => 'bank[account_number][]', 'class' => 'form-control input-sm', 'value' => $defaults['bank']['account_number'][$x])); ?>
                                                    </td><td><button type="button" class="btn btn-danger btn-sm bank-account-remove"><i class="fa fa-times"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endfor; ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <?= form_button(array('type' => 'submit', 'name' => 'status', 'value' => M_Status::STATUS_DEFAULT, 'class' => 'btn btn-primary', 'content' => 'Save')); ?>
                    <?php if ($this->session->userdata('type_id') == M_Account::TYPE_ADMIN): ?>
                        <?php if ($defaults['status'] == M_Status::STATUS_PENDING || $defaults['status'] == M_Status::STATUS_DEFAULT ||  $defaults['status'] == M_Status::STATUS_NONE): ?>
                            <?= form_button(array('type' => 'submit', 'name' => 'status', 'value' => M_Status::STATUS_APPROVED, 'class' => 'btn btn-success', 'content' => 'Save and approve')); ?>
                        <?php elseif ($defaults['status'] == M_Status::STATUS_APPROVED): ?>
                            <?= form_button(array('type' => 'submit', 'name' => 'status', 'value' => M_Status::STATUS_PENDING, 'class' => 'btn btn-warning', 'content' => 'Save and disapprove')); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <a href="<?= base_url('sales/customers') ?>" class="btn btn-danger pull-right">Go back</a>
                </div>
                <?= form_close() ?> 
            </form>
        </div>
    </div>
</div>

