/* App Module */
var libroApp = angular.module('libroApp', [
        'xeditable',
        'ui.router',
        'ui.bootstrap',
        'templates-app',
        'libroApp.api',
        'libroApp.outercontroller',
        'libroApp.security',
        'libroApp.services',
        'libroApp.nuevo_user',
        'libroApp.socios',
        'libroApp.socios.lista',
        'libroApp.socios.nuevo',
        'libroApp.socios.socio',
        'libroApp.user',
        'libroApp.users',
        'libroApp.logged',
        'libroApp.login',
        'libroApp.logout',
        'libroApp.modals',
        'libroApp.countries'
    ])
    .config(['$locationProvider',
        function ($location) {
            $location.html5Mode(true);
        }
    ])
    .config(['$stateProvider', '$urlRouterProvider',
        function ($stateProvider, $urlRouterProvider) {
            $urlRouterProvider.otherwise('/login');
        }
    ])
    .run(['editableOptions',
        function (editableOptions) {
            editableOptions.theme = 'bs3';
        }
    ]);