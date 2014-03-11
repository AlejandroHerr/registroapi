	'use strict';
	var libroControllers = angular.module('libroControllers', []);
	libroControllers.controller('LoginCtrl', ['credenciales', '$scope', '$location','loader',
		function (credenciales, $scope, $location,loader) {
			$scope.logIn = function () {
				event.preventDefault();
				credenciales.setUser($scope.username);
				credenciales.setPass($scope.password);		
				if (credenciales.isLogged()){
					loader.setLoading();
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
		'credenciales', '$scope', '$location','loader',
		function (ApiCall, $modal, queryOptions, credenciales, $scope, $location,loader) {
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
					loader.setLoading();
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
							loader.unsetLoading();
						},function(d){
							flag = true;
							loader.unsetLoading();
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
			$scope.remove = function (socio) {
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
				loader.setLoading();
				$location.url("/app/socio/"+socio+"/edit");
			}
			var flag = true;
			$scope.options = queryOptions.get();
			$scope.maxSize = 100;
			$scope.isCollapsed = true;
			$scope.toCollapse = function () {
				$scope.isCollapsed = !$scope.isCollapsed;
			}
			$scope.loadSocios($scope.options.currentPage);
		}
	]);
	
	libroControllers.controller('SocioCtrl', ['$routeParams', 'ApiCall',
		'$scope', 'credenciales', '$location','$http','$filter','loader',
		function ($routeParams, ApiCall, $scope, credenciales, $location,$http,$filter,loader) {
			if(!credenciales.isLogged()) {
				$location.url("/app/logout");
				return;
			}
			loader.setLoading();
			$scope.getPais = function() {
				if($scope.paises.length) {
				    var selected = $filter('filter')($scope.paises, {alpha2: $scope.socio.pais});
				    return selected.length ? selected[0].name : 'Not set';
				} else {
				    return $scope.socio.pais;
				}
			};
			$scope.loadSocio = function (id) {
				var data = ApiCall.getSocio(id, credenciales.getXWSSE())
					.then(function (d) {
						$scope.socio = d.data;
						$scope.pais = $scope.getPais();
						loader.unsetLoading();
					},function(d){
						loader.unsetLoading();
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
					});
			};
			$scope.checkLength = function(data,min,max){
				if (data.length < min) {
			      return "El tamano mínimo son 2 caracteres!";
			    }
			    if (data.length > max) {
			      return "El tamano máximo son 50 caracteres!";
			    }
			}
			$scope.saveUser = function() {
				loader.setLoading();
				var putData = {
					'nombre':this.socio.nombre,
					'apellido':this.socio.apellido,
					'esncard':this.socio.esncard,
					'pais':this.socio.pais,
					'passport':this.socio.passport,
					'email':this.socio.email,
					'created_at':this.socio.created_at
				}
			    var data = ApiCall.putSocio(putData,this.socio.id, credenciales.getXWSSE())
			    	.then(function (d){
			    		$scope.loadSocio(id);
			    	},function (d){
   						loader.unsetLoading();
			    		//do something when it fails
			    	}
			    );
			};	
			var id = $routeParams.socioId
			$scope.paises = [];
			$http.get('/app/resources/countries.json').success(function(data) {
				$scope.paises = data.countries;
				$scope.loadSocio(id);
		    });							
	}]);
	libroControllers.controller('NuevoSocioCtrl', ['loader','ApiCall', '$modal', 'credenciales',
		'galletitas', '$http', '$scope', '$location',
		function (loader,ApiCall, $modal, credenciales, galletitas, $http, $scope, $location) {
			if(!credenciales.isLogged()) {
				$location.url("/app/logout");
				return;
			}
			loader.setLoading();
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
			loader.unsetLoading();
	}]);
	var RegistrarModalInstanceCtrl = ['ApiCall', 'credenciales', '$scope',
		'$modalInstance', 'socio',
		function (ApiCall, credenciales, $scope, $modalInstance, socio) {
			$scope.successFlag = false;
			$scope.failFlag = false;
			$scope.progress = "50";
			$scope.status = "progress-bar-warning";
			$scope.isCollapsed = false;
			var data = ApiCall.postSocio(socio, credenciales.getXWSSE())
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
	libroControllers.controller('OuterController',['$scope','credenciales','loader',
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