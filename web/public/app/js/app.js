'use strict';

/* App Module */

var libroApp = angular.module('libroApp', [
	'ngRoute',
	'libroControllers',
    'libroServices',

]);

libroApp.config(['$routeProvider',
	function($routeProvider) {
		$routeProvider.
			when('/libro', {
				templateUrl: 'partials/libro.html',
				controller: 'LibroCtrl'
			}).
			when('/login', {
				templateUrl: 'partials/login.html',
				controller: 'LoginCtrl'
			}).
			otherwise({
				redirectTo: '/login'
			});
	}]);
