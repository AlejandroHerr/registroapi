angular.module('libroApp.socios.lista', [])
    .controller('SociosListaCtrl', ['$modal', '$scope', 'ApiCaller', 'credentials', 'queryParams',
        function ($modal, $scope, ApiCaller, credentials, queryParams) {
            $scope.pagination = [];
            $scope.loadSocios = function () {
                var path = '/api/socios?max=' + $scope.pagination.maxItems + '&page=' + $scope.pagination.currentPage + '&dir=' + $scope.pagination.dir + '&by=' + $scope.pagination.by;
                var data = ApiCaller.modalCall(credentials.getXWSSE(), 'GET', path, null, function (d) {
                    $scope.socios = d.data.socios;
                    $scope.pagination.totalItems = d.data.pagination.total;
                    $scope.pagination.page = parseInt(d.data.pagination.page, 10);
                    $scope.pagination.currentPage = d.data.pagination.page;
                    $scope.pagination.maxItems = d.data.pagination.max;
                });
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
                    $scope.loadSocios();
                });
            };
            $scope.reset = function () {
                $scope.pagination = queryParams.reset();
                $scope.loadSocios();
            };
            $scope.pagination = queryParams.get();
            $scope.loadSocios();
        }]);