/* App Module */
var libroApp = angular.module('libroApp', ['xeditable', 'ui.router', 'ui.bootstrap', 'templates-app', 'libroApp.api', 'libroApp.outercontroller', 'libroApp.security', 'libroApp.services', 'libroApp.nuevo', 'libroApp.socio', 'libroApp.socios', 'libroApp.users', 'libroApp.login', 'libroApp.logout', 'libroApp.modals', 'libroApp.countries'])
    .
config(['$locationProvider',
    function($location) {
        $location.html5Mode(true);
    }
]);
libroApp.run(function(editableOptions) {
    editableOptions.theme = 'bs3';
});
libroApp.config(['$stateProvider',
    function($stateProvider) {
        $stateProvider.
        state('login', {
            url: '/login',
            templateUrl: 'login/login.tpl.html',
            controller: 'LoginCtrl'
        })
            .
        state('logout', {
            url: '/logout',
            templateUrl: 'login/login.tpl.html',
            controller: 'LogoutCtrl'
        })
        state('socios', {
            url: '/socios',
            templateUrl: 'socios/socios.tpl.html',
            controller: 'SociosCtrl'
        })
            .
        state('socio.view', {
            url: '/socio/:socioId/:mode',
            templateUrl: 'socio/socio.tpl.html',
            controller: 'SocioCtrl'
        })
            .
        state('socio.nuevo', {
            url: '/socio/nuevo',
            templateUrl: 'nuevo/nuevo.tpl.html',
            controller: 'NuevoSocioCtrl'
        })
            .
        otherwise('/login');
    }
]);
