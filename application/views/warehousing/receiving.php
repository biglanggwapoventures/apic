<?php $url = base_url('warehousing/receiving'); ?>
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
                            <li><a href="#" data-toggle="modal" data-target=".advanced-search-modal">Advanced search</a></li>
                        </ul>
                    </div>                     
                </div>
                <h3 class="box-title">
                    Master List
                </h3>
            </div>

            <div class="box-body no-padding">
                
                <table id="jo-master-list" class="table table-striped table-condensed promix" data-edit-url='<?= $url . '/update/' ?>' 
                       data-master-list-url='<?= $url . '/ajax_master_list' ?>'
                       data-delete-url='<?= $url . '/ajax_delete/' ?>'>
                    <thead><tr class="info"><th>J.O. No.</th><th>Production Code</th><th>Started</th><th>Status</th><th>&nbsp;</th></tr></thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        <tr><td id="view-more-section" colspan="7" class="text-center"><span class="notification"></span><button id="btn-view-more" class="btn btn-flat btn-xs btn-default" type="button">Click to view more</button></td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
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
                                <label class="sr-only" for="search-jo">J.O. #</label>
                                <input type="number" class="form-control" name="jo" id="search-jo" placeholder="J.O. #">
                            </div>
                    <div class="form-group">
                        <label class="sr-only" for="production-code">Production Code</label>
                        <input type="number" class="form-control" name="production_code" id="production-code" placeholder="Production Code">
                    </div>
                    <div class="form-group">
                        <label class="sr-only" for="search-date">Date range</label>
                        <input type="text" class="form-control" name="date" id="daterangepicker" placeholder="Date">
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
