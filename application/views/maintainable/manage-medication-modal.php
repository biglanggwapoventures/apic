<form role="form" name="medicationForm" ng-submit="vm.submit(medicationForm.$valid)" novalidate>
    <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">{{vm.title}}</h4>
    </div>
    <div class="modal-body">
        <div class="callout callout-danger" ng-if="vm.hasOwnProperty('validation_errors')">
            <h4>Validation errors</h4>
            <ul class="list-unstyled">
                <li ng-repeat="err in vm.validation_errors">{{err}}</li>
            </ul>
        </div>
        <div class="form-group" ng-class="{'has-error':medicationForm.code.$touched && medicationForm.code.$invalid}">
            <label for="code">Item Code</label>
            <input type="text" ng-model="vm.medication.code"  class="form-control" name="code" ng-required="true" ng-readonly="vm.submitting"/>
        </div>
        <div class="form-group" ng-class="{'has-error':medicationForm.description.$touched && medicationForm.description.$invalid}">
            <label for="description">Item Description</label>
            <input type="text" ng-model="vm.medication.description"  class="form-control" name="description" ng-required="true"  ng-readonly="vm.submitting"/>
        </div>
        <div class="form-group" ng-class="{'has-error':medicationForm.unit.$touched && medicationForm.unit.$invalid}">
            <label for="unit">Unit</label>
            <select id="unit" ng-model="vm.medication.fk_unit_id" class="form-control" name="unit" ng-options="u.id as u.description for u in vm.units" ng-required="true"  ng-readonly="vm.submitting" ng-change="vm.getUnit()"></select>
        </div>
        <div class="form-group" ng-class="{'has-error':medicationForm.status.$touched && medicationForm.status.$invalid}" ng-if="vm.isAdmin">
            <label for="status">Item Status</label>
            <select id="status" ng-model="vm.medication.status" class="form-control" name="status" ng-required="true"  ng-readonly="vm.submitting">
                <option value="Active">Active</option><option value="Inactive">Inactive</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat" ng-click="vm.cancel()"  ng-disabled="vm.submitting">Close</button>
        <button type="submit" class="btn btn-primary btn-flat"  ng-disabled="vm.submitting">Submit</button>
    </div>
</form>
