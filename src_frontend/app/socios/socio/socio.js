angular.module('libroApp.socios.socio', [])
    .controller('SociosSocioCtrl', ['$modal','$stateParams', 'ApiCaller', '$scope', 'credentials', '$state', '$filter', 'countries',
        function ($modal,$stateParams, ApiCaller, $scope, credentials, $state, $filter, countries) {
            var first = true;
            var id = $stateParams.socioId;

            $scope.setCountry = function () {
                if ($scope.countries.length) {
                    var selected = $filter('filter')($scope.countries, {
                        alpha2: $scope.socio.country
                    });
                    return selected.length ? selected[0].name : 'Not set';
                } else {
                    return $scope.socio.country;
                }
            };
            $scope.loadSocio = function () {
                var path = '/api/socios/' + id;
                var data = ApiCaller.modalCall(credentials.getXWSSE(), 'GET', path, null, function (d) {
                        $scope.socio = d.data;
                        $scope.country = $scope.setCountry();
                        if ($stateParams.mode && first) {
                            if ($stateParams.mode == 'edit') {
                                first = false;
                                setTimeout(function () {
                                    angular.element(document.getElementById('toEditBtn'))
                                        .triggerHandler('click');
                                }, 0);
                            }
                        }
                    }
                );
            };
            $scope.checkLength = function (data, min, max) {
                if (data.length < min) {
                    return 'El tamano mínimo son '+min+' caracteres!';
                }
                if (data.length > max) {
                    return 'El tamano máximo son '+max+' caracteres!';
                }
            };
            $scope.saveUser = function (data,created_at) {    
                var putData = {
                    'name': data.name,
                    'surname': data.surname,
                    'esncard': data.esncard,
                    'country': data.country,
                    'passport': data.passport,
                    'email': data.email,
                    'created_at': created_at,
                    'language': data.language
                };
                var path = '/api/socios/' + id;
                var data = ApiCaller.rawCall(credentials.getXWSSE(), 'PUT', path, putData)
                    .then(function(d){
                        $scope.loadSocio();
                    }, function(d){
                        var modalInstance = $modal.open({
                            templateUrl: 'modal/40x.tpl.html',
                            controller: 'ErrorModalInstanceCtrl',
                            resolve: {
                                error: function () {
                                    return d;
                                }
                            }
                        });
                        modalInstance.result.then(function () {}, function () {
                            if (d.status == 401) {
                                $state.go('logout', {}, {
                                    location: true
                                });
                            }else if (d.status == 403 || d.status == 404){
                                $state.go('logged', {}, {
                                    location: true
                                });
                            }
                        });
                        return 'error';
                    });
                return data;
            };
            
            $scope.countries = [];
            $scope.countries = countries.get().countries;
            $scope.languages = [
                {
                    value: 'English'
                },
                {
                    value: 'Espanyol'
                }
            ];              
            $scope.loadSocio();
        }
    ]);