angular.module('libroApp.outercontroller', [])
    .controller('OuterController', ['$scope', 'credentials', 'loader',
        function($scope, credentials, loader) {
            $scope.logged = credentials.isLogged();
            $scope.$watch(function() {
                return credentials.isLogged()
            }, function(newVal) {
                $scope.logged = newVal;
            })
            $scope.$watch(function() {
                return loader.isLoading()
            }, function(newVal) {
                $scope.isLoading = newVal;
            })
        }
    ]);
