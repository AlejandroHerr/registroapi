'use strict'; /* Services */
libroApp.factory('ApiCall', ['$http',
	function ($http) {
		var ApiCall = {
			getSocios: function (page, passwordDigest, options) {
				$http.defaults.headers.get = {
					'X-WSSE': passwordDigest,
					//'Current-Page': page,
					//'Max-Results': 10,
					'Query-Options': angular.toJson(options)
				};
				var promise = $http({
					method: 'GET',
					url: '/api/socios?maxResults='+options.maxResults+'&currentPage='+page+'&orderDir='+options.orderDir+'&orderBy='+options.orderBy
				}).then(function (response) {
					return response;
				});
				return promise;
			},
			postSocio: function (data,passwordDigest) {
				$http.defaults.headers.post = {
					'X-WSSE': passwordDigest,
				};
				var promise = $http({
					method: 'POST',
					url: '/api/socios',
					data: data
				}).then(function (response) {
					return response;
				});
				return promise;
			},
			deleteSocio: function (passwordDigest,id) {
				$http.defaults.headers.delete = {
					'X-WSSE': passwordDigest,
				};
				var promise = $http({
					method: 'DELETE',
					url: '/api/socios/' + id
				}).then(function (response) {
					return response;
				});
				return promise;
			}
		};
		return ApiCall;
}]);