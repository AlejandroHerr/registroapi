angular.module('libroApp.user', [])
    .config(['$stateProvider',
        function ($stateProvider) {
            $stateProvider
                .state('logged.user.user', {
                    url: '/user/:userId',
                    templateUrl: 'user/user.tpl.html',
                    controller: 'UserCtrl'

                });
        }
    ])
    .controller('UserCtrl', ['$stateParams', 'ApiCaller', '$scope', 'credentials', '$state', '$http', '$filter', 'loader',
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