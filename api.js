angular.module('libroApp.api', [])

.factory('ApiCall', ['$http',
    function($http) {
        $http.defaults.useXDomain = true;
        delete $http.defaults.headers.common['X-Requested-With'];
        var ApiCall = {
            makeCall: function(passwordDigest,method,path,data) {
                $http.defaults.headers.common = {
                    'X-WSSE': passwordDigest
                };
                var promise = $http({
                    method: method,
                    url: path,
                    data : data
                }).then(function(response) {
                    return response;
                });
                return promise;
            }
        };
        return ApiCall;
    }
]);
