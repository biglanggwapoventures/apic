<div ng-app="provera" ng-controller="Packaging as vm" ng-init="vm.baseUrl = '<?= base_url('inventory/packaging') ?>/'; vm.isAdmin = <?= $this->session->userdata('type_id') == M_Account::TYPE_ADMIN ? 'true' : 'false'; ?>;">
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
                                <li><a ng-click="vm.openManageModal('create', null)">Add new medication</a></li>
                            </ul>
                        </div>                 
                    </div><!-- /. tools -->
                </div><!-- /.box-header -->
                <div class="box-body no-padding" style="display: block;">
                    <table class="table table-striped ">
                        <thead><tr><th>Quantity</th><th>Description</th></th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            <tr ng-repeat="p in vm.packaging">
                                <td>{{p.quantity}}</td>
                                <td>{{p.description}}</td>
                                <td><span class="label" ng-class="p.status==='Active'?'label-success':'label-warning'">{{p.status}}</span></td>
                                <td><a class="btn btn-xs btn-flat btn-info"  ng-click="vm.openManageModal('update', $index)">Update</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->  

            </div>
        </div>
    </div>
</div>