angular.module('libroApp.socios.socio', [])
    .controller('SociosSocioCtrl', ['$modal', '$stateParams', 'ApiCaller', '$scope', 'credentials', '$state', '$filter', 'Countries',
        function ($modal, $stateParams, ApiCaller, $scope, credentials, $state, $filter, Countries) {
            var first = true;
            var id = $stateParams.socioId;
            $scope.countries = Countries.getCountries();
            $scope.languages = [
                {
                    value: 'English'
                },
                {
                    value: 'Espanyol'
                }
            ];
            var setCountry = function () {
                var selected = $filter('filter')($scope.countries, $scope.socio.country, true);
                return selected.length ? selected[0][1] : 'Not set';
            };
            var setLanguage = function () {
                var selected = $filter('filter')($scope.languages, {
                    value: $scope.socio.language
                });
                return selected.length ? selected[0].value : 'Not set';
            };
            $scope.loadSocio = function () {
                var path = '/api/socios/' + id;
                var data = ApiCaller.modalCall(credentials.getXWSSE(), 'GET', path, null, function (d) {
                    $scope.socio = d.data;
                    $scope.country = setCountry();
                    $scope.language = setLanguage();
                    if ($stateParams.mode && first) {
                        if ($stateParams.mode === 'edit') {
                            first = false;
                            setTimeout(function () {
                                angular.element(document.getElementById('toEditBtn'))
                                    .triggerHandler('click');
                            }, 0);
                        }
                    }
                });
            };
            $scope.checkLength = function (data, min, max) {
                if (data.length < min) {
                    return 'El tamano mínimo son ' + min + ' caracteres!';
                }
                if (data.length > max) {
                    return 'El tamano máximo son ' + max + ' caracteres!';
                }
            };
            $scope.saveSocio = function (data, created_at) {
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
                    .then(function () {
                        $scope.loadSocio();
                    }, function (d) {
                        var modalInstance = $modal.open({
                            templateUrl: 'modal/40x.tpl.html',
                            controller: 'ErrorModalInstanceCtrl',
                            resolve: {
                                error: function () {
                                    return d;
                                }
                            }
                        });
                        modalInstance.result.then(null, function () {
                            if (d.status === 401) {
                                $state.go('logout', {}, {
                                    location: true
                                });
                            } else if (d.status === 403 || d.status === 404) {
                                $state.go('logged', {}, {
                                    location: true
                                });
                            }
                        });
                        return 'error';
                    });
                return data;
            };
            $scope.loadSocio();
        }]);