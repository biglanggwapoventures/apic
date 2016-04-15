(function (angular) {

    var app = angular.module('provera', ['ngAnimate', 'ui.bootstrap']);
    app.controller('Packaging', Medications);
    app.controller('ManageModal', ManageModal);

    Medications.$inject = ['$http', '$filter', '$modal', '$scope'];
    ManageModal.$inject = ['$http', '$filter', '$modalInstance', 'data'];

    function Medications($http, $filter, $modal, $scope) {
        var vm = this;
        vm.openManageModal = openManageModal;
        vm.initialize = initialize;

        function baseUrl(segment) {
            return vm.baseUrl + segment;
        }

        function openManageModal(mode, index) {
            var modalInstance = $modal.open({
                templateUrl: baseUrl('ajax_manage_medication_modal'),
                controller: 'ManageModal as vm',
                resolve: {
                    data: function () {
                        return {
                            mode: mode,
                            units: vm.units,
                            medication: mode === 'create' ? {status: 'Inactive'} : angular.copy(vm.medications[index]),
                            url: mode === 'create' ? baseUrl('ajax_create') : baseUrl('ajax_update'),
                            isAdmin: vm.isAdmin
                        };
                    }
                }
            });

            modalInstance.result.then(function (data) {
                if (mode === 'create') {
                    vm.medications.push(data);
                }else{
                    vm.medications[index] = data;
                }
            }, function () {
                /* handle error */
            })
        }

        function initialize() {
            var request = $http.get(baseUrl('ajax_initialize'));
            request.success(function (response) {
                if (response.error_flag === false) {
                    vm.packaging = response.data;
                }
            });
        }

        /* wait for the url to initialize from view to start initializing data */
        $scope.$watch('vm.baseUrl', function () {
            vm.initialize();
        });

    }

    /* controller for the modal instance */
    function ManageModal($http, $filter, $modalInstance, data) {
        var vm = this;
        vm.title = data.mode === 'create' ? 'Add new medication' : 'Update medication: ' + data.medication.code;
        vm.units = data.units;
        vm.medication = data.medication;
        vm.isAdmin = data.isAdmin;
        vm.submitting = false;
        vm.getUnit = getUnit;
        vm.submit = submit;
        vm.cancel = cancel;


        function cancel() {
            $modalInstance.dismiss('cancel');
        }

        function submit(isValid) {
            vm.submitting = true;
            if (isValid) {
                var request = $http({
                    method: 'POST',
                    url: data.url,
                    data: $.param(vm.medication),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                });
                request.success(function (response) {
                    if (response.error_flag) {
                        vm.validation_errors = response.data;
                        return;
                    }
                    if (data.mode === 'create') {
                        vm.medication.id = response.data.id;
                    }else{
                        if(vm.isAdmin === false){
                            vm.medication.status = 'Inactive';
                        }
                    }
                    $modalInstance.close(vm.medication);
                });
                request.finally(function () {
                    vm.submitting = false;
                });
            }
        }
        
        function getUnit(){
            var unit = $filter('filter')(vm.units, {id:vm.medication.fk_unit_id});
            vm.medication.unit_description = unit[0].description;
        }
    }

})(angular);