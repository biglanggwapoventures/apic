<input type='hidden' name='data-url-for-printing' value='<?= base_url('sales/counters/do_print') ?>'>
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
            <div class="col-md-8 invisible">
                <form method="get" class="form-inline" action="<?= $url ?>">
                    <div class="form-group">
                        <input type="text" name="search_keyword" class="form-control"  placeholder="Customer Name" value="<?= $default_keyword ?>"/>
                    </div>
                    <button class="btn btn-default" type="submit">Search!</button>
                </form>
            </div>
            <div class="col-md-4 text-right">
                <a class="btn btn-success" href="<?= base_url('sales/counters/add') ?>"> 
                    <i class="glyphicon glyphicon-plus"></i> Add new counter receipt
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info table-responsive">
                    <table class="table table-hover pm-table">
                        <thead>
                            <tr>
                                <th>C.R. No.</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Action(s)</th>
                            </tr>
                        </thead>
                        <tbody cdata-delete-url="<?= base_url('sales/counters/a_delete') ?>">
                            <?php if (isset($entries) AND is_array($entries) AND ! empty($entries)): ?>
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
                                        <td><a href="<?= base_url("sales/counters/update/{$e['id']}") ?>"><?= $e['id'] ?></td>
                                        <td><?= date("F j, Y", strtotime($e['date'])); ?></td>
                                        <td><?= $e['company_name'] ?></td>
                                        <td><?= $status ?></td>
                                        <td>
                                            <?php if (!$e['is_printed'] && $e['status'] == M_Status::STATUS_FINALIZED): ?>
                                                <a href='javascript:void(0)' data-pk='<?= $e['id'] ?>' title='Print' role='button' class='print-doc'>
                                                    <span class='badge bg-teal'><i class='fa fa-print'></i></span>
                                                </a>
                                            <?php else: ?>
                                                <a href='javascript:void(0)' data-pk='<?= $e['id'] ?>' title='Print' role='button'>
                                                    <span class='badge bg-gray'><i class='fa fa-print'></i></span>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ((int) $this->session->userdata('type_id') === (int) M_Account::TYPE_ADMIN): ?>
                                                <?php if ((int) $e['is_locked'] === 1): ?>
                                                    <a href="javascript:void(0)" data-request="do_unlock" class="request-lock-state"><span class="badge bg-green"><i class="fa fa-unlock"></i></span></a>
                                                <?php else: ?>
                                                    <a href="javascript:void(0)"  data-request="do_lock" class="request-lock-state"><span class="badge bg-orange"><i class="fa fa-lock"></i></span></a>
                                                <?php endif; ?>
                                                <a href="javascript:void(0)" class="remove-item"><span class="badge bg-red"><i class="fa fa-times"></i></span></a>
                                            <?php endif; ?>
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