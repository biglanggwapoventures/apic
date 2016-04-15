<?php $url = base_url('production/receiving'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header  bg-light-blue-gradient" style="color:#FFFFFF!important">
                <!-- tools box -->
                <div class="pull-right box-tools">
                    <!-- button with a dropdown -->
                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li><a href="<?= "{$url}/create"?>">Receive new products</a></li>
                            <li><a href="#" data-toggle="modal" data-target=".advanced-search-modal">Advanced search</a></li>
                        </ul>
                    </div>                     
                </div>
                <h3 class="box-title">
                    Master List
                </h3>
            </div>

            <div class="box-body no-padding">
                
                <table id="rr-master-list" class="table table-striped table-condensed promix" data-master-list-url='<?= $url . '/ajax_master_list' ?>' data-update-url='<?= $url . '/update/' ?>' data-delete-url='<?= $url . '/ajax_delete/' ?>'>
                    <thead><tr class="info"><th>RR #</th><th>Date &amp; time</th><th>Production Code</th><th>Status</th><th>&nbsp;</th></tr></thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>