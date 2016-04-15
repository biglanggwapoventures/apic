<div class="row">
    <div class="col-md-7">
        <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-6">
                <div class="btn-toolbar">
                    <a class="btn btn-success" role="button" data-target=".pm-inventory-modal" data-toggle="modal"> 
                        <i class="glyphicon glyphicon-plus"></i> Add new product type
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <form method="get" action="<?= base_url('inventory/types') ?>">
                    <div class="input-group">
                        <input type="text" name="search_keyword" class="form-control pull-right" style="width: 150px;" placeholder="Search" value="<?=$default_keyword?>"/>
                        <div class="input-group-btn">
                            <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">

                <div class="box box-info">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover pm-table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Action(s)</th>
                                </tr>
                            </thead>
                            <tbody class="pm-inventory-tbody" data-edit-url="<?= base_url('inventory/types/a_update') ?>" data-delete-url="<?= base_url('inventory/types/a_delete') ?>">
                                <?php if (isset($entries) AND is_array($entries)): ?>
                                    <?php foreach ($entries as $e): ?>
                                        <tr>
                                            <td><a class="tbody-item" data-pk="<?= $e['id'] ?>"><?= $e['description'] ?></a></td>
                                            <td><a class="tbody-item-remove"><i class="fa fa-times"></i> Remove</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade pm-inventory-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form class="pm-inventory-form" method="post" action="<?= base_url('inventory/types/a_add') ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title" id="myModalLabel">Add a unit</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="unit-description">Unit description</label>
                                <input required="required" type="text" name="description" class="form-control" id="unit-description" placeholder="Unit description">
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
    </div>
</div>