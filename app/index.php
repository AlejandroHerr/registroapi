<!DOCTYPE HTML>
<html ng-app='libroApp'>
	<head>
		<title>ESN UAB Registro</title>
		<meta charset='utf-8'> 
		<link rel="stylesheet" href="/app/css/bootstrap.css">
		<link rel="stylesheet" href="/app/css/bootstrap-theme.css">
		<link rel="stylesheet" href="/app/css/bootstrap-custom-theme.css">
	</head>
	<body>
		<div ng-controller="OuterController">
			<?php include "partials/navbar.html";?>
		</div>
		<div class="jumbotron">
			
    		<div ng-view class="view-frame"></div>
  		</div>

  		<script src="/app/lib/angular.js-1.2.13/angular.min.js"></script>
	  	<script src="/app/lib/angular.js-1.2.13/angular-resource.min.js"></script>
	  	<script src="/app/lib/angular.js-1.2.13/angular-route.min.js"></script>
	  	<script src="/app/lib/angular.js-1.2.13/angular-cookies.min.js"></script>
		<script src="/app/js/app.js"></script>
	  	<script src="/app/js/controllers.js"></script>
	  	<script src="/app/js/services.js"></script>
	  	<script src="/app/js/services_security.js"></script>
	  	<script src="/app/js/services_api.js"></script>
	  	<script src="/app/lib/ui-bootstrap-tpls-0.10.0.min.js"></script>
		<script src="/app/lib/jquery-1.11.0.min.js"></script>
	</body>
</html>	