angular.module('libroApp.users', ['libroApp.users.collection', 'libroApp.users.nuevo', 'libroApp.users.user'])
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
                .state('logged.user.nuevo', {
                    url: '/nuevo',
                    templateUrl: 'users/nuevo/nuevo.tpl.html',
                    controller: 'UsersNuevoCtrl'
                })
                .state('logged.user.user', {
                    url: '/:userId',
                    templateUrl: 'users/user/user.tpl.html',
                    controller: 'UsersUserCtrl'
                });
        }]);