angular.module('libroApp.socios.lista', [])
    .config(['$stateProvider',
        function ($stateProvider) {
            $stateProvider
                .state('logged.socios.lista', {
                    url: '/',
                    templateUrl: 'socios/lista/lista.tpl.html',
                    controller: 'SociosListaCtrl'
                });
        }
    ])
    .controller('SociosListaCtrl', ['ApiCaller', '$modal', 'queryParams', 'credentials', '$scope', '$state',
        function (ApiCaller, $modal, queryParams, credentials, $scope, $state) {
            $scope.changePage = function (page) {
                $scope.options.page = page;
                $scope.loadSocios($scope.options.page);
            };
            $scope.edit = function (socio) {
                $state.go('logged.socios.socio', {
                    socioId: socio,
                    mode: 'edit'
                }, {
                    location: true
                });
            };
            $scope.loadSocios = function (page) {
                var path = '/api/socios?max=' + $scope.options.max + '&page=' + page + '&dir=' + $scope.options.dir + '&by=' + $scope.options.by;
                var data = ApiCaller.modalCall(credentials.getXWSSE(), 'GET', path, null, function (d) {
                        $scope.socios = d.data.socios;
                        $scope.total = d.data.pagination.total;
                        $scope.options.page = parseInt(d.data.pagination.page,10);
                        $scope.page = d.data.pagination.page;
                        $scope.max = d.data.pagination.max;
                        $scope.options.maxPages=parseInt($scope.numPages,10);
                    }
                );
            };
            $scope.refresh = function () {
                $scope.loadSocios($scope.options.page);
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
                    $scope.loadSocios($scope.options.page);
                });
            };
            
            $scope.reset = function () {
                $scope.options = queryParams.reset();
                $scope.loadSocios($scope.options.page);
            };
            $scope.toCollapse = function () {
                $scope.isCollapsed = !$scope.isCollapsed;
            };
            
            $scope.options = queryParams.get();
            $scope.maxSize = 100;
            $scope.isCollapsed = true;
            
            $scope.loadSocios($scope.options.page);
        }
    ]);