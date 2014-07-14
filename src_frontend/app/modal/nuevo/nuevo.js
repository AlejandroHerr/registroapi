angular.module('libroApp.modal.nuevo', [])
    .controller('ModalNuevoCtrl', ['$modalInstance', '$scope', 'ApiCaller', 'credentials', 'item', 'url',
        function ($modalInstance, $scope, ApiCaller, credentials, item, url) {
            $scope.successFlag = false;
            $scope.failFlag = false;
            $scope.progress = "50";
            $scope.status = "progress-bar-warning";
            $scope.isCollapsed = false;
            var path = url;
            var data = ApiCaller.rawCall(credentials.getXWSSE(), 'POST', path, item)
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
            $scope.volver = function () {
                $modalInstance.close();
            };
            $scope.salir = function () {
                $modalInstance.dismiss();
            };
            //en caso afirmativo el tiene que ir a close, sino a dismiss
        }]);