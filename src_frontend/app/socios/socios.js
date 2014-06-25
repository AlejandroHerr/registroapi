angular.module('libroApp.socios', [])
    .config(['$stateProvider',
        function ($stateProvider) {
            $stateProvider
                .state('logged.socios', {
                    abstract: true,
                    url: '^/socios',
                    template: '<ui-view/>'
                })
                .state('logged.socios.lista', {
                    url: '/',
                    templateUrl: 'socios/lista/lista.tpl.html',
                    controller: 'SociosListaCtrl'
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
        }
    ]);