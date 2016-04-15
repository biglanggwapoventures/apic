<input type='hidden' name='data-url-generate' value='<?= base_url('inventory/adjustments/generate') ?>'>
<input type='hidden' name='data-url-approve' value='<?= base_url('inventory/adjustments/a_do_approve') ?>'>


<div class='row'>
    <div class='col-md-9'>
        <?php if ($this->session->flashdata('form_submission_success')): ?>
            <div class="callout callout-info">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4>Hurray! <i class='fa fa-check'></i></h4> <p><?= $this->session->flashdata('form_submission_success') ?></p>
            </div>
        <?php endif; ?>
        <div class='btn-toolbar'>
            <a role='button' href='<?= base_url('inventory/adjustments/manage?do=add-new-stock-adjustment') ?>' class='btn btn-success'>
                <i class='fa fa-plus'></i> Add new stock adjustment
            </a>
        </div>
        <div>
            <div class='box box-info table-responsive'>
                <table class='table table-hover pm-table'>
                    <thead><tr><th>#</th><th>Reason</th><th>Requested by</th><th>Date</th><th >Status</th><th>Action(s)</th></tr></thead>
                    <tbody class='pm-tbody' data-delete-url="<?= base_url('inventory/adjustments/a_do_action/delete') ?>">
                        <?php if (isset($entries) && is_array($entries) && !empty($entries)): ?>

                            <?php foreach ($entries as $item): ?>
                                <?php $status = ''; ?>
                                <?php $tr_class = ''; ?>
                                <?php $is_approved = 0 ?>
                                <?php if ($item['status'] == M_Status::STATUS_DEFAULT OR $item['status'] == M_Status::STATUS_PENDING): ?>
                                    <?php $status = 'Waiting for admin approval' ?>
                                    <?php $tr_class = 'danger'; ?>
                                <?php elseif ($item['status'] == M_Status::STATUS_CANCELLED): ?>
                                    <?php $status = 'Cancelled' ?>
                                    <?php $tr_class = 'warning'; ?>
                                <?php else: ?>
                                    <?php $status = 'Approved' ?>
                                    <?php $is_approved = 1 ?>
                                <?php endif; ?>
                                <tr class="<?= $tr_class ?>" data-pk="<?= $item['id'] ?>">
                                    <td><?= $item['id'] ?></td><td><?= $item['reason'] ?></td><td><?= $item['FirstName'].' '.$item['LastName'] ?></td><td><?= date("F j, Y", strtotime($item['datetime'])); ?></td><td><?= $status ?></td>  
                                    <td>
                                        <?php $is_admin = is_admin($this->session->userdata('user_id')); ?>
                                        <a href='javascript:void(0)' style='<?= ($is_approved || !$is_admin)?"":"display:none"?>' data-pk='<?= $item['id'] ?>' title='Print' role='button' class='<?= ($is_approved || $is_admin)?"tbody-item-print":""?>'><span class='badge <?= ($is_approved)?'bg-teal':'bg-gray'; ?>'><i class='fa fa-print'></i></span></a>
                                        <?php if(!$is_approved && $is_admin): ?> 
                                            <a href='javascript:void(0)' data-pk='<?= $item['id'] ?>' class="tbody-item-approve"><span class="badge bg-green"><i class="fa fa-check"></i></span></a>
                                        <?php endif; ?>
                                        <a href='javascript:void(0)' data-pk='<?= $item['id'] ?>' class="tbody-item-view"><span class="badge bg-light-blue"><i class="fa fa-file"></i></span></a>
                                        <a class="tbody-item-remove"><span class="badge bg-red"><i class="fa fa-times"></i></span></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        <?php else: ?>
                            <tr> <td class='text-center' colspan='6'>No requests to display.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div>
    </div>
</div>