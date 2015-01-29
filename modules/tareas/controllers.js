'use strict';

angular.module('Tareas')

.controller('TareasController',
    ['$scope', '$rootScope', '$http', '$location', '$routeParams', '$window', '$timeout', 'AuthenticationService',
    function ($scope, $rootScope, $http, $location, $routeParams, $window, $timeout, AuthenticationService) {
		$rootScope.hideMenus=true;
		$scope.Tarea={ api: '0', t: 'tareas', id: $routeParams.id, IDCASO: 0, DESCRIPCION: '', DETALLES: '', RECORDATORIO: 1, IDEMPLEADO: 0, IDEMPLEADO_S: 0, CONCLUSIONES: '', tk: $rootScope.globals.currentUser.TOKEN};
		$scope.IDEMPLEADO_C=0;
		$scope.FECHA=new Date();
		$scope.FECHAENTREGA=null;
		$scope.FECHACIERRE=null;
		$scope.ESTADO='';
		$scope.CREADO='';
		$scope.TabPanel1=true;
		$scope.TabPanel2=false;
		$scope.TabPanel3=false;
		$scope.TabPanel3Visible=true;
		$scope.ReadOnly=!$routeParams.readonly;
		$scope.CasoToggle=false;
		$scope.CasoColor=null;
		$scope.CasoTimeout=null;
		$scope.CasoSelected={ID: 0, C1: ''};
		$scope.Casos=[];
		$scope.ResponsableToggle=false;
		$scope.ResponsableColor=null;
		$scope.ResponsableTimeout=null;
		$scope.ResponsableSelected={ID: 0, C1: ''};
		$scope.Responsables=[];
		$scope.SupervisorToggle=false;
		$scope.SupervisorColor=null;
		$scope.SupervisorTimeout=null;
		$scope.SupervisorSelected={ID: 0, C1: ''};
		$scope.Supervisors=[];
		
		
		$scope.CasoSearch = function(event) {
			//if (event.keyCode==13){
			$scope.dataLoading = true;
			//console.log(angular.toJson($scope.Tarea));
			$http.post("./api.php", { api: '02', a: '2', t: 'casos', name: $scope.CasoSelected.C1, p: 1, np: $rootScope.ItemsPerPage, tk: $rootScope.globals.currentUser.TOKEN }).
			success(function(dataFromServer, status, headers, config) {
				$scope.Casos=dataFromServer.results;
				$scope.dataLoading = false;
				if ($scope.Casos.length>0){
					$scope.CasoToggle=true;
					$scope.CasoColor='red';
					//document.getElementById("RESPONSABLE").focus();
					if($scope.CasoTimeout) {
						$timeout.cancel($scope.CasoTimeout)
					}
					$scope.CasoTimeout = $timeout(function() {
						//document.getElementById("RESPONSABLE").focus();
					}, $rootScope.SearchDelayTime);
				}else{
					$scope.CasoToggle=false;
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
		
		$scope.CasoSelect = function() {
			$scope.CasoColor=null;
			$scope.CasoToggle=false;
		};
		$scope.ResponsableSearch = function(event) {
			//if (event.keyCode==13){
			$scope.dataLoading = true;
			//console.log(angular.toJson($scope.Tarea));
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
		
		$scope.SupervisorSearch = function(event) {
			//if (event.keyCode==13){
			$scope.dataLoading = true;
			//console.log(angular.toJson($scope.Tarea));
			$http.post("./api.php", { api: '02', a: '1', t: 'empleados', name: $scope.SupervisorSelected.C1, p: 1, np: $rootScope.ItemsPerPage, tk: $rootScope.globals.currentUser.TOKEN }).
			success(function(dataFromServer, status, headers, config) {
				$scope.Supervisors=dataFromServer.results;
				$scope.dataLoading = false;
				if ($scope.Supervisors.length>0){
					$scope.SupervisorToggle=true;
					$scope.SupervisorColor='red';
					//document.getElementById("RESPONSABLE").focus();
					if($scope.SupervisorTimeout) {
						//$timeout.cancel($scope.SupervisorTimeout)
					}
					$scope.SupervisorTimeout = $timeout(function() {
						//document.getElementById("RESPONSABLE").focus();
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
			$http.post("./api.php", { api: '02', a: '0', t: 'tareas', id: $routeParams.id , tk: $rootScope.globals.currentUser.TOKEN }).
			success(function(dataFromServer, status, headers, config) {
				$scope.dataLoading = false;
				console.log(angular.toJson(dataFromServer));
				if (dataFromServer.results.length>0){
					$scope.FECHA=new Date(dataFromServer.results[0].C1);
					$scope.Tarea.IDCASO=dataFromServer.results[0].C2;
					$scope.CasoSelected.ID=dataFromServer.results[0].C2;
					$scope.CasoSelected.C1=dataFromServer.results[0].C3;
					$scope.Tarea.IDEMPLEADO=dataFromServer.results[0].C4;
					$scope.ResponsableSelected.ID=dataFromServer.results[0].C4;
					$scope.ResponsableSelected.C1=dataFromServer.results[0].C5;
					$scope.Tarea.IDEMPLEADO_S=dataFromServer.results[0].C6;
					$scope.SupervisorSelected.ID=dataFromServer.results[0].C6;
					$scope.SupervisorSelected.C1=dataFromServer.results[0].C7;
					$scope.Tarea.DESCRIPCION=dataFromServer.results[0].C8;
					$scope.Tarea.DETALLES=dataFromServer.results[0].C9;
					$scope.FECHAENTREGA=new Date(dataFromServer.results[0].C10);
					$scope.Tarea.RECORDATORIO=parseInt(dataFromServer.results[0].C11);
					$scope.FECHACIERRE=new Date(dataFromServer.results[0].C12);
					$scope.Tarea.CONCLUSIONES=dataFromServer.results[0].C13;
					$scope.ESTADO=dataFromServer.results[0].C14;
					$scope.IDEMPLEADO_C=dataFromServer.results[0].C15;
					$scope.CREADO=dataFromServer.results[0].C16;
					console.log(angular.toJson($scope.Tarea));
				}
			}).
			error(function(data, status, headers, config) {
				$scope.dataLoading = false;
				console.log(status+'-'+data);
				AuthenticationService.CheckCredentials(status,2);
			});
		};
		
		$scope.SaveData = function() {
			$scope.Tarea.IDEMPLEADO=$scope.ResponsableSelected.ID;
			$scope.Tarea.IDEMPLEADO_S=$scope.SupervisorSelected.ID;
			if ($scope.FECHAENTREGA!=null) {
				//$scope.Tarea.FECHAENTREGA=$scope.FECHAENTREGA.format("Y-m-d");
				$scope.Tarea.FECHAENTREGA=$scope.FECHAENTREGA.yyyymmdd();
			}
			if ($scope.FECHACIERRE!=null) {
				$scope.Tarea.FECHACIERRE=$scope.FECHACIERRE.yyyymmdd();
			}
			$scope.dataLoading = true;
			//console.log(angular.toJson($scope.Tarea));
			$http.post("./api.php", $scope.Tarea).
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
				$location.path('/tareas');
			}
		};
		
		$scope.LoadData();
    }]);