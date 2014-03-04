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