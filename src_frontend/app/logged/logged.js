angular.module('libroApp.logged', [])
    .config(['$stateProvider',
        function ($stateProvider) {
            $stateProvider
                .state('logged', {
                    url: '/hola',
                    templateUrl: 'logged/logged.tpl.html',
                    controller: 'LoggedCtrl'
                });
        }
    ])
    .controller('LoggedCtrl', ['$scope', '$location', 'loader',
        function ($scope, $location, loader) {
            loader.unsetLoading();
        }
    ]);