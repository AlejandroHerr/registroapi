angular.module('libroApp.login', [])
    .config(['$stateProvider',
        function ($stateProvider) {
            $stateProvider
                .state('login', {
                    url: '/login',
                    views: {
                        'content': {
                            templateUrl: 'login/login.tpl.html',
                            controller: 'LoginCtrl'
                        }
                    }
                });
        }
    ])
    .controller('LoginCtrl', ['credenciales', '$scope', '$location', 'loader',
        function (credenciales, $scope, $location, loader) {
            $scope.logIn = function () {
                event.preventDefault();
                credenciales.setUser($scope.username);
                credenciales.setPass($scope.password);
                if (credenciales.isLogged()) {
                    loader.setLoading();
                    $location.url("/socios");
                }
            };
        }
    ]);