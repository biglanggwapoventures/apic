<div class="row">
    <div class="col-md-10">
        <div class="row">
            <div class="col-md-12">
                <?php if ($this->session->flashdata('form_submission_success')): ?>
                    <div class="alert alert-success alert-dismissable">
                        <i class="fa fa-check"></i>
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <b>Hurray!</b> <?= $this->session->flashdata('form_submission_success') ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <form method="get" class="form-inline" action="<?= $url ?>">
                    <div class="form-group">
                        <input type="text" name="search_keyword" class="form-control pull-right"  placeholder="Customer Name" value="<?= $default_keyword ?>"/>
                    </div>
                    <button class="btn btn-default" type="submit">Search!</button>
                </form>
            </div>
            <div class="col-md-4 text-right">
                <a class="btn btn-success" href="<?= base_url('sales/receipts/add') ?>"> 
                    <i class="glyphicon glyphicon-plus"></i> Add new receipt
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info table-responsive">
                    <table class="table table-hover pm-table">
                        <thead>
                            <tr>
                                <th>S.R. No.</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Action(s)</th>
                            </tr>
                        </thead>
                        <tbody cdata-delete-url="<?= base_url('sales/receipts/a_delete') ?>">
                            <?php if (isset($entries) AND is_array($entries) AND !empty($entries)): ?>
                                <?php foreach ($entries as $e): ?>
                                    <?php $status = ''; ?>
                                    <?php $tr_class = ''; ?>
                                    <?php if ($e['status'] == M_Status::STATUS_DEFAULT OR $e['status'] == M_Status::STATUS_PENDING): ?>
                                        <?php $status = 'Waiting for admin finalization' ?>
                                        <?php $tr_class = 'warning'; ?>
                                    <?php else: ?>
                                        <?php $status = 'Finalized' ?>
                                    <?php endif; ?>
                                    <tr class="<?= $tr_class ?>" data-pk="<?= $e['id'] ?>">
                                        <td><a href="<?= base_url("sales/receipts/update/{$e['id']}") ?>"><?= str_pad($e['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                        <td><?= $e['formatted_date'] ?></td>
                                        <td><?= $e['company_name'] ?></td>
                                        <td><?= $status ?></td>
                                        <td>
                                            <a class="remove-item"><span class="badge bg-red"><i class="fa fa-times"></i></span></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center">No results found</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
    </div>

</div>