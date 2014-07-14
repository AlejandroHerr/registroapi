angular.module('libroApp.users.user', [])
    .controller('UsersUserCtrl', ['$modal', '$stateParams', 'ApiCaller', '$scope', 'credentials', '$state',
        function ($modal, $stateParams, ApiCaller, $scope, credentials, $state) {
            var id = $stateParams.userId;
            $scope.loadUser = function () {
                var path = '/api/admin/users/' + id;
                var data = ApiCaller.modalCall(credentials.getXWSSE(), 'GET', path, null, function (d) {
                    $scope.user = d.data;
                });
            };
            $scope.checkLength = function (data, min, max) {
                if (data.length < min) {
                    return 'El tamano mínimo son ' + min + ' caracteres!';
                }
                if (data.length > max) {
                    return 'El tamano máximo son ' + max + ' caracteres!';
                }
            };
            $scope.isProtected = function (protected,element) {
                var pClass;
                if(protected === "1"){
                    pClass = { btn : 'btn-warning' , span : 'glyphicon-lock'};
                }else{
                    pClass = { btn : 'btn-success' , span : 'glyphicon-pencil'};

                }
                return pClass[element];
            };
            $scope.saveUser = function (data, created_at) {
                var putData = {
                    'name': data.name,
                    'surname': data.surname,
                    'esncard': data.esncard,
                    'country': data.country,
                    'passport': data.passport,
                    'email': data.email,
                    'created_at': created_at,
                    'language': data.language
                };
                var path = '/api/users/' + id;
                var data = ApiCaller.rawCall(credentials.getXWSSE(), 'PUT', path, putData)
                    .then(function () {
                        $scope.loadSocio();
                    }, function (d) {
                        var modalInstance = $modal.open({
                            templateUrl: 'modal/40x.tpl.html',
                            controller: 'ErrorModalInstanceCtrl',
                            resolve: {
                                error: function () {
                                    return d;
                                }
                            }
                        });
                        modalInstance.result.then(null, function () {
                            if (d.status === 401) {
                                $state.go('logout', {}, {
                                    location: true
                                });
                            } else if (d.status === 403 || d.status === 404) {
                                $state.go('logged', {}, {
                                    location: true
                                });
                            }
                        });
                        return 'error';
                    });
                return data;
            };
            $scope.loadUser();
        }]);