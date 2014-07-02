angular.module('libroApp.logs', ['libroApp.logs.lista','libroApp.logs.log'])
    .config(['$stateProvider',
        function ($stateProvider) {
            $stateProvider
                .state('logged.logs', {
                    abstract: true,
                    url: '^/logs',
                    template: '<ui-view/>'
                })
                .state('logged.logs.lista', {
                    url: '/',
                    templateUrl: 'logs/lista/lista.tpl.html',
                    controller: 'LogsListaCtrl'
                })
                /*.state('logged.logs.nuevo', {
                    url: '/nuevo',
                    templateUrl: 'logs/nuevo/nuevo.tpl.html',
                    controller: 'LogsNuevoCtrl'
                })*/
                .state('logged.logs.log', {
                    url: '/:logDate',
                    templateUrl: 'logs/log/log.tpl.html',
                    controller: 'LogsLogCtrl'

                });
        }
    ]);