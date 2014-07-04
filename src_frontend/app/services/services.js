angular.module('libroApp.services', [ /*'ngResource'*/ ])
    .service('loader', [
        function () {
            return {
                isLoading: function () {
                    return this.loadingState;
                },
                setLoading: function () {
                    this.loadingState = true;
                },
                unsetLoading: function () {
                    this.loadingState = false;
                }
            };
        }
    ]);