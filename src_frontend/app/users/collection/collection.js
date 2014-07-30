angular.module('libroApp.users.collection', [])
    .controller('UsersCollectionCtrl', ['ApiCaller', '$scope', 'credentials',
        function (ApiCaller, $scope, credentials) {
            $scope.pagination = {
                totalItems: '',
                currentPage: 1,
                maxItems: "25",
                by: 'id',
                dir: 'DESC'
            };
            $scope.activeClass = function (state) {
                if (state == 1) {
                    return 'btn-warning';
                }
                return 'btn-danger';
            };
            $scope.activeText = function (state) {
                if (state == 1) {
                    return 'ACTIVO';
                }
                return 'INACTIVO';
            };
            $scope.roleClass = function (role) {
                if (role === 'ROLE_PRESIDENTE' || role === 'ROLE_SECRETARIO' || role === 'ROLE_SUPERADMIN') {
                    return 'btn-success';
                }
                if (role === 'ROLE_JUNTA' || role === 'ROLE_ADMIN') {
                    return 'btn-primary';
                }
                if (role === 'ROLE_COLABORADOR' || role === 'ROLE_USER') {
                    return 'btn-info';
                }
                return 'btn-default';
            };
            $scope.loadUsers = function () {
                var path = '/api/user/?page=' + $scope.pagination.currentPage;
                ApiCaller.modalCall(credentials.getXWSSE(), 'GET', path, null, function (d) {
                    $scope.users = d.data.users;
                    $scope.totalItems = d.data.pagination.total;
                    $scope.currentPage = d.data.pagination.currentPage;
                    $scope.maxItems = d.data.pagination.maxResults;
                });
            };
            $scope.loadUsers();
        }]);