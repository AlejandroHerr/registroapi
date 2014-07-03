angular.module('libroApp.services', [ /*'ngResource'*/ ])
    .service('queryParams', function () {
        var config;
        config = {
            totalItems: '',
            currentPage: 1,
            maxItems: "25",
            by: 'id',
            dir: 'DESC'
        };
        return {
            get: function () {
                return config;
            },
            reset: function () {
                this.config = {
                    totalItems: '',
                    currentPage: 1,
                    maxItems: "25",
                    by: 'id',
                    dir: 'DESC'
                };
                return this.config;
            }
        };
    })
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