'use strict';

angular.module('Departamentos')

.controller('DepartamentosController',
    ['$scope', '$rootScope', '$http', '$location', '$routeParams', '$window', '$timeout', 'AuthenticationService',
    function ($scope, $rootScope, $http, $location, $routeParams, $window, $timeout, AuthenticationService) {
		$rootScope.hideMenus=true;
		$scope.Departamento={ api: '0', t: 'departamentos', id: $routeParams.id, NOMBRE: '', IDEMPLEADO: 0, tk: $rootScope.globals.currentUser.TOKEN};
		$scope.ReadOnly=!$routeParams.readonly;
		$scope.ResponsableToggle=false;
		$scope.ResponsableColor=null;
		$scope.ResponsableTimeout=null;
		$scope.ResponsableSelected={ID: 0, C1: ''};
		$scope.Responsables=[];
		
		$scope.ResponsableSearch = function(event) {
			//if (event.keyCode==13){
			$scope.dataLoading = true;
			//console.log(angular.toJson($scope.Departamento));
			$http.post("./api.php", { api: '02', a: '1', t: 'empleados', name: $scope.ResponsableSelected.C1, p: 1, np: $rootScope.ItemsPerPage, tk: $rootScope.globals.currentUser.TOKEN }).
			success(function(dataFromServer, status, headers, config) {
				$scope.Responsables=dataFromServer.results;
				$scope.dataLoading = false;
				if ($scope.Responsables.length>0){
					$scope.ResponsableToggle=true;
					$scope.ResponsableColor='red';
					//document.getElementById("RESPONSABLE").focus();
					if($scope.ResponsableTimeout) {
						$timeout.cancel($scope.ResponsableTimeout)
					}
					$scope.ResponsableTimeout = $timeout(function() {
						//document.getElementById("RESPONSABLE").focus();
					}, $rootScope.SearchDelayTime);
				}else{
					$scope.ResponsableToggle=false;
				}
				console.log(angular.toJson(dataFromServer));
			}).
			error(function(data, status, headers, config) {
				$scope.dataLoading = false;
				console.log(status+'-'+data);
				AuthenticationService.CheckCredentials(status,1);
			});
			//}
		};
		
		$scope.ResponsableSelect = function() {
			$scope.ResponsableColor=null;
			$scope.ResponsableToggle=false;
		};
		
		$scope.LoadData = function() {
			$scope.dataLoading = true;
			$http.post("./api.php", { api: '02', a: '0', t: 'departamentos', id: $routeParams.id , tk: $rootScope.globals.currentUser.TOKEN }).
			success(function(dataFromServer, status, headers, config) {
				$scope.dataLoading = false;
				console.log(angular.toJson(dataFromServer));
				if (dataFromServer.results.length>0){
					$scope.Departamento.NOMBRE=dataFromServer.results[0].C1;
					$scope.Departamento.IDEMPLEADO=dataFromServer.results[0].C2;
					$scope.ResponsableSelected.ID=dataFromServer.results[0].C2;
					$scope.ResponsableSelected.C1=dataFromServer.results[0].C3;
				}
			}).
			error(function(data, status, headers, config) {
				$scope.dataLoading = false;
				console.log(status+'-'+data);
				AuthenticationService.CheckCredentials(status,2);
			});
		};
		
		$scope.SaveData = function() {
			$scope.Departamento.IDEMPLEADO=$scope.ResponsableSelected.ID;
			$scope.dataLoading = true;
			console.log(angular.toJson($scope.Departamento));
			console.log(angular.toJson($scope.ResponsableSelected));
			$http.post("./api.php", $scope.Departamento).
			success(function(dataFromServer, status, headers, config) {
				$scope.dataLoading = false;
				//console.log(dataFromServer);
				$window.history.back();
			}).
			error(function(data, status, headers, config) {
				$scope.dataLoading = false;
				console.log(status+'-'+data);
				AuthenticationService.CheckCredentials(status,2);
			});
		};
		
		$scope.Cancelar = function() {
			if ($rootScope.navigated) {
				$window.history.back();
				//$scope.$apply();
			}else{
				$location.path('/departamentos');
			}
		};
		
		$scope.LoadData();
    }]);