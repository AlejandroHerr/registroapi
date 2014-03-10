'use strict';
/* App Module */
angular.module('app', ['ngCookies']);
var libroApp = angular.module('libroApp', [
	'ngRoute',
	'ngCookies',
	'ui.bootstrap',
	'libroControllers',
	'libroServices',
	'xeditable'
]).
config(['$locationProvider',
	function ($location) {
		$location.html5Mode(true); 
}]);
libroApp.run(function(editableOptions) {
  editableOptions.theme = 'bs3';
});
libroApp.config(['$routeProvider',
	function ($routeProvider) {
		$routeProvider.
		when('/app/login', {
			templateUrl: '/app/partials/login.html',
			controller: 'LoginCtrl'
		}).
		when('/app/logout', {
			templateUrl: '/app/partials/login.html',
			controller: 'LogoutCtrl'
		}).
		when('/app/socios', {
			templateUrl: '/app/partials/socios.html',
			controller: 'SociosCtrl'
		}).
		when('/app/socio/:socioId/:mode', {
			templateUrl: '/app/partials/socio.html',
			controller: 'SocioCtrl'
		}).
		when('/app/socio/nuevo', {
			templateUrl: '/app/partials/nuevo.html',
			controller: 'NuevoSocioCtrl'
		}).
		otherwise({
			redirectTo: '/app/socios'
		});
	}
]);