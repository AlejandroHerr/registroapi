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
    .controller('LoginCtrl', ['credenciales', '$scope', '$state', 'loader',
        function (credenciales, $scope, $state, loader) {
            $scope.logIn = function () {
                event.preventDefault();
                credenciales.setUser($scope.username);
                credenciales.setPass($scope.password);
                if (credenciales.isLogged()) {
                    loader.setLoading();
                    $state.go('logged', {}, {
                        location: true
                    });
                }
            };
        }
    ]);