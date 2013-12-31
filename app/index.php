<!DOCTYPE HTML>
<html ng-app='libroApp'>
	<head>
		<title>ESN UAB Registro</title>
		<meta charset='utf-8'> 
		<link rel="stylesheet" href="/app/css/bootstrap.css">
		<link rel="stylesheet" href="/app/css/bootstrap-theme.css">
		<link rel="stylesheet" href="/app/css/bootstrap-custom-theme.css">
		<link href="/app/css/xeditable.css" rel="stylesheet">



	</head>
	<body>
		<?php include "partials/navbar.html";?>
		<div class="jumbotron">
    		<div ng-view class="view-frame"></div>
  		</div>

  		<script src="/app/lib/angular/angular.min.js"></script>
	  	<script src="/app/lib/angular/angular-resource.min.js"></script>
	  	<script src="/app/lib/angular/angular-route.min.js"></script>
	  	<script src="/app/lib/angular/angular-cookies.min.js"></script>
		<script src="/app/js/app.js"></script>
	  	<script src="/app/js/controllers.js"></script>
	  	<script src="/app/js/services.js"></script>
	  	<script src="/app/js/services_security.js"></script>
	  	<script src="/app/js/services_api.js"></script>
	  	<script src="/app/lib/ui-bootstrap-tpls-0.7.0.js"></script>
		<script src="/app/lib/jQuery.min.js"></script>
		<script src="/app/lib/xeditable.min.js"></script>	
	</body>
</html>	