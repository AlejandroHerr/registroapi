angular.module('libroApp.socio', [])
    .controller('SocioCtrl', ['$routeParams', 'ApiCall', '$scope', 'credenciales', '$location', '$http', '$filter', 'loader', 'countries',
        function($routeParams, ApiCall, $scope, credenciales, $location, $http, $filter, loader, countries) {
            if (!credenciales.isLogged()) {
                $location.url("/logout");
                return;
            }
            loader.setLoading();
            $scope.getPais = function() {
                if ($scope.paises.length) {
                    var selected = $filter('filter')($scope.paises, {
                        alpha2: $scope.socio.pais
                    });
                    return selected.length ? selected[0].name : 'Not set';
                } else {
                    return $scope.socio.pais;
                }
            };
            $scope.loadSocio = function(id) {
                var path = '/api/socios/' + id;
                var data = ApiCall.makeCall(credenciales.getXWSSE(), 'GET', path, null)
                    .then(function(d) {
                        $scope.socio = d.data;
                        $scope.pais = $scope.getPais();
                        loader.unsetLoading();
                    }, function(d) {
                        loader.unsetLoading();
                        var modalInstance = $modal.open({
                            templateUrl: 'modal/40x.tpl.html',
                            controller: ErrorModalInstanceCtrl,
                            resolve: {
                                error: function() {
                                    return d;
                                }
                            }
                        });
                        modalInstance.result.then(function() {}, function() {
                            if (d.status == 403) {
                                //levatelo a alg'un lado
                            } else {
                                $location.url("/logout");
                            }
                        });
                    });
            };
            $scope.checkLength = function(data, min, max) {
                if (data.length < min) {
                    return "El tamano mínimo son 2 caracteres!";
                }
                if (data.length > max) {
                    return "El tamano máximo son 50 caracteres!";
                }
            };
            $scope.saveUser = function() {
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
                    .then(function(d) {
                        $scope.loadSocio(id);
                    }, function(d) {
                        loader.unsetLoading();
                        //do something when it fails
                    });
            };
            var id = $routeParams.socioId;
            $scope.paises = [];
            $scope.languages = [{
                value: 'English'
            }, {
                value: 'Espanyol'
            }];
            $scope.paises = countries.get()
                .countries;
            $scope.loadSocio(id);
        }
    ]);
