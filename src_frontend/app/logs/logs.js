angular.module('libroApp.logs', ['libroApp.logs.collection','libroApp.logs.log'])
    .config(['$stateProvider',
        function ($stateProvider) {
            $stateProvider
                .state('logged.logs', {
                    abstract: true,
                    url: '^/logs',
                    template: '<ui-view/>'
                })
                .state('logged.logs.collection', {
                    url: '/',
                    templateUrl: 'logs/collection/collection.tpl.html',
                    controller: 'LogsCollectionCtrl'
                })
                .state('logged.logs.log', {
                    url: '/:logDate',
                    templateUrl: 'logs/log/log.tpl.html',
                    controller: 'LogsLogCtrl'

                });
        }
    ]);