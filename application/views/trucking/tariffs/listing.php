<?php $url = base_url('trucking/tariffs'); ?>
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
                            <li><a href="<?= "{$url}/create"?>">Create new tariff</a></li>
                            <li><a data-toggle="modal" data-target="#search">Search</a></li>
                        </ul>
                    </div>                 
                </div><!-- /. tools -->
            </div><!-- /.box-header -->
    <div class="box-body no-padding">
                <table class="table table-striped"> 
                    <thead><tr class="info"><th>Tariff code</th><th>Option</th><th>Location </th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach($items AS $row):?>   
                            <tr data-pk="<?= $row['id']?>">

                                <td><a href="<?= "{$url}/edit/{$row['id']}"?>"><?= $row['code']?></a></td>
                                <td><?php if($row['option']==1) echo "Origin";else echo "Destination"; ?></td> 
                                <td><?= $row['location_tariff']?></td>  
                                <td><?php if(!empty($row['approved_by'])) echo '<span class="label label-success">Approved</span>'; else echo '<span class="label label-warning">Pending Approval</span>';?></td> 
                                <td>
                                    <a class="btn btn-xs btn-flat btn-danger toDelete <?= is_approved($row) ? '' : 'disabled'?>"><i class="fa fa-times"></i> Delete</a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        <?php if(empty($items)):?>
                            <tr><td colspan="5" class="text-center">No data to show.</td></tr>
                        <?php endif;?>
                    </tbody>



                    
                </table>
            </div> 
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
                <p class="text-danger text-center text-bold">Do you really want to delete this tariff?<br> <u>This action cannot be undone.<u></p>
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
                        <label>Code</label>
                        <input type="text" class="form-control" value="<?= $this->input->get('code')?>" name="code"/>
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <?php $type_default = $this->input->get('location') ? $this->input->get('location') : 'all';?>
                         <?= form_dropdown('location', ['' => ''] + array_column($locations, 'name', 'id'),  $type_default, 'class="form-control"')?>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <?php $option_default = $this->input->get('option') ? $this->input->get('option') : '';?>
                        <?=option_dropdown('option', $option_default, 'class="form-control option"')?>
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

