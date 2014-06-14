angular.module('libroApp.users', [])
    .config(['$stateProvider',
        function ($stateProvider) {
            $stateProvider.state('logged.user.lista', {
                url: '/lista',
                templateUrl: 'users/users.tpl.html',
                controller: 'UsersCtrl'
            });
        }
    ])
    .controller('UsersCtrl', ['ApiCall', '$scope', 'credenciales',
        function (ApiCall, $scope, credenciales) {
            $scope.activeClass = function (state) {
                if (state == 1) {
                    return 'btn-success';
                }
                return 'btn-warning';
            }
            $scope.activeText = function (state) {
                if (state == 1) {
                    return 'ACTIVO';
                }
                return 'INACTIVO';
            }
            $scope.roleClass = function (role) {
                if (role == 'ROLE_PRESIDENTE' || role == 'ROLE_SECRETARIO' || role == 'ROLE_SUPERADMIN') {
                    return 'btn-danger';
                }
                if (role == 'ROLE_JUNTA' || role == 'ROLE_ADMIN') {
                    return 'btn-primary';
                }
                if (role == 'ROLE_COLABORADOR' || role == 'ROLE_USER') {
                    return 'btn-primary';
                }
                return 'btn-default';
            }
            $scope.loadUsers = function (page) {
                var path = '/api/admin/users?page=' + page;
                ApiCall.apiCall(credenciales.getXWSSE(), 'GET', path, null, succesCb);
            };
            succesCb = function (d) {
                $scope.users = d.data.users;
                $scope.totalItems = d.data.pagination.totalResults;
                $scope.currentPage = d.data.pagination.currentPage;
                $scope.maxResults = d.data.pagination.maxResults;
            };
            $scope.loadUsers(1);
        }
    ]);