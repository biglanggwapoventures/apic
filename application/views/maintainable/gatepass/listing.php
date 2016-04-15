<?php $url = base_url('maintainable/gatepass'); ?>
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
                            <li><a href="<?= "{$url}/create" ?>">Add new dummy check</a></li>
                            <li><a onclick="alert('Coming soon...');" data-toggle="modal" data-target=".advanced-search-modal">Advanced search</a></li>
                        </ul>
                    </div>                 
                </div><!-- /. tools -->
            </div><!-- /.box-header -->
            <div class="box-body no-padding" style="display: block;">
                <div class="row">
                    <div class="col-xs-12">
                        <table id="list" class="table table-striped table-condensed" data-print="<?= "{$url}/ajax_print/"?>" data-get="<?= "{$url}/ajax_get"?>" data-update="<?= "{$url}/update/"?>" data-delete="<?= "{$url}/ajax_delete"?>">
                            <thead>
                                <tr class="info">
                                    <th>GP #</th>
                                    <th>Date & time</th>
                                    <th>Type</th>
                                    <th>Issued for</th>
                                    <th>Created by</th>
                                    <th></th>  
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="5" class="text-center">Loading data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- /.box-body -->  
        </div>
    </div>
</div>