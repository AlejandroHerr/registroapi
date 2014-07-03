angular.module('libroApp.socios', ['libroApp.socios.collection', 'libroApp.socios.nuevo', 'libroApp.socios.socio'])
    .config(['$stateProvider',
        function ($stateProvider) {
            $stateProvider
                .state('logged.socios', {
                    abstract: true,
                    url: '^/socios',
                    template: '<ui-view/>'
                })
                .state('logged.socios.collection', {
                    url: '/',
                    templateUrl: 'socios/collection/collection.tpl.html',
                    controller: 'SociosCollectionCtrl'
                })
                .state('logged.socios.nuevo', {
                    url: '/nuevo',
                    templateUrl: 'socios/nuevo/nuevo.tpl.html',
                    controller: 'SociosNuevoCtrl'
                })
                .state('logged.socios.socio', {
                    url: '/:socioId/{mode:.*}',
                    templateUrl: 'socios/socio/socio.tpl.html',
                    controller: 'SociosSocioCtrl'
                });
        }]);