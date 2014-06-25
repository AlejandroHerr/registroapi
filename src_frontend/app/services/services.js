angular.module('libroApp.services', [ /*'ngResource'*/ ])
    .service('queryOptions', function() {
        var config;
        config = {
            orderBy: 'created_at',
            orderDir: 'DESC',
            currentPage: 1,
            maxResults: 25
        };
        return {
            get: function() {
                if (this.config) {
                    return this.config;
                }
                return this.reset();
            },
            getValue: function(value) {
                return this.config[value];
            },
            set: function(values) {
                this.config = values;
                return this;
            },
            reset: function() {
                return {
                    orderBy: 'created_at',
                    orderDir: 'DESC',
                    currentPage: 1,
                    maxResults: 25
                };
            }
        };
    })
    .service('queryParams', function() {
        var config;
        config = {
            by: 'id',
            dir: 'DESC',
            page: '1',
            max: 25
        };
        return {
            get: function() {
                return config;
            },
            getValue: function(value) {
                return this.config[value];
            },
            set: function(values) {
                this.config = values;
                return this;
            },
            reset: function() {
                return {
                    by: 'id',
                    dir: 'DESC',
                    page: 1,
                    max: 25
                };
            }
        };
    })
    .service('loader', [

        function() {
            var loadingState = false;
            return {
                isLoading: function() {
                    return this.loadingState;
                },
                setLoading: function() {
                    this.loadingState = true;
                },
                unsetLoading: function() {
                    this.loadingState = false;
                }
            };
        }
    ]);
