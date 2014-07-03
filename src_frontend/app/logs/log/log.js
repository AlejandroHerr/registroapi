angular.module('libroApp.logs.log', ['libroApp.directives', 'libroApp.filters'])
    .controller('LogsLogCtrl', ['$stateParams', '$filter', '$scope', 'ApiCaller', 'credentials',
        function ($stateParams, $filter, $scope, ApiCaller, credentials) {
            var log = [];
            $scope.date = $stateParams.logDate;
            $scope.pagination = {
                'order': false,
                'totalItems': '',
                'currentPage': 1
            };
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
                {name: 100, level_class: 'primary'},
                {name: 200, level_class: 'success'},
                {name: 250, level_class: 'success'},
                {name: 300, level_class: 'warning'},
                {name: 400, level_class: 'warning'},
                {name: 500, level_class: 'danger'},
                {name: 550, level_class: 'danger'}
            ];
            $scope.loadLog = function () {
                var path = '/api/logs/' + $scope.date;
                var data = ApiCaller.modalCall(credentials.getXWSSE(), 'GET', path, null, function (d) {
                    log = d.data.log;
                    $scope.displayResults();
                });
            };
            $scope.loadPage = function () {
                if (log.length > 0) {
                    var min = 10 * ($scope.pagination.currentPage - 1), max = min + 10;
                    $scope.entries = $scope.entries.slice(min, max);
                }
            };
            $scope.displayResults = function () {
                $scope.entries = $filter('selectorFilter')(log, 'channel', $scope.selectedChannel, 'name');
                $scope.entries = $filter('selectorFilter')($scope.entries, 'level', $scope.selectedLevel, 'name');
                $scope.entries = $filter('orderBy')($scope.entries, 'datetime.date', $scope.pagination.order);
                $scope.pagination.totalItems = $scope.entries.length;
                $scope.loadPage();
            };
            $scope.changeOrder = function () {
                $scope.pagination.order = !$scope.pagination.order;
                $scope.displayResults();
            };
            $scope.orderClass = function () {
                if ($scope.pagination.order) {
                    return 'glyphicon glyphicon-sort-by-alphabet';
                }
                return 'glyphicon glyphicon-sort-by-alphabet-alt';  
            };
            $scope.getLevelClass = function (level) {
                var theLevel = $filter('filter')($scope.levels, level)[0];
                return 'btn-' + theLevel.level_class;
            };
            $scope.loadLog();
        }]);