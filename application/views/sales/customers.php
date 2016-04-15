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
                            <li><a href="<?=base_url('sales/customers/add')?>" >Add new customer</a></li>
                            <li><a onclick="javascript:alert('Coming soon...')">Advanced search</a></li>
                        </ul>
                    </div>                 
                </div><!-- /. tools -->
            </div><!-- /.box-header -->
            <table class="table table-hover pm-table">
                <thead>
                    <tr class="info">
                        <th>Customer Code</th>
                        <th>Company Name</th>
                        <th>Contact Person</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="pm-tbody" data-delete-url="<?= base_url('sales/customers/a_delete') ?>">
                    <?php if (isset($entries) AND is_array($entries) AND ! empty($entries)): ?>
                        <?php foreach ($entries as $e): ?>
                            <?php $status = ''; ?>
                            <?php $tr_class = ''; ?>
                            <?php if ($e['status'] == M_Status::STATUS_APPROVED): ?>
                                <?php $status = 'Approved' ?>
                            <?php else: ?>
                                <?php $status = 'Inactive' ?>
                                <?php $tr_class = 'warning'; ?>
                            <?php endif; ?>
                            <tr class="<?= $tr_class ?>" data-pk="<?= $e['id'] ?>">
                                <td><a href="<?= base_url("sales/customers/update/{$e['id']}") ?>"><?= $e['customer_code'] ?></a></td>
                                <td><?= $e['company_name'] ?></td>
                                <td><?= $e['contact_person'] ?></td>
                                <td><?= $status ?></td>
                                <td class="text-right">
                                    <a data-target="#statement-setup" data-toggle="modal" class="btn btn-info btn-flat btn-xs print-statement">Print statement</a>
                                    <a href="<?= base_url("sales/customers/show_pricing/{$e['id']}") ?>" class="btn btn-primary btn-flat btn-xs">Price list</a>
                                    <a class="tbody-item-remove btn btn-danger btn-flat btn-xs">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">No results found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<!-- Modal -->
<div class="modal fade" id="statement-setup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Select date range</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="customer" id="statement-customer" value="">
                <div class="form-group">
                    <label for="start-date">Start date</label>
                    <input type="text" class="form-control datepicker" id="start-date" name="start_date" placeholder="Start date">
                </div>
                <div class="form-group">
                    <label for="end-date">End date</label>
                    <input type="text" class="form-control datepicker" id="end-date" name="end_date" placeholder="End date">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
                <a role="button"  id="generate-statement" class="btn btn-primary btn-flat">Generate</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#generate-statement').printPage({
            params: {
                customer: function(){
                    return $('#statement-customer').val();
                },
                start_date: function(){
                    return $('#start-date').val()
                },
                end_date: function(){
                    return $('#end-date').val()
                }
            },
            url: '<?= base_url("sales/customers/ajax_statement_of_accounts")?>',
            callback: function(){
                $('#statement-setup').modal('hide')
            }
        });
        $(".print-statement").click(function () {
            var customer = $(this).closest('tr').data('pk');
            $('#statement-setup #statement-customer').val(customer);
        });
        $('.datepicker').datepicker({dateFormat:'yy-mm-dd'});
    });
</script>