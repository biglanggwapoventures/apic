<?php $url = base_url('accounting/dummy_checks'); ?>
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
                            <li><a href="#" data-toggle="modal" data-target="#advanced-search-modal">Advanced search</a></li>
                        </ul>
                    </div>                 
                </div><!-- /. tools -->
            </div><!-- /.box-header -->
            <div class="box-body no-padding" style="display: block;">
                <div class="row">
                    <div class="col-xs-12">
                        <table id="list" class="table table-striped table-condensed" data-get="<?= "{$url}/ajax_get"?>" data-update="<?= "{$url}/update/"?>" data-delete="<?= "{$url}/ajax_delete"?>" data-print="<?= "{$url}/ajax_print/"?>">
                            <thead>
                                <tr class="info">
                                    <th>DC #</th>
                                    <th>Date</th>
                                    <th>Payee</th>
                                    <th>Amount</th>
                                    <!-- <th>Status</th> -->
                                    <th>CB</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="6" class="text-center">Loading data...</td></tr>
                            </tbody>
                            <tfoot>
                                <tr ><td id="view-more-section" colspan="7" class="text-center"><span class="notification"></span><button id="btn-view-more" class="btn btn-flat btn-xs btn-default" type="button">Click to view more</button></td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div><!-- /.box-body -->  
        </div>
    </div>
</div>

<div class="modal fade" id="advanced-search-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mySmallModalLabel">Advanced search</h4>
            </div>
            <form id="advanced-search">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Check Number</label>
                        <input type="text" class="form-control" name="check_number" placeholder="Check Number">
                    </div>
                    <div class="form-group">
                        <label>Payee</label>
                        <input type="text" class="form-control" name="payee" placeholder="Payee"/>
                    </div>
                    <div class="form-group">
                        <label>Start date</label>
                        <input type="text" class="form-control datepicker" name="start_date" placeholder="Start date">
                    </div>
                    <div class="form-group">
                        <label>End date</label>
                        <input type="text" class="form-control datepicker" name="end_date" placeholder="End date">
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

<script>
    $(document).ready(function(){
        $('.datepicker').datetimepicker({format: 'MMM-DD-YYYY'});
    });
</script>