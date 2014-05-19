angular.module('libroApp.socio', [])
    .config(['$stateProvider',
        function ($stateProvider) {
            $stateProvider
                .state('logged.socio.socio', {
                    url: '/socio/:socioId/{mode:.*}',
                    templateUrl: 'socio/socio.tpl.html',
                    controller: 'SocioCtrl'

                });
        }
    ])
    .controller('SocioCtrl', ['$stateParams', 'ApiCall', '$scope', 'credenciales', '$state', '$http', '$filter', 'loader', 'countries',
        function ($stateParams, ApiCall, $scope, credenciales, $state, $http, $filter, loader, countries) {
            loader.setLoading();
            $scope.setPais = function () {
                if ($scope.paises.length) {
                    var selected = $filter('filter')($scope.paises, {
                        alpha2: $scope.socio.pais
                    });
                    return selected.length ? selected[0].name : 'Not set';
                } else {
                    return $scope.socio.pais;
                }
            };
            $scope.loadSocio = function (id) {
                var path = '/api/socios/' + id;
                var data = ApiCall.makeCall(credenciales.getXWSSE(), 'GET', path, null)
                    .then(function (d) {
                        $scope.socio = d.data;
                        $scope.pais = $scope.setPais();
                        if ($stateParams.mode) {
                            if ($stateParams.mode == 'edit') {
                                setTimeout(function () {
                                    angular.element(document.getElementById('toEditBtn'))
                                        .triggerHandler('click');
                                }, 0);
                            }
                        }
                        loader.unsetLoading();
                    }, function (d) {
                        loader.unsetLoading();
                        var modalInstance = $modal.open({
                            templateUrl: 'modal/40x.tpl.html',
                            controller: ErrorModalInstanceCtrl,
                            resolve: {
                                error: function () {
                                    return d;
                                }
                            }
                        });
                        modalInstance.result.then(function () {}, function () {
                            if (d.status == 404) {
                                $state.go('logout', {}, {
                                    location: true
                                });
                            } else {
                                $state.go('logged', {}, {
                                    location: true
                                });
                            }
                        });
                    });
            };
            $scope.checkLength = function (data, min, max) {
                if (data.length < min) {
                    return 'El tamano mínimo son '+min+' caracteres!';
                }
                if (data.length > max) {
                    return 'El tamano máximo son '+max+' caracteres!';
                }
            };
            $scope.saveUser = function () {
                loader.setLoading();
                var putData = {
                    'nombre': this.socio.nombre,
                    'apellido': this.socio.apellido,
                    'esncard': this.socio.esncard,
                    'pais': this.socio.pais,
                    'passport': this.socio.passport,
                    'email': this.socio.email,
                    'created_at': this.socio.created_at,
                    'language': this.socio.language
                };
                var path = '/api/socios/' + id;
                var data = ApiCall.makeCall(credenciales.getXWSSE(), 'PUT', path, putData)
                    .then(function (d) {
                        $scope.loadSocio(id);
                    }, function (d) {
                        loader.unsetLoading();
                        var modalInstance = $modal.open({
                            templateUrl: 'modal/40x.tpl.html',
                            controller: ErrorModalInstanceCtrl,
                            resolve: {
                                error: function () {
                                    return d;
                                }
                            }
                        });
                        modalInstance.result.then(function () {}, function () {
                            if (d.status == 404) {
                                $state.go('logout', {}, {
                                    location: true
                                });
                            } else {
                                $state.go('logged', {}, {
                                    location: true
                                });
                            }
                        });
                    });
            };
            $scope.paises = [];
            $scope.languages = [{
                value: 'English'
            }, {
                value: 'Espanyol'
            }];
            $scope.paises = countries.get()
                .countries;
            var id = $stateParams.socioId;
            $scope.loadSocio(id);
        }
    ]);