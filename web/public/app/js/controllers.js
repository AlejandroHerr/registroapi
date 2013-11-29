'use strict';

/* Controllers */

var libroControllers = angular.module('libroControllers', []);

libroControllers.controller('LoginCtrl',
	['$scope','login', function($scope,login){
		$scope.logIn = function(){
			event.preventDefault();
			login.setUsername($scope.username);
			login.setPassword($scope.password);
			login.logIn();
		};
	}]
);

libroControllers.controller('InicioCtrl',
	['$scope', function($scope){
		
	}]
);

libroControllers.controller('LibroCtrl',
	['$scope','xwsse','login', function($scope,xwsse,login){
		
		$scope.susername = xwsse.calc(login.getUsername(),login.getPassword());
		$scope.spassword = xwsse.calc($scope.username,$scope.password);
	}]
);

//5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==