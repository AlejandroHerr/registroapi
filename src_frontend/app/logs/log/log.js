angular.module('libroApp.logs.log', ['libroApp.directives', 'libroApp.filters'])
    .controller('LogsLogCtrl', ['$stateParams', '$filter', '$scope', 'ApiCaller', 'credentials',
        function ($stateParams, $filter, $scope, ApiCaller, credentials) {
            var log = [];
            var cosas = [
                {name: 100, level_class: 'primary'},
                {name: 200, level_class: 'success'},
                {name: 250, level_class: 'success'},
                {name: 300, level_class: 'warning'},
                {name: 400, level_class: 'warning'},
                {name: 500, level_class: 'danger'},
                {name: 550, level_class: 'danger'}
            ];

            $scope.date = $stateParams.logDate;
            $scope.pagination = {
                'order': false,
                'totalItems': '',
                'currentPage': 1
            };
            $scope.selectedChannel = [];
            $scope.selectedLevel = [];
    
            $scope.loadLog = function () {
                var path = '/api/logs/' + $scope.date;
                var data = ApiCaller.modalCall(credentials.getXWSSE(), 'GET', path, null, function (d) {
                    log = d.data.log;

                    var levels = _.uniq(_.map(log,function(item,key){return item.level;}));
                    $scope.levels = _.map(levels,function(item,key){
                        var row = $filter('filter')(cosas,item,true);
                        return row[0];
                    });
                    
                    var channels = _.uniq(_.map(log,function(item,key){return item.channel;}));
                    $scope.channels = _.map(channels,function(item,key){
                        return _.object(['name'],[item]);
                    });
                   
                    $scope.displayResults();
                });
            };
            $scope.displayResults = function () {
                var min = 10 * ($scope.pagination.currentPage - 1)
                var max = min + 10;
                $scope.entries = $filter('selectorFilter')(log, 'channel', $scope.selectedChannel, 'name');
                $scope.entries = $filter('selectorFilter')($scope.entries, 'level', $scope.selectedLevel, 'name');
                $scope.entries = $filter('orderBy')($scope.entries, 'datetime.date', $scope.pagination.order);
                $scope.pagination.totalItems = $scope.entries.length;
                $scope.entries = $scope.entries.slice(min, max);
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