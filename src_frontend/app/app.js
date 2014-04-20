/* App Module */
var libroApp = angular.module('libroApp', [
    'xeditable',
    'ngRoute',
    'ui.bootstrap',
    'templates-app',
    'templates-common',
    'libroApp.api',
    'libroApp.outercontroller',
    'libroApp.security',
    'libroApp.services',
    'libroApp.nuevo',
    'libroApp.socio',
    'libroApp.socios',
    'libroApp.login',
    'libroApp.logout',
    'libroApp.modals'
]).
config(['$locationProvider',
    function($location) {
        $location.html5Mode(true);
    }
]);
libroApp.run(function(editableOptions) {
    editableOptions.theme = 'bs3';
});
libroApp.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
        when('/login', {
            templateUrl: 'login/login.tpl.html',
            controller: 'LoginCtrl'
        }).
        when('/logout', {
            templateUrl: 'login/login.tpl.html',
            controller: 'LogoutCtrl'
        }).
        when('/socios', {
            templateUrl: 'socios/socios.tpl.html',
            controller: 'SociosCtrl'
        }).
        when('/socio/:socioId/:mode', {
            templateUrl: 'socio/socio.tpl.html',
            controller: 'SocioCtrl'
        }).
        when('/socio/nuevo', {
            templateUrl: 'nuevo/nuevo.tpl.html',
            controller: 'NuevoSocioCtrl'
        }).
        otherwise({
            redirectTo: '/login'
        });
    }
]);
