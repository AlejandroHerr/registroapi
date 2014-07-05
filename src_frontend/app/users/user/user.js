angular.module('libroApp.users.user', [])
    .controller('UsersUserCtrl', ['$stateParams', 'ApiCaller', '$scope', 'credentials', '$state', '$http', '$filter', 'loader',
        function ($stateParams, ApiCaller, $scope, credentials, $state, $http, $filter, loader) {
            var bindUser = function(d){
                $scope.user = d.data;
            };
            $scope.loadSocio = function (id) {
                var path = '/api/admin/users/' + id;
                ApiCaller.modalCall(credentials.getXWSSE(), 'GET', path, null, bindUser);                   
            };
            var id = $stateParams.userId;
            $scope.loadSocio(id);
        }
    ]);