angular.module('libroApp.nuevo', [])
    .config(['$stateProvider',
        function ($stateProvider) {
            $stateProvider
                .state('nuevoSocio', {
                    url: '/socio/nuevo',
                    views: {
                        'content': {
                            templateUrl: 'nuevo/nuevo.tpl.html',
                            controller: 'NuevoSocioCtrl'
                        },
                        'navbar': {
                            templateUrl: 'navbar/navbar.tpl.html'
                        }
                    }
                });
        }
    ])
    .controller('NuevoSocioCtrl', ['loader', 'ApiCall', '$modal', 'credenciales', '$http', '$scope', '$state', 'countries',
        function (loader, ApiCall, $modal, credenciales, $http, $scope, $state, countries) {
            if (!credenciales.isLogged()) {
                $state.go('logout', {}, {
                    location: true
                });
                return;
            }
            loader.setLoading();
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
            $scope.registrar = function () {
                if (this.nuevoSocio.$invalid) {
                    return;
                }
                var socio = this.socio;
                var modalInstance = $modal.open({
                    templateUrl: 'modal/registrar.tpl.html',
                    controller: 'RegistrarModalInstanceCtrl',
                    resolve: {
                        socio: function () {
                            return socio;
                        }
                    }
                });
                modalInstance.result.then(function () {
                    $scope.nuevoSocio.$setPristine();
                    $scope.socio = {
                        'nombre': '',
                        'apellido': '',
                        'esncard': '',
                        'created_at': new Date()
                            .toJSON()
                            .slice(0, 10),
                        'passport': '',
                        'pais': '',
                        'email': '',
                        'language': ''
                    };
                });
            };
            loader.unsetLoading();
        }
    ]);