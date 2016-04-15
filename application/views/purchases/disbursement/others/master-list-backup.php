<style type="text/css">
    table.promix tbody td:nth-child(7),
    table.promix thead th:nth-child(7){
        text-align: right;
    }
    table.promix tbody td:nth-child(8),
    table.promix thead th:nth-child(8){
        text-align: center;
    }
</style>
<?php $url = base_url('purchases/other_disbursements'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff">
                <h3 class="box-title">Master List</h3>
                <!-- tools box -->
                <div class="pull-right box-tools">
                    <!-- button with a dropdown -->
                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li><a href="<?= $url . '/manage?do=new-check-voucher' ?>">Add new voucher</a></li>
                            <li><a href="#" data-toggle="modal" data-target=".advanced-search-modal">Advanced search</a></li>
                        </ul>
                    </div>                 
                </div><!-- /. tools -->
            </div><!-- /.box-header -->
            <div class="box-body no-padding" style="display: block;">
                <div class="row">
                    <div class="col-xs-12">
                        <table class="table table-striped table-condensed promix">
                            <thead>
                                <tr class="info"><th style="width: 5%;"></th><th style="width: 7%;">C.V. #</th><th style="width: 10%;">Date</th><th style="width: 20%;">Pay to</th><th>Remarks</th><th style="width: 10%;">Check Number</th><th style="width: 15%;">Total Amount</th><th style="width: 10%;">Status</th><th class="text-right"></th></tr>
                            </thead>

                            <tbody data-unlock-url="<?= base_url('purchases/disbursements/change_lock_state'); ?>" data-delete-url="<?= base_url('purchases/disbursements/a_do_action/delete') ?>">
                                <?php if (empty($entries)): ?>
                                    <tr><td colspan="8" class="text-center">No entries recorded.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($entries as $e): ?>
                                    <?php $status = ''; ?>
                                    <?php $tr_class = ''; ?>
                                    <?php if ($e['status'] == M_Status::STATUS_DEFAULT): ?>
                                        <?php $status = 'Pending approval' ?>
                                        <?php $tr_class = 'warning'; ?>
                                        <?php $label_class = 'label-warning' ?>
                                    <?php else: ?>
                                        <?php $status = 'Approved' ?>
                                        <?php $label_class = 'label-success' ?>
                                    <?php endif; ?>
                                    <tr class="<?= $tr_class ?>" data-pk="<?= $e['id'] ?>">
                                        <td>
                                            <?php if ($this->session->userdata('type_id') == M_Account::TYPE_ADMIN): ?>
                                                <?php if ((int) $e['is_locked'] === 1): ?>
                                                    <a href="javascript:void(0)" data-request="do_unlock" class="request-lock-state btn btn-flat btn-success btn-xs"><i class="fa fa-lock"></i></a>
                                                <?php else: ?>
                                                    <a href="javascript:void(0)" data-request="do_lock" class="request-lock-state btn btn-flat btn-warning btn-xs"><i class="fa fa-unlock"></i></a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><a href="<?= base_url("purchases/other_disbursements/manage?do=update-check-voucher&id={$e['id']}") ?>"><?= str_pad($e['id'], 4, 0, STR_PAD_LEFT) ?></a></td>
                                        <td><?= $e['date']; ?></td>
                                        <td><?= $e['payee']; ?></td>
                                        <td><?= $e['remarks']; ?></td>
                                        <td><?= $e['check_number']; ?></td>
                                        <td><?= number_format($e['total_amount'], 2); ?></td>
                                        <td><span class="label <?= $label_class ?>"><?= $status ?></span></td>
                                        <td>
                                            <a href="<?="{$url}/do_print?id={$e['id']}"?>" title='Print' role='button' class="btn btn-primary print-doc btn-flat btn-xs">Print</a>
                                            <?php if ($this->session->userdata('type_id') == M_Account::TYPE_ADMIN): ?>
                                                <a href="javascript:void(0)" class="remove-item btn btn-danger btn-flat btn-xs">Delete</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="hidden">
                                <tr ><td id="view-more-section" colspan="7" class="text-center"><span class="notification"></span><button id="btn-view-more" class="btn btn-flat btn-xs btn-default" type="button">Click to view more</button></td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div><!-- /.box-body -->  

        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.print-doc').printPage();
        // $('.print-doc').click(function () {
        //     var request = $.get('<?= $url ?>/do_print', {'id': $(this).closest('tr').data('pk')});
        //     request.done(function (response) {
        //         var printpage = window.open();
        //         printpage.document.write(response);
        //         printpage.document.close();
        //         printpage.focus();
        //         printpage.print();
        //         printpage.close();
        //     });
        // });
        $("a.request-lock-state").click(function () {
            var $this = $(this);
            var $request_state = $this.data("request");
            console.log($request_state);
            $.post($("tbody").data('unlock-url'), {request_state: $request_state, order_id: $this.closest("tr").data("pk")}).done(function (data) {
                if (!data.error_flag) {
                    $this.toggleClass('btn-success btn-warning');
                    $this.find('i').toggleClass('fa-unlock fa-lock');
                    $this.attr('data-request', function (index, value) {
                        return value === 'do_unlock' ? 'do_lock' : 'do_unlock';
                    });
                } else {
                    alert(data.message);
                }
            }).fail(function () {
                alert('Internal server error.');
            });
        });
        $('.remove-item').click(function () {
            var $this = $(this);
            var sure = confirm('Delete this item?');
            if (!sure) {
                return;
            }
            var deleteUrl = $("tbody").attr("data-delete-url");
            var id = $this.closest("tr").attr("data-pk");
            var request = $.post(deleteUrl, {pk: id});
            request.done(function (response) {
                if (!response.error_flag) {
                    $this.closest('tr').remove();
                    $.growl.notice({title: 'Success', message: 'Deleted C.V. #' + id});
                } else {
                    $.growl.error({title: 'Error', message: response.message});
                }
            });
            request.fail(function () {
                $.growl.error({title: 'Error', message: 'Unknown error has occured. Please try again.'});
            });

        });
    });
</script>
