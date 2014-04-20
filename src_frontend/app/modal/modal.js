angular.module('libroApp.modals', [])
    .controller('DeleteModalInstanceCtrl', ['ApiCall', 'credenciales', '$scope', '$modalInstance', 'socio',
        function(ApiCall, credenciales, $scope, $modalInstance, socio) {
            $scope.socio = socio;
            $scope.alerts = [{
                type: 'warning',
                msg: '¡Esta acción no se puede deshacer!'
            }];
            $scope.addAlert = function(Type, Msg) {
                $scope.alerts.push({
                    type: Type,
                    msg: Msg
                });
            };
            $scope.closeAlert = function(index) {
                $scope.alerts.splice(index, 1);
            };
            $scope.deletAlerts = function() {
                $scope.alerts = '';
            }
            $scope.salir = function() {
                $modalInstance.dismiss();
            };
            $scope.ok = function(str) {
                if (str == 'ELIMINAR ' + $scope.socio.esncard) {
                    $scope.addAlert('success',
                        'Nuestros monos lo están borrando... Espera y no toques nada.');
                    var data = ApiCall.deleteSocio(credenciales.getXWSSE(), $scope.socio.id)
                        .then(function(d) {
                                $modalInstance.close();
                            },
                            function(d) {
                                if (d.status == 401) {
                                    $scope.addAlert('danger', '¡Tus credenciales son incorrectas..!');
                                } else if (d.status == 403) {
                                    $scope.addAlert('danger', '¡No tienes permiso para borrar socios!');
                                } else {
                                    $scope.addAlert('danger', '¡Un error no esperado a sucedido!');
                                }
                            })
                } else {
                    $scope.addAlert('danger',
                        '¡Tienes que escribir la confirmación correcta!');
                }
            };
        }
    ])
    .controller('ErrorModalInstanceCtrl', ['$scope', '$modalInstance', 'error',
        function($scope, $modalInstance, error) {
            $scope.error = error;
            if (error.status == 401) {
                $scope.name = 'No autorizado';
                $scope.msg =
                    'Parece ser que no te has loggeado correctamente. Puede ser que seas un piratilla o que tengas los dedos muy gordos y hayas tecleado muy mal. En todo caso, no te preocupes, la polic&iacute; se drige a tu casa para solucionarlo.';
            } else if (error.status == 403) {
                $scope.name = 'Acceso prohibido';
                $scope.msg =
                    'Parece ser que no tienes acceso para entrar en esta sección. Pide acceso en la próxima asamblea general.';
            } else {
                $scope.name = 'Error no previsto';
                $scope.msg =
                    'Ha ocurrido un error raro que Alejandro I el Hermoso no tuvo en cuenta, así; que no te podemos dar más detalles.';
            }
            $scope.salir = function() {
                $modalInstance.dismiss();
            };
        }
    ])
    .controller('RegistrarModalInstanceCtrl', ['ApiCall', 'credenciales', '$scope',
        '$modalInstance', 'socio',
        function(ApiCall, credenciales, $scope, $modalInstance, socio) {
            $scope.successFlag = false;
            $scope.failFlag = false;
            $scope.progress = "50";
            $scope.status = "progress-bar-warning";
            $scope.isCollapsed = false;
            var data = ApiCall.postSocio(socio, credenciales.getXWSSE())
                .then(function(d) {
                    $scope.successFlag = true;
                    $scope.progress = "100";
                    $scope.status = "progress-bar-success";
                }, function(d) {
                    $scope.failFlag = true;
                    $scope.progress = "100";
                    $scope.status = "progress-bar-danger";
                    $scope.errors = d.data;
                });
            $scope.volver = function() {
                $modalInstance.close();
            }
            $scope.salir = function() {
                $modalInstance.dismiss();
            };


            //en caso afirmativo el tiene que ir a close, sino a dismiss
        }
    ]);
