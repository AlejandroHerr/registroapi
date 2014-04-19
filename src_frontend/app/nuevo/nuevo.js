angular.module('libroApp.nuevo', []).controller('NuevoSocioCtrl', ['loader', 'ApiCall', '$modal', 'credenciales',
    '$http', '$scope', '$location',
    function(loader, ApiCall, $modal, credenciales, $http, $scope, $location) {
        if (!credenciales.isLogged()) {
            $location.url("/app/logout");
            return;
        }
        loader.setLoading();
        $http.get('/app/resources/countries.json').success(function(response) {
            $scope.countries = response.countries;
        });
        $scope.languages = [{
            value: 'English'
        }, {
            value: 'Espanyol'
        }];
        $scope.socio = {
            'created_at': new Date().toJSON().slice(0, 10)
        };
        $scope.registrar = function() {
            if (this.nuevoSocio.$invalid) {
                return;
            }
            var socio = this.socio;
            var modalInstance = $modal.open({
                templateUrl: '/app/partials/modal/registrar.html',
                controller: 'RegistrarModalInstanceCtrl',
                resolve: {
                    socio: function() {
                        return socio;
                    }
                }
            });
            modalInstance.result.then(function() {
                $scope.nuevoSocio.$setPristine();
                $scope.socio = {
                    'nombre': '',
                    'apellido': '',
                    'esncard': '',
                    'created_at': new Date().toJSON().slice(0, 10),
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
