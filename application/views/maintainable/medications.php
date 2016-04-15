<div ng-app="provera" ng-controller="Medications as vm" ng-init="vm.baseUrl = '<?= base_url('maintainable/medications') ?>/'; vm.unitsListUrl = '<?= base_url('inventory/units') ?>/'; vm.isAdmin = <?= $this->session->userdata('type_id') == M_Account::TYPE_ADMIN ? 'true' : 'false'; ?>;">
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
                        <thead><tr><th>Description</th><th>Product Code</th><th>Unit</th><th>Status</th></tr></thead>
                        <tbody>
                            <tr ng-repeat="m in vm.medications">
                                <td>{{m.description}}</td>
                                <td>{{m.code}}</td>
                                <td>{{m.unit_description}}</td>
                                <td><span class="label" ng-class="m.status==='Active'?'label-success':'label-warning'">{{m.status}}</span></td>
                                <td><a class="btn btn-xs btn-flat btn-info"  ng-click="vm.openManageModal('update', $index)">Update</a></td></tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->  

            </div>
        </div>
    </div>
</div>