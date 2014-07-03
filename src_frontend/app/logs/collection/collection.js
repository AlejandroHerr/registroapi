angular.module('libroApp.logs.collection', [])
    .controller('LogsCollectionCtrl', ['$scope', 'ApiCaller', 'credentials',
        function ($scope, ApiCaller, credentials) {
            $scope.loadLogs = function () {
                var path = '/api/logs/';
                var data = ApiCaller.modalCall(credentials.getXWSSE(), 'GET', path, null, function (d) {
                    $scope.logs = d.data.logs;
                });
            };
            $scope.loadLogs();
        }]);