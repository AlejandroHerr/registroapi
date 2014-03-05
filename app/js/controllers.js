	'use strict';
	var libroControllers = angular.module('libroControllers', []);
	libroControllers.controller('LoginCtrl', ['credenciales', '$scope', '$location',
		function (credenciales, $scope, $location) {
			$scope.logIn = function () {
				event.preventDefault();
				credenciales.setUser($scope.username);
				credenciales.setPass($scope.password);		
				if (credenciales.isLogged()){
					$location.url("/app/socios");
				}
			};
		}
	]);
	libroControllers.controller('LogoutCtrl', ['credenciales', '$scope', '$location',
		function (credenciales, $scope, $location) {
			credenciales.logOut();
			$location.url('/app/login');
		}
	]);
	libroControllers.controller('SociosCtrl', ['ApiCall', '$modal', 'queryOptions',
		'credenciales', '$http', '$scope', '$location',
		function (ApiCall, $modal, queryOptions, credenciales, $http, $scope, $location) {
			if(!credenciales.isLogged()) {
				$location.url("/app/logout");
				return;
			}
			$scope.refresh = function () {
				$scope.loadSocios($scope.options.currentPage);
			};
			$scope.reset = function () {
				$scope.options = queryOptions.reset();
				$scope.loadSocios($scope.options.currentPage);
			};
			$scope.changePage = function (page) {
				$scope.options.currentPage = page;
				$scope.loadSocios($scope.options.currentPage);
			}
			$scope.loadSocios = function (page) {
				if(flag) {
					$scope.isLoading=true;
					flag = false;
					var data = ApiCall.getSocios(page, credenciales.getXWSSE(),
						$scope.options)
						.then(function (d) {
							$scope.socios = d.data.socios;
							$scope.totalItems = d.data.pagination.totalResults;
							$scope.options.currentPage = parseInt(d.data.pagination.currentPage);
							$scope.currentPage = d.data.pagination.currentPage;
							$scope.maxResults = d.data.pagination.maxResults;
							flag = true;
							$scope.isLoading=false;
						},function(d){
							flag = true;
							$scope.isLoading=false;
							var modalInstance = $modal.open({
								templateUrl: '/app/partials/modal/40x.html',
								controller: ErrorModalInstanceCtrl,
								resolve: {
									error: function () {
										return d;
									}
								}
							});
							modalInstance.result.then(function(){},function(){
								if(d.status == 403){
									//levatelo a alg'un lado
								}else{
									$location.url("/app/logout");
								}
							});

						})
				}
			};
			$scope.delete = function (socio) {
				var modalInstance = $modal.open({
					templateUrl: '/app/partials/modal/delete.html',
					controller: DeleteModalInstanceCtrl,
					resolve: {
						socio: function () {
							return socio;
						}
					}
				});
				modalInstance.result.then(function () {
					$scope.loadSocios($scope.options.currentPage);
				});
			}
			$scope.edit = function (socio) {
				$location.url("/app/socio/"+socio+"/edit");
			}
			var flag = true;
			$scope.options = queryOptions.get();
			$scope.maxSize = 100;
			$scope.isCollapsed = true;
			$scope.isLoading=false;
			$scope.toCollapse = function () {
				$scope.isCollapsed = !$scope.isCollapsed;
			}
			$scope.loadSocios($scope.options.currentPage);
		}
	]);
	
	libroControllers.controller('SocioCtrl', ['$routeParams', '$cookies', '$http',
		'$scope', 'credenciales', '$location',
		function ($routeParams, $cookies, $http, $scope, credenciales, $location) {
			if(!credenciales.isLogged()) {
				$location.url("/app/logout");
				return;
			}
			//$routeParams.socioId
	}]);
	libroControllers.controller('NuevoSocioCtrl', ['ApiCall', '$modal', 'optiones',
		'galletitas', '$http', '$scope', '$location',
		function (ApiCall, $modal, optiones, galletitas, $http, $scope, $location) {
			if(!galletitas.isLogged()) {
				$location.url("/app/login");
				return;
			}
			$http.get('/app/resources/countries.json').success(function (response) {
				$scope.countries = response.countries;
			});
			$scope.socio = {
				'created_at': new Date().toJSON().slice(0, 10)
			}
			$scope.registrar = function () {
				if(this.nuevoSocio.$invalid){
					return;
				}
				var socio = this.socio;
				var modalInstance = $modal.open({
					templateUrl: '/app/partials/modal/registrar.html',
					controller: RegistrarModalInstanceCtrl,
					resolve: {
						socio: function () {
							return socio;
						}
					}
				});
				modalInstance.result.then(function () {
					$scope.nuevoSocio.$setPristine();
					$scope.socio = {
						'nombre' : '',
						'apellido': '',
						'esncard': '',
						'created_at': new Date().toJSON().slice(0, 10),
						'passport': '',
						'pais': '',
						'email': ''
					};
				});
			}
	}]);
	var RegistrarModalInstanceCtrl = ['ApiCall', 'galletitas', '$scope',
		'$modalInstance', 'socio',
		function (ApiCall, galletitas, $scope, $modalInstance, socio) {
			$scope.successFlag = false;
			$scope.failFlag = false;
			$scope.progress = "50";
			$scope.status = "progress-bar-warning";
			$scope.isCollapsed = false;
			var data = ApiCall.postSocio(socio, galletitas.getXWSSE())
				.then(function (d) {
					$scope.successFlag = true;
					$scope.progress = "100";
					$scope.status = "progress-bar-success";
				}, function (d) {
					$scope.failFlag = true;
					$scope.progress = "100";
					$scope.status = "progress-bar-danger";
					$scope.errors = d.data;
				});
			$scope.volver = function(){
				$modalInstance.close();
			}
			$scope.salir = function () {
				$modalInstance.dismiss();
			};


				//en caso afirmativo el tiene que ir a close, sino a dismiss
	}];

/*	libroControllers.controller('OuterController',['credenciales','scope',function(credenciales,$scope){
		$scope.fuera='hola';
	}]);
*/
	libroControllers.controller('OuterController',['$scope','credenciales',
		function ($scope, credenciales) {
			$scope.logged=credenciales.isLogged();
			$scope.$watch(
        		function(){ return credenciales.isLogged() },

        		function(newVal) {
          			$scope.logged = newVal;
        		}
      		)
			
		}]);