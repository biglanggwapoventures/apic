<?php $url = base_url('production/formulations'); ?>
<div class="box box-solid">
    <div class="box-header bg-light-blue-gradient" style="color:#fff">
        <h3 class="box-title">Master List</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
            <!-- button with a dropdown -->
            <div class="btn-group">
                <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li><a href="<?= $url . '/create' ?>">Add new formulation</a></li>
                    <li><a href="#" data-toggle="modal" data-target=".advanced-search-modal">Advanced search</a></li>
                </ul>
            </div>                 
        </div><!-- /. tools -->
    </div><!-- /.box-header -->
    <div class="box-body no-padding" style="display: block;">
        <div class="row">
            <div class="col-xs-12">

                <table id="master-list" class="table table-striped table-condensed promix" 
                        data-edit-url="<?= $url . '/edit/' ?>"
                       data-master-list-url='<?= $url . '/master_list' ?>'
                       data-delete-url='<?= $url . '/delete' ?>'>
                    <thead>
                        <tr class="info">
                            <th>Formulation code</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="9" class="text-center">Loading data. Please wait...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- /.box-body -->  
</div>

<div class="modal fade advanced-search-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mySmallModalLabel">Advanced search</h4>
            </div>
            <form id="advanced-search">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Formulation Code</label>
                        <input type="text" class="form-control" name="formulation_code">
                    </div>
                    <div class="form-group">
                        <label>Show only</label>
                        <?= form_dropdown('status', ['a' => 'Active only', 'ia' => 'Inactive only', '' => 'All status'], 'a', 'class="form-control"')?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
    </div>
</div>