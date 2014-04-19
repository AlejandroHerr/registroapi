angular.module('libroApp.outercontroller', [])
.controller('OuterController',['$scope','credenciales','loader',
		function ($scope, credenciales,loader) {
			$scope.logged=credenciales.isLogged();
			$scope.$watch(
        		function(){ return credenciales.isLogged() },

        		function(newVal) {
          			$scope.logged = newVal;
        		}
      		)
      		$scope.$watch(
        		function(){ return loader.isLoading() },

        		function(newVal) {
          			$scope.isLoading = newVal;
        		}
      		)
			
		}]
	);