angular.module('libroApp.logout', [])
    .controller('LogoutCtrl', ['credenciales', '$scope', '$location',
        function(credenciales, $scope, $location) {
            credenciales.logOut();
            $location.url('/login');
        }
    ]);
