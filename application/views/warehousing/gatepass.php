<input type='hidden' name='data-url-for-printing' value='<?= base_url('warehousing/gatepass/generate') ?>'>
<div class='row'>
    <div class='col-md-9'>
        <?php if ($this->session->flashdata('form_submission_success')): ?>
            <div class="callout callout-info">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4>Hurray! <i class='fa fa-check'></i></h4> <p><?= $this->session->flashdata('form_submission_success') ?></p>
            </div>
        <?php endif; ?>
        <div class='btn-toolbar'>
            <a role='button' href='<?= base_url('warehousing/gatepass/manage?do=add-new-gatepass') ?>' class='btn btn-success'>
                <i class='fa fa-plus'></i> Add new gatepass 
            </a>
        </div>
        <div class='box box-info'>
            <div class='box-body no-padding'>
                <table class='table table-hover'>
                    <thead><tr><th>#</th><th>Delivery Date</th><th>Delivered by</th><th>Plate Number</th><th>Action(s)</th></tr></thead>
                    <tbody>
                        <?php if (isset($entries) && is_array($entries) && !empty($entries)): ?>

                            <?php foreach ($entries as $item): ?>
                                <tr>
                                    <td><?= $item['id'] ?></td><td><?= $item['exit_datetime'] ?></td><td><?= $item['trucking_name'] ?></td><td><?= $item['plate_number'] ?></td>  <td>
                                        <a href='javascript:void(0)' data-pk='<?= $item['id'] ?>' title='Print' role='button' class='print-doc'>
                                            <span class='badge bg-teal'><i class='fa fa-print'></i></span>
                                        </a>
                                        <a href='javascript:void(0)' title='Delete' role='button'><span class='badge bg-red'><i class='fa fa-times'></i></span></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        <?php else: ?>
                            <tr> <td class='text-center' colspan='5'>No items to display.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div>
    </div>
</div>