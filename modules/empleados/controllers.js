'use strict';

angular.module('Empleados')

.controller('EmpleadosController',
    ['$scope', '$rootScope', '$http', '$location', '$routeParams', '$window', '$timeout', 'AuthenticationService',
    function ($scope, $rootScope, $http, $location, $routeParams, $window, $timeout, AuthenticationService) {
		$rootScope.hideMenus=true;
		$scope.Empleado={ api: '0', t: 'empleados', id: $routeParams.id, NOMBRE: '', APELLIDO: '', TELEFONOS: '', CORREO: '', IDSUCURSAL: 0, IDDEPARTAMENTO: 0, IDEMPLEADO_S: 0, NUSUARIO: '', CUSUARIO: '', tk: $rootScope.globals.currentUser.TOKEN};
		$scope.TabPanel1=true;
		$scope.TabPanel2=false;
		$scope.TabPanel3=false;
		$scope.TabPanel3Visible=false;
		$scope.ReadOnly=!$routeParams.readonly;
		$scope.SucursalToggle=false;
		$scope.SucursalColor=null;
		$scope.SucursalTimeout=null;
		$scope.SucursalSelected={ID: 0, C1: ''};
		$scope.Sucursals=[];
		$scope.DepartamentoToggle=false;
		$scope.DepartamentoColor=null;
		$scope.DepartamentoTimeout=null;
		$scope.DepartamentoSelected={ID: 0, C1: ''};
		$scope.Departamentos=[];
		$scope.SupervisorToggle=false;
		$scope.SupervisorColor=null;
		$scope.SupervisorTimeout=null;
		$scope.SupervisorSelected={ID: 0, C1: ''};
		$scope.Supervisors=[];
		
		$scope.SucursalSearch = function(event) {
			//if (event.keyCode==13){
			$scope.dataLoading = true;
			//console.log(angular.toJson($scope.Empleado));
			$http.post("./api.php", { api: '02', a: '1', t: 'sucursales', name: $scope.SucursalSelected.C1, p: 1, np: $rootScope.ItemsPerPage, tk: $rootScope.globals.currentUser.TOKEN }).
			success(function(dataFromServer, status, headers, config) {
				$scope.Sucursals=dataFromServer.results;
				$scope.dataLoading = false;
				if ($scope.Sucursals.length>0){
					$scope.SucursalToggle=true;
					$scope.SucursalColor='red';
					//document.getElementById("SUCURSAL").focus();
					if($scope.SucursalTimeout) {
						$timeout.cancel($scope.SucursalTimeout)
					}
					$scope.SucursalTimeout = $timeout(function() {
						//document.getElementById("SUCURSAL").focus();
					}, $rootScope.SearchDelayTime);
				}else{
					$scope.SucursalToggle=false;
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
		
		$scope.SucursalSelect = function() {
			$scope.SucursalColor=null;
			$scope.SucursalToggle=false;
		};

		$scope.DepartamentoSearch = function(event) {
			//if (event.keyCode==13){
			$scope.dataLoading = true;
			//console.log(angular.toJson($scope.Empleado));
			$http.post("./api.php", { api: '02', a: '1', t: 'departamentos', name: $scope.DepartamentoSelected.C1, p: 1, np: $rootScope.ItemsPerPage, tk: $rootScope.globals.currentUser.TOKEN }).
			success(function(dataFromServer, status, headers, config) {
				$scope.Departamentos=dataFromServer.results;
				$scope.dataLoading = false;
				if ($scope.Departamentos.length>0){
					$scope.DepartamentoToggle=true;
					$scope.DepartamentoColor='red';
					//document.getElementById("DEPARTAMENTO").focus();
					if($scope.DepartamentoTimeout) {
						$timeout.cancel($scope.DepartamentoTimeout)
					}
					$scope.DepartamentoTimeout = $timeout(function() {
						//document.getElementById("DEPARTAMENTO").focus();
					}, $rootScope.SearchDelayTime);
				}else{
					$scope.DepartamentoToggle=false;
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
		
		$scope.DepartamentoSelect = function() {
			$scope.DepartamentoColor=null;
			$scope.DepartamentoToggle=false;
		};
		
		$scope.SupervisorSearch = function(event) {
			//if (event.keyCode==13){
			$scope.dataLoading = true;
			//console.log(angular.toJson($scope.Empleado));
			$http.post("./api.php", { api: '02', a: '1', t: 'empleados', name: $scope.SupervisorSelected.C1, p: 1, np: $rootScope.ItemsPerPage, tk: $rootScope.globals.currentUser.TOKEN }).
			success(function(dataFromServer, status, headers, config) {
				$scope.Supervisors=dataFromServer.results;
				$scope.dataLoading = false;
				if ($scope.Supervisors.length>0){
					$scope.SupervisorToggle=true;
					$scope.SupervisorColor='red';
					//document.getElementById("SUPERVISOR").focus();
					if($scope.SupervisorTimeout) {
						//$timeout.cancel($scope.SupervisorTimeout)
					}
					$scope.SupervisorTimeout = $timeout(function() {
						//document.getElementById("SUPERVISOR").focus();
					}, $rootScope.SearchDelayTime);
				}else{
					$scope.SupervisorToggle=false;
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
		
		$scope.SupervisorSelect = function() {
			$scope.SupervisorColor=null;
			$scope.SupervisorToggle=false;
		};
		
		$scope.TabNavigation = function(position) {
			$scope.TabPanel1=false;
			$scope.TabPanel2=false;
			$scope.TabPanel3=false;
			switch (position) {
				case 1:
						$scope.TabPanel1=true;
						break;
				case 2:
						$scope.TabPanel2=true;
						break;
				case 3:
						$scope.TabPanel3=true;
						break;
				default:
						$scope.TabPanel1=true;
			}
		};
		
		$scope.LoadData = function() {
			$scope.dataLoading = true;
			$http.post("./api.php", { api: '02', a: '0', t: 'empleados', id: $routeParams.id , tk: $rootScope.globals.currentUser.TOKEN }).
			success(function(dataFromServer, status, headers, config) {
				$scope.dataLoading = false;
				//console.log(angular.toJson(dataFromServer));
				if (dataFromServer.results.length>0){
					$scope.Empleado.NOMBRE=dataFromServer.results[0].C1;
					$scope.Empleado.APELLIDO=dataFromServer.results[0].C2;
					$scope.Empleado.TELEFONOS=dataFromServer.results[0].C3;
					$scope.Empleado.CORREO=dataFromServer.results[0].C4;
					$scope.Empleado.IDSUCURSAL=dataFromServer.results[0].C5;
					$scope.SucursalSelected.ID=dataFromServer.results[0].C5;
					$scope.SucursalSelected.C1=dataFromServer.results[0].C6;
					$scope.Empleado.IDDEPARTAMENTO=dataFromServer.results[0].C7;
					$scope.DepartamentoSelected.ID=dataFromServer.results[0].C7;
					$scope.DepartamentoSelected.C1=dataFromServer.results[0].C8;
					$scope.Empleado.IDEMPLEADO_S=dataFromServer.results[0].C9;
					$scope.SupervisorSelected.ID=dataFromServer.results[0].C9;
					$scope.SupervisorSelected.C1=dataFromServer.results[0].C10;
					$scope.Empleado.NUSUARIO=dataFromServer.results[0].C11;
				}
			}).
			error(function(data, status, headers, config) {
				$scope.dataLoading = false;
				console.log(status+'-'+data);
				AuthenticationService.CheckCredentials(status,2);
			});
		};
		
		$scope.SaveData = function() {
			$scope.Empleado.IDSUCURSAL=$scope.SucursalSelected.ID;
			$scope.Empleado.IDDEPARTAMENTO=$scope.DepartamentoSelected.ID;
			$scope.Empleado.IDEMPLEADO_S=$scope.SupervisorSelected.ID;
			$scope.dataLoading = true;
			//console.log(angular.toJson($scope.Empleado));
			$http.post("./api.php", $scope.Empleado).
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
				$location.path('/empleados');
			}
		};
		
		$scope.LoadData();
		if (($rootScope.globals.currentUser.IDEMPLEADO==0) || ($rootScope.globals.currentUser.IDEMPLEADO==$routeParams.id)){
			$scope.TabPanel3Visible=true;
		}
		if ($routeParams.params=='user'){
			$scope.TabNavigation(3);
		}
    }]);