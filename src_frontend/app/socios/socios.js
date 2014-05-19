angular.module('libroApp.socios', [])
    .config(['$stateProvider',
        function ($stateProvider) {
            $stateProvider
                .state('logged.socio.lista', {
                    url: '/lista',
                    templateUrl: 'socios/socios.tpl.html',
                    controller: 'SociosCtrl'
                });
        }
    ])
    .controller('SociosCtrl', ['ApiCall', '$modal', 'queryOptions', 'credenciales', '$scope', 'loader', '$state',
        function (ApiCall, $modal, queryOptions, credenciales, $scope, loader, $state) {
            if (!credenciales.isLogged()) {
                $state.go('logout', {}, {
                    location: true
                });
                return;
            }
            $scope.refresh = function () {
                $scope.loadSocios($scope.options.currentPage);
            };
            $scope.reset = function () {
                $scope.options = queryOptions.reset();
                $scope.loadSocios($scope.options.currentPage);
            };
            $scope.changePage = function (page) {
                $scope.options.currentPage = page;
                $scope.loadSocios($scope.options.currentPage);
            };
            $scope.loadSocios = function (page) {
                if (flag) {
                    loader.setLoading();
                    flag = false;
                    var path = '/api/socios?maxResults=' + $scope.maxResults + '&currentPage=' + page + '&orderDir=' + $scope.options.orderDir + '&orderBy=' + $scope.options.orderBy;
                    var data = ApiCall.makeCall(credenciales.getXWSSE(), 'GET', path, null)
                        .then(function (d) {
                            $scope.socios = d.data.socios;
                            $scope.totalItems = d.data.pagination.totalResults;
                            $scope.options.currentPage = parseInt(d.data.pagination.currentPage, 10);
                            $scope.currentPage = d.data.pagination.currentPage;
                            $scope.maxResults = d.data.pagination.maxResults;
                            flag = true;
                            loader.unsetLoading();
                        }, function (d) {
                            flag = true;
                            loader.unsetLoading();
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
                                if (d.status == 403) {
                                    //levatelo a alg'un lado
                                } else {
                                    $state.go('logout', {}, {
                                        location: true
                                    });
                                }
                            });
                        });
                }
            };
            $scope.remove = function (socio) {
                var modalInstance = $modal.open({
                    templateUrl: 'modal/delete.tpl.html',
                    controller: 'DeleteModalInstanceCtrl',
                    resolve: {
                        socio: function () {
                            return socio;
                        }
                    }
                });
                modalInstance.result.then(function () {
                    $scope.loadSocios($scope.options.currentPage);
                });
            };
            $scope.edit = function (socio) {
                loader.setLoading();
                $state.go('logged.socio.socio', {
                    socioId: socio,
                    mode: 'edit'
                }, {
                    location: true
                });
            };
            var flag = true;
            $scope.options = queryOptions.get();
            $scope.maxSize = 100;
            $scope.isCollapsed = true;
            $scope.toCollapse = function () {
                $scope.isCollapsed = !$scope.isCollapsed;
            };
            $scope.loadSocios($scope.options.currentPage);
        }
    ]);