/* App Module */
var libroApp = angular.module('libroApp', [
	'xeditable',
	'ngRoute',
	'ui.bootstrap',
	'templates-app',
	'templates-common',
	'libroApp.api',
	'libroApp.outercontroller',
	'libroApp.security',
	'libroApp.services',
	'libroApp.nuevo',
	'libroApp.socio',
	'libroApp.socios',
	'libroApp.login',
	'libroApp.logout',
	'libroApp.modals'
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
		when('/bin/login', {
			templateUrl: 'login/login.tpl.html',
			controller: 'LoginCtrl'
		}).
		when('/bin/logout', {
			templateUrl: 'login/login.tpl.html',
			controller: 'LogoutCtrl'
		}).
		when('/bin/socios', {
			templateUrl: 'socios/socios.tpl.html',
			controller: 'SociosCtrl'
		}).
		when('/bin/socio/:socioId/:mode', {
			templateUrl: 'socio/socio.tpl.html',
			controller: 'SocioCtrl'
		}).
		when('/bin/socio/nuevo', {
			templateUrl: 'nuevo/nuevo.tpl.html',
			controller: 'NuevoSocioCtrl'
		}).
		otherwise({
			redirectTo: '/bin/login'
		});
	}
]);