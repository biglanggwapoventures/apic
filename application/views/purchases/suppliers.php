<input type="hidden" name="data-get-master-list-url" value="<?= base_url('purchases/suppliers/a_get') ?>"/>
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
                            <li><a data-toggle="modal" data-target="#supplier-modal" href="#">Add new supplier</a></li>
                        </ul>
                    </div>                 
                </div><!-- /. tools -->
            </div><!-- /.box-header -->
            <div class="box-body no-padding" style="display: block;">
                <table class="table table-hover pm-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>TIN Number</th>
                            <th>Address</th>
                            <th>Contact Person</th>
                            <th>Contact Number</th>
                            <th>Action(s)</th>  
                        </tr>
                    </thead>
                    <tbody data-edit-url="<?= base_url('purchases/suppliers/a_do_action/update') ?>" data-delete-url="<?= base_url('purchases/suppliers/a_do_action/delete') ?>">

                    </tbody>
                </table>
            </div><!-- /.box-body -->  

        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="supplier-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= base_url('purchases/suppliers/a_do_action/add') ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add new supplier</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input required="required" type="text" name="name" class="form-control" id="trucking-name" placeholder="Supplier Name">
                    </div>
                    <div class="form-group">
                        <label for="tin-no">TIN No.</label>
                        <input type="text" name="tin_number" class="form-control" id="tin-no" placeholder="TIN No.">
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" name="address" class="form-control" id="driver" placeholder="Address">
                    </div>
                    <div class="form-group">
                        <label for="contact-person">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control" id="ontact-person" placeholder="Contact Person">
                    </div>
                    <div class="form-group">
                        <label for="contact-number">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control" id="ontact-number" placeholder="Contact Number">
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