<input type="hidden" value="<?= base_url('accounting/bank_accounts/a_get') ?>" name="data-get-master-list-url" disabled="disabled">
<div class="row">
    <div class="col-md-10">
        <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-4">
                <div class="btn-toolbar">
                    <a class="btn btn-success" role="button" data-target="#add-bank-account-modal" data-toggle="modal"> 
                        <i class="glyphicon glyphicon-plus"></i> Add new bank account
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info table-responsive">
                    <table class="table table-hover pm-table">
                        <thead>
                            <tr>
                                <th>Bank</th>
                                <th>Branch</th>
                                <th>Account Number</th>
                                <th>Action(s)</th>
                            </tr>
                        </thead>
                        <tbody data-edit-url="<?= base_url('accounting/bank_accounts/a_update') ?>" data-delete-url="<?= base_url('accounting/bank_accounts/a_delete') ?>">

                        </tbody>
                    </table>

                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="add-bank-account-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= base_url('accounting/bank_accounts/a_add') ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add new bank account</h4>
                </div>
                <div class="modal-body">
                    <div class="callout callout-danger hidden">
                        <ul class="list-unstyled"><b class="text-danger">Error!</b>

                        </ul>
                    </div>
                    <div class="form-group">
                        <label for="bank-name">Bank Name</label>
                        <input required="required" type="text" name="bank_name" class="form-control" id="bank-name" placeholder="Bank name">
                    </div>
                    <div class="form-group">
                        <label for="branch">Branch</label>
                        <input required="required" type="text" name="bank_branch" class="form-control" id="branch" placeholder="Branch">
                    </div>
                    <div class="form-group">
                        <label for="account-number">Account Number</label>
                        <input required="required" type="text" name="account_number" class="form-control" id="account-number" placeholder="Account number">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>