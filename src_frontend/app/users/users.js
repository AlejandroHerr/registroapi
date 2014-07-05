angular.module('libroApp.users', ['libroApp.users.collection', 'libroApp.users.user'])
    .config(['$stateProvider',
        function ($stateProvider) {
            $stateProvider
                .state('logged.user', {
                    abstract: true,
                    url: '^/users',
                    template: '<ui-view/>'
                })
                .state('logged.user.collection', {
                    url: '/lista',
                    templateUrl: 'users/collection/collection.tpl.html',
                    controller: 'UsersCollectionCtrl'
                })
                .state('logged.user.user', {
                    url: '/lista',
                    templateUrl: 'users/user/user.tpl.html',
                    controller: 'UsersUserCtrl'
                });
        }]);