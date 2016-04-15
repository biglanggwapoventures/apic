<input type='hidden' name='data-url-for-printing' value='<?= base_url('sales/deliveries/do_print') ?>'>
<div class="row">
    <div class="col-md-12">
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
            <div class="col-xs-8">
                <form method="get" class="form-inline" action="<?= $url ?>">
                    <div class="form-group">
                        <input type="hidden" name="data-list-customers-url" value="<?= base_url('sales/customers/a_get') ?>" disabled="disabled" />
                        <select class="form-control" name="search_category">
                            <option value="date">Date</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" name="search_keyword" class="form-control datepicker" value="<?= $default_keyword ?>"/>
                    </div>
                    <button class="btn btn-default" type="submit">Search!</button>
                </form>
            </div>
            <div class="text-right col-xs-4">
                <a class="btn btn-success" href="<?= base_url('sales/deliveries/add') ?>"> 
                    <i class="glyphicon glyphicon-plus"></i> Add new packing list
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12"> 
                <div class="box box-info table-responsive">
                    <table class="table table-striped pm-table">
                        <thead>
                            <tr>
                                <th>P.L. No.</th>
                                <th>S.O. No.</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th></th>  
                            </tr>
                        </thead>
                        <tbody class="pm-tbody" data-delete-url="<?= base_url('sales/deliveries/a_delete') ?>">
                            <?php if (isset($entries) AND is_array($entries)): ?>
                                <?php foreach ($entries as $e): ?>
                                    <?php $approved = $e['status'] == M_Status::STATUS_DELIVERED?>
                                    <?php $status = ''; ?>
                                    <?php $tr_class = ''; ?>
                                    <?php if (!$approved): ?>
                                        <?php $status = 'Waiting to be delivered' ?>
                                        <?php $tr_class = 'danger'; ?>
                                    <?php else: ?>
                                        <?php $status = 'Approved' ?>
                                    <?php endif; ?>
                                    <tr class="<?= $tr_class ?>" data-pk="<?= $e['id'] ?>">
                                        <td><a href="<?= base_url("sales/deliveries/update/{$e['id']}") ?>"><?= str_pad($e['id'], 4, "0", STR_PAD_LEFT) ?></a></td>
                                        <td><a href="<?= base_url("sales/orders/update/{$e['fk_sales_order_id']}") ?>"><?= str_pad($e['fk_sales_order_id'], 4, "0", STR_PAD_LEFT) ?></td>
                                        <td><?= date("F j, Y", strtotime($e['date'])); ?></td>
                                        <td><?= $e['company_name'] ?></td>
                                        <td><?= $status ?></td>
                                        <td>
                                            <?php if($approved):?>
                                            <a data-pk="<?= $e['id'] ?>" class="print-doc btn btn-primary btn-xs btn-flat" href="">Print</a>
                                            <?php else:?>
                                            <a href="javascrip:void(0);" class="disabled btn btn-primary btn-xs btn-flat">Print</a>
                                            <?php endif;?>
                                            <a class="text-danger remove-item btn btn-danger btn-xs btn-flat">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center">No results found</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
    </div>

</div>
<pre>
    <?php print_r($master_list)?>
</pre>