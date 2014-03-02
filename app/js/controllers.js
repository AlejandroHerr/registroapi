	'use strict';
	/* Controllers */
	var libroControllers = angular.module('libroControllers', []);
	libroControllers.controller('LoginCtrl', ['credenciales', '$scope', '$location',
		function (credenciales, $scope, $location) {
			$scope.logIn = function () {
				event.preventDefault();
				credenciales.setUser($scope.username);
				credenciales.setPass($scope.password);		
				if (credenciales.isLogged()){
					$location.url("/app");
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
	libroControllers.controller('SociosCtrl', ['ApiCall', '$modal', 'optiones',
		'galletitas', '$http', '$scope', '$location',
		function (ApiCall, $modal, optiones, galletitas, $http, $scope, $location) {
			if(!galletitas.isLogged()) {
				$location.url("/app/login");
				return;
			}
			var flag = true;
			$scope.actualizar = function () {
				$scope.loadSocios($scope.options.currentPage);
			};
			$scope.reset = function () {
				$scope.options = optiones.reset();
				$scope.loadSocios($scope.options.currentPage);
			};
			$scope.changePage = function (page) {
				$scope.options.currentPage = page;
				$scope.loadSocios($scope.options.currentPage);
			}
			$scope.loadSocios = function (page) {
				if(flag) {
					flag = false;
					var data = ApiCall.getSocios(page, galletitas.getXWSSE(),
						$scope.options)
						.then(function (d) {
							if(d.status == 200) {
								$scope.socios = d.data.socios;
								$scope.totalItems = d.data.pagination.totalResults;
								$scope.options.currentPage = parseInt(d.data.pagination.currentPage);
								$scope.currentPage = d.data.pagination.currentPage;
								$scope.maxResults = d.data.pagination.maxResults;
								flag = true;
							}
							//Añadir 401 y 403 y un default.
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
				console.log(socio);
			}
			$scope.options = optiones.get();
			$scope.maxSize = 100;
			$scope.isCollapsed = true;
			$scope.toCollapse = function () {
				$scope.isCollapsed = !$scope.isCollapsed;
			}
			$scope.loadSocios($scope.options.currentPage);
		}
	]);
	var DeleteModalInstanceCtrl = ['ApiCall', 'galletitas', '$scope',
		'$modalInstance', 'socio',
		function (ApiCall, galletitas, $scope, $modalInstance, socio) {
			$scope.socio = socio;
			$scope.alerts = [
				{
					type: 'warning',
					msg: '¡Esta acción no se puede deshacer!'
				},
			  ];
			$scope.addAlert = function (Type, Msg) {
				$scope.alerts.push({
					type: Type,
					msg: Msg
				});
			};
			$scope.closeAlert = function (index) {
				$scope.alerts.splice(index, 1);
			};
			$scope.deletAlerts = function () {
				$scope.alerts = '';
			}
			$scope.salir = function () {
				$modalInstance.dismiss();
			};
			$scope.ok = function (str) {
				if(str == 'ELIMINAR ' + $scope.socio.esncard) {
					$scope.addAlert('success',
						'Nuestros monos lo están borrando... Espera y no toques nada.');
					var data = ApiCall.deleteSocio(galletitas.getXWSSE(), $scope.socio.id)
						.then(function (d) {
							if(d.status == 204) {
								$modalInstance.close();
							}
							if(d.status == 401) {
								$scope.addAlert('danger', '¡Tu sesion ha caducado..!');
							}
							if(d.status == 403) {
								$scope.addAlert('danger', '¡No tienes permiso para borrar socios!');
							}
						})
				} else {
					$scope.addAlert('danger', '¡Tienes que escribir la confirmación correcta!');
				}
			};
	}];
	libroControllers.controller('SocioCtrl', ['$routeParams', '$cookies', '$http',
		'$scope', 'xwsse', '$location',
		function ($routeParams, $cookies, $http, $scope, xwsse, $location) {
			if(!$cookies.username || !$cookies.password) {
				$location.url("/app/login");
				return;
			}
			var passwordDigest = xwsse.calc($cookies.username, $cookies.password);
			$http.defaults.headers.get = {
				'X-WSSE': passwordDigest,
			};
			$http({
				method: 'GET',
				url: '/api/socios/' + $routeParams.socioId
			}).
			success(function (data) {
				$scope.socio = data;
			})
				.
			error(function (data, status) {
				$scope.error = {
					code: status,
					descripcion: ""
				}
			});
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