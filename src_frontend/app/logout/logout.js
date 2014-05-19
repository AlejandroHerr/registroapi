angular.module('libroApp.logout', [])
    .config(['$stateProvider',
        function ($stateProvider) {
            $stateProvider
                .state('logout', {
                url: '/logout',
                templateUrl: 'login/login.tpl.html',
                controller: 'LogoutCtrl'
            });
        }
    ])
    .controller('LogoutCtrl', ['credenciales', '$scope', '$state',
        function (credenciales, $scope, $state) {
            credenciales.logOut();
            $state.go('login', {}, {
                location: true
            });
        }
    ]);