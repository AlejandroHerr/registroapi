angular.module('libroApp.login', [])
    .config(['$stateProvider',
        function ($stateProvider) {
            $stateProvider
                .state('login', {
                    url: '/login',
                    templateUrl: 'login/login.tpl.html',
                    controller: 'LoginCtrl'
                });
        }
    ])
    .controller('LoginCtrl', ['credentials', '$scope', '$state', 'loader',
        function (credentials, $scope, $state, loader) {
            $scope.logIn = function () {
                event.preventDefault();
                credentials.setUser($scope.username);
                credentials.setPass($scope.password);
                if (credentials.isLogged()) {
                    loader.setLoading();
                    $state.go('logged', {}, {
                        location: true
                    });
                }
            };
        }
    ]);