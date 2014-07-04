angular.module('libroApp.socios.nuevo', [])
    .controller('SociosNuevoCtrl', ['$modal', '$scope', 'countries',
        function ($modal, $scope, countries) {
            $scope.registrar = function () {
                if (this.nuevoSocio.$invalid) {
                    return;
                }
                var socio = this.socio;
                var modalInstance = $modal.open({
                    templateUrl: 'socios/nuevo/modal.tpl.html',
                    controller: 'SociosNuevoModalCtrl',
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
        }])
    .controller('SociosNuevoModalCtrl', ['$modalInstance', '$scope', 'ApiCaller', 'credentials', 'item', 'url',
        function ($modalInstance, $scope, ApiCaller, credentials, item, url) {
            $scope.successFlag = false;
            $scope.failFlag = false;
            $scope.progress = "50";
            $scope.status = "progress-bar-warning";
            $scope.isCollapsed = false;
            var path = url;
            var data = ApiCaller.rawCall(credentials.getXWSSE(), 'POST', path, item)
                .then(function (d) {
                    $scope.successFlag = true;
                    $scope.progress = "100";
                    $scope.status = "progress-bar-success";
                }, function (d) {
                    $scope.failFlag = true;
                    $scope.progress = "100";
                    $scope.status = "progress-bar-danger";
                    $scope.errors = d.data;
                });
            $scope.volver = function () {
                $modalInstance.close();
            };
            $scope.salir = function () {
                $modalInstance.dismiss();
            };
            //en caso afirmativo el tiene que ir a close, sino a dismiss
        }]);