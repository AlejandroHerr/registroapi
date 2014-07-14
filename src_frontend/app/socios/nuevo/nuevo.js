angular.module('libroApp.socios.nuevo', ['libroApp.modal.nuevo'])
    .controller('SociosNuevoCtrl', ['$modal', '$scope', 'Countries',
        function ($modal, $scope, Countries) {
            $scope.registrar = function () {
                if (this.nuevoSocio.$invalid) {
                    return;
                }
                var socio = this.socio;
                var modalInstance = $modal.open({
                    templateUrl: 'modal/nuevo/nuevo.tpl.html',
                    controller: 'ModalNuevoCtrl',
                    resolve: {
                        item: function () {
                            return socio;
                        },
                        url: function () {
                            return '/api/socios';
                        }
                    }
                });
                modalInstance.result.then(function () {
                    $scope.nuevoSocio.$setPristine();
                    $scope.socio = {
                        'name': '',
                        'surname': '',
                        'esncard': '',
                        'created_at': new Date()
                            .toJSON()
                            .slice(0, 10),
                        'passport': '',
                        'country': '',
                        'email': '',
                        'language': ''
                    };
                });
            };
            $scope.countries = Countries.getCountries();
            console.log($scope.countries);
            console.log();
            $scope.languages = [{
                value: 'English'
            }, {
                value: 'Espanyol'
            }];
            $scope.socio = {
                'created_at': new Date()
                    .toJSON()
                    .slice(0, 10)
            };
        }]);