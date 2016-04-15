<div class="row">
    <div class="col-md-7">
        <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-6">
                <div class="btn-toolbar">
                    <a class="btn btn-success" role="button" data-target="#trucking-modal" data-toggle="modal"> 
                        <i class="glyphicon glyphicon-plus"></i> Add new trucking
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Trucking Name</th>
                                    <th>Driver</th>
                                    <th>Plate No.</th>
                                    <th>Action(s)</th>
                                </tr>
                            </thead>
                            <tbody class="pm-inventory-tbody" data-edit-url="<?= base_url('sales/trucking/a_update') ?>" data-delete-url="<?= base_url('sales/trucking/a_delete') ?>">
                                <?php if (isset($entries) AND is_array($entries)): ?>
                                    <?php foreach ($entries as $e): ?>
                                        <tr data-pk="<?= $e['id'] ?>">
                                            <td><a data-name="trucking_name" class="editable"><?= $e['trucking_name'] ?></a></td>
                                            <td><a data-name="driver" class="editable"><?= $e['driver'] ?></a></td>
                                            <td><a data-name="plate_number" class="editable"><?= $e['plate_number'] ?></a></td>
                                            <td><a href="javascript:void(0)" class="remove-item"><span class="badge bg-red"><i class="fa fa-times"></i></span></a></td>
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
        <div class="modal fade" id="trucking-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" action="<?= base_url('sales/trucking/a_add') ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title" id="myModalLabel">Add trucking</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="trucking-name">Trucking Name</label>
                                <input required="required" type="text" name="trucking_name" class="form-control" id="trucking-name" placeholder="Trucking name">
                            </div>
                            <div class="form-group">
                                <label for="driver">Driver</label>
                                <input type="text" name="driver" class="form-control" id="driver" placeholder="Driver">
                            </div>
                            <div class="form-group">
                                <label for="plate-number">Plate #</label>
                                <input type="text" name="plate_number" class="form-control" id="plate-number" placeholder="Plate Number">
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