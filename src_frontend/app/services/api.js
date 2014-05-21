angular.module('libroApp.api', [])
    .factory('ApiCall', ['$http', '$modal', '$state', 'loader',
        function ($http, $modal, $state, loader) {
            $http.defaults.useXDomain = true;
            delete $http.defaults.headers.common['X-Requested-With'];
            var domain = 'remoteBackendURI';
            var makeCall = function (passwordDigest, method, path, data) {
                loader.setLoading();
                $http.defaults.headers.common = {
                    'X-WSSE': passwordDigest
                };
                var promise = $http({
                        method: method,
                        url: domain + path,
                        data: data
                    })
                    .then(function (response) {
                        return response;
                    });
                promise['finally'](function () {
                    loader.unsetLoading();
                });
                return promise;
            };
            var apiCall = function (passwordDigest, method, path, data, successCb) {
                makeCall(passwordDigest, method, path, data)
                    .then(function (d) {
                        successCb(d);
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
                        modalInstance.result.then(function () {}, function () {
                            if (d.status == 401) {
                                $state.go('logout', {}, {
                                    location: true
                                });
                            }
                            else {
                                $state.go('logged', {}, {
                                    location: true
                                });
                            }
                        });
                    }, function (d) {

                    });

            };
            return {
                apiCall: apiCall,
                makeCall: makeCall
            }
        }
    ]);