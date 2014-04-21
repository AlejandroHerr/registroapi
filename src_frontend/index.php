<!DOCTYPE HTML>
<html ng-app='libroApp'>
	<head>
		<title>ESN UAB Registro</title>
		<meta charset='utf-8'> 
		<!-- 
		<link rel="stylesheet" href="/app/css/bootstrap.css">
		<link rel="stylesheet" href="/app/css/bootstrap-theme.css">
		<link rel="stylesheet" href="/app/css/bootstrap-custom-theme.css">
		<link rel="stylesheet" href="/app/css/xeditable.css">
		-->
		<!-- compiled CSS --><% styles.forEach( function ( file ) { %>
    	<link rel="stylesheet" type="text/css" href="<%= file %>" /><% }); %>
	</head>
	<body>
		<div class='container'>
			<div ng-controller="OuterController">
				<?php include "partials/navbar.html";?>
				<div class='loader hide' ng-class="{show: isLoading}"></div>
			</div>
			<div id='view-container'>
				<div ng-view class="view-frame"></div>
			</div>
	  	</div>
	  	<div id='footer'>
	  		<div class='container'>
	  			<p>Proudly handcoded in the ESN UAB's basement<br>Code Hard, Party Harder!</p> 
	  		</div>
	  	</div>
	  	<!-- compiled JavaScript --><% scripts.forEach( function ( file ) { %>
    	<script type="text/javascript" src="<%= file %>"></script><% }); %>
		<!-- SCRIPTS
  		<script src="/app/lib/angular.js-1.2.13/angular.min.js"></script>
	  	<script src="/app/lib/angular.js-1.2.13/angular-resource.min.js"></script>
	  	<script src="/app/lib/angular.js-1.2.13/angular-route.min.js"></script>
	  	<script src="/app/lib/xeditable.min.js"></script>
		<script src="/app/js/app.js"></script>
	  	<script src="/app/js/controllers.js"></script>
	  	<script src="/app/js/controllers_bootstrap.js"></script>
	  	<script src="/app/js/services.js"></script>
	  	<script src="/app/js/services_security.js"></script>
	  	<script src="/app/js/services_api.js"></script>
	  	<script src="/app/lib/ui-bootstrap-tpls-0.10.0.min.js"></script>
	  	-->
	</body>
</html>	