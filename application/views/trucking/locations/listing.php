<?php $url = base_url('trucking/locations'); ?>
<div class="row">
    <div class="col-md-6">
        <div class="box box-solid">
            <div class="box-header bg-light-blue-gradient" style="color:#fff">
                <h3 class="box-title">Master List</h3>
                <!-- tools box -->
                <div class="pull-right box-tools">
                    <!-- button with a dropdown -->
                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li><a href="<?= "{$url}/create"?>">Create new location</a></li>
                            <li><a data-toggle="modal" data-target="#search">Search</a></li>
                        </ul>
                    </div>                 
                </div><!-- /. tools -->
            </div><!-- /.box-header -->

            <div class="box-body no-padding">
                <table class="table table-striped"> 
                    <thead><tr class="info"><th>Location</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach($items AS $row):?>   
                            <tr data-pk="<?= $row['id']?>">
                                <td><a href="<?= "{$url}/edit/{$row['id']}"?>"><?= $row['name']?></a></td>   
                                <td>
                                    <a class="btn btn-xs btn-flat btn-danger _delete <?= can_delete($row) ? '' : 'disabled'?>"><i class="fa fa-times"></i> Delete</a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        <?php if(empty($items)):?>
                            <tr><td colspan="5" class="text-center">No data to show.</td></tr>
                        <?php endif;?>
                    </tbody>
                    <tfoot>
                        <tr class="hidden"><td id="view-more-section" colspan="8" class="text-center"><span class="notification"></span><button id="btn-view-more" class="btn btn-flat btn-xs btn-default" type="button">Click to view more</button></td></tr>
                    </tfoot>
                </table>
            </div><!-- /.box-body -->  
        </div>
    </div>
</div>

<div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="mySmallModalLabel">Confirm action</h4>
            </div>
            <div class="modal-body">
                <p class="text-danger text-center text-bold">Do you really want to delete this location?<br> <u>This action cannot be undone.<u></p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-flat btn-danger" id="delete-confirmed" data-delete-url="<?= "{$url}/delete/"?>">Yes</a>
                <a data-dismiss="modal" class="btn btn-flat btn-default">No</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="search" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="mySmallModalLabel">Search</h4>
            </div>
            <form method="GET">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" class="form-control" value="<?= $this->input->get('name')?>" name="name"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-flat btn-danger" type="submit">Search</button>
                    <a data-dismiss="modal" class="btn btn-flat btn-default">Cancel</a>
                </div>
             </form>
        </div>
    </div>
</div>