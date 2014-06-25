angular.module('libroApp.socios.nuevo', [])
    .config(['$stateProvider',
        function ($stateProvider) {
            $stateProvider
                .state('logged.socios.nuevo', {
                    url: '/nuevo',
                    templateUrl: 'socios/nuevo/nuevo.tpl.html',
                    controller: 'SociosNuevoCtrl'
                });
        }
    ])
    .controller('SociosNuevoCtrl', ['loader', 'ApiCaller', '$modal', 'credentials', '$http', '$scope', '$state', 'countries',
        function (loader, ApiCaller, $modal, credentials, $http, $scope, $state, countries) {
            $scope.registrar = function () {
                if (this.nuevoSocio.$invalid) {
                    return;
                }
                var socio = this.socio;
                var modalInstance = $modal.open({
                    templateUrl: 'modal/registrar.tpl.html',
                    controller: 'RegistrarModalInstanceCtrl',
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
            $scope.countries = countries.get()
                .countries;
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
        }
    ]);