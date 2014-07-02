angular.module('libroApp.logs.collection', [])
    .controller('LogsCollectionCtrl', ['ApiCaller', '$modal', 'queryParams', 'credentials', '$scope', '$state',
        function (ApiCaller, $modal, queryParams, credentials, $scope, $state) {
            
            $scope.loadLogs = function () {
                var path = '/api/logs/';
                var data = ApiCaller.modalCall(credentials.getXWSSE(), 'GET', path, null, function (d) {
                        $scope.logs = d.data.logs;
                    }
                );
            };    
            $scope.loadLogs();
        }
    ]);