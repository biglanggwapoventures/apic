<div class="row" style="margin-bottom: 10px;">
    <div class="col-md-8">
        <form method="get" class="form-inline" action="<?= base_url('inventory/products') ?>">
            <div class="form-group">
                <input type="text" name="search_keyword" class="form-control pull-right"  placeholder="Product Description" value="<?= $default_keyword ?>"/>
            </div>
            <div class="form-group">
                <?= form_dropdown('product_class', $classes, $default_class, "class='form-control'"); ?>
            </div>
             <div class="form-group">
                <?= form_dropdown('status', ['active'=>'Active only', 'inactive' => 'Inactive only', 'all' => 'All',], $default_status, "class='form-control'"); ?>
            </div>
            <button class="btn btn-default" type="submit">Search!</button>
        </form>
    </div>

    <div class="col-md-4 text-right">
        <a class="btn btn-success" href="<?= base_url('inventory/products/add') ?>"> 
            <i class="glyphicon glyphicon-plus"></i> Add new product
        </a>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box box-info table-responsive">
            <table class="table  table-condensed pm-table">
                <thead>
                    <tr>
                        <th>Product Description</th>
                        <th>Product Code</th>
                         <th>Formulation Code (UPDATED)</th>
                        <th>Stock on hand</th>
                        <th>Reorder level</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="pm-inventory-tbody" data-delete-url="<?= base_url('inventory/products/a_delete') ?>">
                    <?php $reports_url = base_url('reports/product_inventory')?>
                    <?php if (!empty($entries)): ?>
                        <?php foreach ($entries as $e): ?>
                            <tr data-pk="<?= $e['id'] ?>">
                                <td>
                                    <a class="<?= $e['stock'] <= $e['reorder_level'] ? 'text-danger' : ''?>" href="<?= base_url("inventory/products/update/{$e['id']}") ?>">
                                        <?= $e['description'] ?>
                                    </a>
                                </td>
                                <td><?= $e['code'] ?></td>
                                <td><?= $e['formulation_code'] ? $e['formulation_code'] : '<em>N/A</em>'?></td>
                                <td> <?= "{$e['stock']} {$e['unit_description']}" ?></td>
                                <td><?= $e['reorder_level'] ?></td>
                                <td>
                                    <a class="btn btn-xs btn-flat btn-info" href="<?= "{$reports_url}?product_id={$e['id']}"?>">View logs</a>
                                    <a class="btn btn-xs btn-flat btn-danger tbody-item-remove">Delete</a>
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