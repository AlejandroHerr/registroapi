angular.module('libroApp.users.nuevo', ['libroApp.modal.nuevo'])
    .controller('UsersNuevoCtrl', ['loader', 'ApiCaller', '$modal', 'credentials', '$http', '$scope', '$state',
        function (loader, ApiCaller, $modal, credentials, $http, $scope, $state) {
            if (!credentials.isLogged()) {
                $state.go('logout', {}, {
                    location: true
                });
                return;
            }
            loader.setLoading();
            $scope.roles = [
                { value: 'ROLE_COLABORADOR' },
                { value: 'ROLE_JUNTA' },
                { value: 'ROLE_SECRETARIO' },
                { value: 'ROLE_PRESIDENTE' }
            ];
            $scope.registrar = function () {
                if (this.nuevoUser.$invalid) {
                    return;
                }
                var user = this.user;
                var modalInstance = $modal.open({
                    templateUrl: 'modal/nuevo/nuevo.tpl.html',
                    controller: 'ModalNuevoCtrl',
                    resolve: {
                        item: function () {
                            return user;
                        },
                        url: function () {
                            return '/api/admin/users';
                        }
                    }
                });
                modalInstance.result.then(function () {
                    $scope.nuevoUser.$setPristine();
                    $scope.user = {
                        'username':'',
                        'password':'',
                        'name': '',
                        'surname': '',
                        'email': '',
                        'roles': ''
                    };
                });
            };
            loader.unsetLoading();
        }
    ]);