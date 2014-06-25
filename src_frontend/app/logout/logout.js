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
    .controller('LogoutCtrl', ['credentials', '$scope', '$state',
        function (credentials, $scope, $state) {
            credentials.logOut();
            $state.go('login', {}, {
                location: true
            });
        }
    ]);