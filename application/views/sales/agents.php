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
                        <input type="text" name="search_keyword" class="form-control pull-right"  placeholder="Agent Name" value="<?= $default_keyword ?>"/>
                    </div>
                    <button class="btn btn-default" type="submit">Search!</button>
                </form>
            </div>

            <div class="col-md-4 text-right">
                <a class="btn btn-success" href="<?= base_url('sales/agents/add') ?>"> 
                    <i class="glyphicon glyphicon-plus"></i> Add new sales agent
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info table-responsive">
                    <table class="table table-hover pm-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Area</th>
                                <th>Quota per month (Units)</th>
                                <th>Quota per month (Amount)</th>
                                <th>Action(s)</th>
                            </tr>
                        </thead>
                        <tbody class="pm-tbody" data-delete-url="<?= base_url('sales/agents/a_delete') ?>">
                            <?php if (isset($entries) AND is_array($entries) AND !empty($entries)): ?>
                                <?php foreach ($entries as $e): ?>
                                    <tr data-pk="<?= $e['id'] ?>">
                                        <td><a href="<?= base_url("sales/agents/update/{$e['id']}") ?>"><?= $e['name'] ?></a></td>
                                        <td><?= $e['area'] ?></td>
                                        <td><?= $e['unit_quantity'] . ' ' . $e['unit_description'] ?></td>
                                        <td><?= number_format($e['amount'],2) ?></td>
                                        <td>
                                            <a class="tbody-item-remove"><span class="badge bg-red"><i class="fa fa-times"></i></span></a>
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
    </div>

</div>