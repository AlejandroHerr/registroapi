'use strict';
/* Services */
var libroServices = angular.module('libroServices', ['ngResource']);
libroServices.service('queryOptions', function () {
	var config;
	config = {
		orderBy: 'created_at',
		orderDir: 'DESC',
		currentPage: 1,
		maxResults: 25
	};
	return{
		get : function () {
			if(this.config){
				return this.config;
			}
			return this.reset();
			
		},
		getValue : function (value) {
			return this.config[value];
		},
		set : function (values) {
			this.config = values;
			return this;
		},
		reset : function () {
			return {
				orderBy: 'created_at',
				orderDir: 'DESC',
				currentPage: 1,
				maxResults: 25
			};
		}
	};
});
