angular.module('libroApp.logs.log', ['libroApp.directives','libroApp.filters'])
    .controller('LogsLogCtrl', ['$modal', '$stateParams', '$filter', 'ApiCaller', '$scope', 'credentials', '$state', '$filter', 'countries',
        function ($modal, $stateParams, $filter, ApiCaller, $scope, credentials, $state, $filter, countries) {
            var log = [];
            $scope.date = $stateParams.logDate;
            $scope.pagination = {
                'totalItems': '',
                'currentPage': 1
            }
            $scope.selectedChannel = [];
            $scope.selectedLevel = [];
            $scope.channels = [{
                name: 'main'
            }, {
                name: 'access'
            }, {
                name: 'transaction'
            }];
            $scope.levels = [
                {name: 100},
                {name: 200},
                {name: 250},
                {name: 300},
                {name: 400},
                {name: 500},
                {name: 550}
            ];
            $scope.loadLog = function () {
                var path = '/api/logs/' + $scope.date;
                var data = ApiCaller.modalCall(credentials.getXWSSE(), 'GET', path, null, function (d) {
                    log = d.data.log;
                    $scope.pagination.totalItems = log.length;
                    $scope.displayResults();
                });
            }
            $scope.loadPage = function () {
                if(log.length >0){
                    min = 10 * ($scope.pagination.currentPage - 1);
                    max = min + 10;
                    $scope.entries = $scope.entries.slice(min, max);
                }
            }
            $scope.displayResults = function () {
                $scope.entries = $filter('selectorFilter')(log, 'channel', $scope.selectedChannel, 'name');
                $scope.entries = $filter('selectorFilter')($scope.entries, 'level', $scope.selectedLevel, 'name');
                $scope.entries = $filter('orderBy')($scope.entries, 'datetime.date',false);
                $scope.pagination.totalItems = $scope.entries.length;
                $scope.loadPage();
            }
            //*** INIT
            $scope.loadLog();
        }
    ])
    .filter('logFilter', [
        function () {
            return function (entries, key1, selectedChannel, key2) {
                if (!angular.isUndefined(entries) && !angular.isUndefined(selectedChannel) && selectedChannel.length > 0) {
                    var tempEntries = [];
                    angular.forEach(selectedChannel, function (key2) {
                        angular.forEach(entries, function (entry) {
                            if (angular.equals(entry[key1], key2)) {
                                tempEntries.push(entry);
                            }
                        });
                    });
                    return tempEntries;
                } else {
                    return entries;
                }
            };
        }
    ]);