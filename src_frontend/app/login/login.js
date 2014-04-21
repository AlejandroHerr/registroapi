angular.module('libroApp.login', [])

.controller('LoginCtrl', ['credenciales', '$scope', '$location', 'loader',
    function(credenciales, $scope, $location, loader) {
        $scope.logIn = function() {
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
