<?php

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" ng-app="myApp">
<head>
    <title>Pruebas AngularJS/API</title>
    <script type="text/javascript" src="http://code.angularjs.org/1.2.25/angular.min.js"></script>
    <script type="text/javascript">
var app = angular.module('myApp', []);

app.controller('TestAPI', function ($scope, $http) {
	//alert("Entro");
	$scope.parametros = "{\"api\":\"1\",\"t\":\"\"}";
	$scope.resultado = "";
    $scope.TestSEND = function() {
		//alert($scope.parametros);
        console.log(angular.toJson($scope.parametros));
		//$http.defaults.headers.post['Content-Type'] = 'application/json;  charset=UTF-8';
		//console.log(angular.toJson($http.defaults.headers.post));
		var responsePromise = $http.post("./api.php", $scope.parametros);
		responsePromise.success(function(dataFromServer, status, headers, config) {
			//alert(angular.toJson(dataFromServer));
			$scope.resultado=dataFromServer;
			console.log(angular.toJson(dataFromServer));
		});
		responsePromise.error(function(data, status, headers, config) {
			//alert("Submitting form failed!");
			console.log(data);
		});
    };	
 });	
	</script>
    <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body class="container">
<h2>Pruebas AngularJS/API</h2>
<div ng-controller="TestAPI">
	<form>
		<br> parametros: <input type="text" id="name" ng-model="parametros" ng-change="TestSEND()"></br>
		<button ng-click="TestSEND()">Probar</button>
	</form>
	<div>{{resultado}}
	</div>
</div>
</body>
</html>