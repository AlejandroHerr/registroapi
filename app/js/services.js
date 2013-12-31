'use strict';
/* Services */
var libroServices = angular.module('libroServices', ['ngResource']);
libroServices.service('optiones', function () {
	this.config = {
		orderBy: 'created_at',
		orderDir: 'DESC',
		currentPage: 1,
		maxResults: 25
	}
	this.get = function () {
		return this.config;
	}
	this.getValue = function (value) {
		return this.config[value];
	}
	this.set = function (values) {
		this.config = values;
		return this;
	}
	this.reset = function () {
		return {
			orderBy: 'created_at',
			orderDir: 'DESC',
			currentPage: 1,
			maxResults: 25
		};
	}
});