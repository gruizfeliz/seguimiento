'use strict';

angular.module('Home')

.controller('HomeController',
    ['$scope', '$rootScope', '$http', '$cookieStore', '$routeParams', '$location', 'AuthenticationService',
    function ($scope, $rootScope, $http, $cookieStore, $routeParams, $location, AuthenticationService) {
		$rootScope.hideMenus=false;
		$scope.MainSearchs={totalcount:1, totalpages:1, page:1, prevpagelink: '', nextpagelink:'', results: [], status:'Success'};
		$scope.SearchCMD={ api: '02', a: '1', t: $rootScope.MantenimientoActual, name: '', o: '0', p: 1, np: $rootScope.ItemsPerPage, tk: $rootScope.globals.currentUser.TOKEN };
		$rootScope.MenuClick = function(seleccion) {
			$scope.CurrentRow=0;
			$scope.AgregarLabel='Agregar';
			$scope.AgregarShow=true;
			$scope.EditarLabel='Editar';
			$scope.EditarShow=true;
			$scope.EliminarLabel='Eliminar';
			$scope.EliminarShow=true;
			$scope.Campo1Label='Nombre';
			$scope.Campo2Label='Descripcion';
			$scope.SearchOptions=[{id: 0, label: 'Todos'}];
			$scope.SearchOptionSelected=$scope.SearchOptions[$rootScope.SearchOptionSelected];
			if ('string' === typeof seleccion) {
				if ((seleccion === 't_vencidas') || (seleccion === 't_porvencer') || (seleccion === 't_nuevas')) {
					$rootScope.MantenimientoActualMenu='Tareas';
					$rootScope.MantenimientoActual='tareas';
				}
			}else{
				$rootScope.MantenimientoActualMenu=seleccion.NOMBRE;
				$rootScope.MantenimientoActual=seleccion.MANTENIMIENTO;
			}
			$scope.SearchCMD.p=1;
			$scope.SearchCMD.name='';	
			if ($rootScope.MantenimientoActual=='casos') {
				$scope.Campo1Label='Fecha-Estado';
				$scope.Campo2Label='Origen';
				$scope.AgregarLabel='Crear';
				$scope.EliminarLabel='Cerrar';
			}
			if ($rootScope.MantenimientoActual=='tareas') {
				$scope.Campo1Label='Fecha-Estado';
				$scope.Campo2Label='Caso-Descripcion';
				$scope.AgregarLabel='Crear';
				$scope.EliminarLabel='Cerrar';
				$scope.SearchOptions=[{id: 0, label: 'Todos'},{id: 1, label: 'Vencidas'},{id: 2, label: 'Nuevas'},{id: 3, label: 'En proceso'},{id: 4, label: 'Completadas'},{id: 5, label: 'Canceladas'},{id: 6, label: 'Por Vencer'}];
				$scope.SearchOptionSelected=$scope.SearchOptions[$rootScope.SearchOptionSelected];
			}
			if ($rootScope.MantenimientoActual=='consultas') {
				$scope.SearchOptions=[{id: 0, label: 'Tareas por Empleado'}];
				$scope.SearchOptionSelected=$scope.SearchOptions[0];
			}
			if ($rootScope.MantenimientoActual=='sucursales') {
				$scope.Campo1Label='Nombre';
				$scope.Campo2Label='Direccion';
			}
			if ($rootScope.MantenimientoActual=='empleados') {
				$scope.Campo1Label='Nombre';
				$scope.Campo2Label='Departamento';
			}
			if ($rootScope.MantenimientoActual=='departamentos') {
				$scope.Campo1Label='Nombre';
				$scope.Campo2Label='Encargado';
			}
			if ('string' === typeof seleccion) {
				if (seleccion === 't_vencidas') {
					$scope.SearchOptionSelected=$scope.SearchOptions[1];
					$rootScope.SearchOptionSelected=1;
					$location.path('/tareas');
				}
				if (seleccion === 't_porvencer') {
					$scope.SearchOptionSelected=$scope.SearchOptions[6];
					$rootScope.SearchOptionSelected=6;
					$location.path('/tareas');
				}
				if (seleccion === 't_nuevas') {
					$scope.SearchOptionSelected=$scope.SearchOptions[2];
					$rootScope.SearchOptionSelected=2;
					$location.path('/tareas');
				}
			}else{
				$rootScope.SearchOptionSelected=0;
			}
			$scope.BuscarREQUEST();
		};

		$rootScope.NotificationsREQUEST = function() {
			$scope.dataLoading=true;
			$http.post("./api.php", { api: '02', a: '1', t: 'notifications', tk: $rootScope.globals.currentUser.TOKEN }).
			success(function(dataFromServer, status, headers, config) {
				$scope.dataLoading=false;
				$rootScope.Notifications=dataFromServer;
			}).
			error(function(data, status, headers, config) {
				$scope.dataLoading=false;
				console.log(status+'-'+data);
				AuthenticationService.CheckCredentials(status,1);
			});
		};

		$scope.BuscarREQUEST = function() {
			$scope.dataLoading=true;
			$scope.SearchCMD.t=$rootScope.MantenimientoActual;
			$scope.SearchCMD.o=$scope.SearchOptionSelected.id;
			$scope.SearchCMD.tk=$rootScope.globals.currentUser.TOKEN;
			//console.log(angular.toJson($scope.SearchCMD));
			if ($rootScope.globals.currentUser){
				$http.post("./api.php", $scope.SearchCMD).
				success(function(dataFromServer, status, headers, config) {
					//alert(angular.toJson(dataFromServer));
					$scope.dataLoading=false;
					$scope.MainSearchs=dataFromServer;
					$scope.RowFill();
					$scope.RowSelect($scope.CurrentRow);
					console.log(angular.toJson($scope.MainSearchs));
				}).
				error(function(data, status, headers, config) {
					$scope.dataLoading=false;
					$scope.Resultados={};
					console.log(status+'-'+data);
					AuthenticationService.CheckCredentials(status,1);
				});
			}
		};

		$scope.Agregar = function () {
			$location.path('/'+$rootScope.MantenimientoActual+'/0/false/none');
			//console.log('/'+$rootScope.MantenimientoActual+'/0/false/none');
		};
		
		$scope.Editar = function () {
			$location.path('/'+$rootScope.MantenimientoActual+'/'+$scope.MainSearchs.results[$scope.CurrentRow].ID+'/false/none');
			//console.log('/'+$rootScope.MantenimientoActual+'/'+$scope.MainSearchs.results[$scope.CurrentRow].ID+'/false/none');
		};
		
		$scope.Eliminar = function () {
			var Mensaje=$scope.EliminarLabel.toLowerCase()+' el/la '+$rootScope.MantenimientoActual+' ';
			Mensaje=Mensaje.replace('es ', '');
			Mensaje=Mensaje.replace('os ', 'o');
			Mensaje=Mensaje.replace('as ', 'a');
			var retVal = confirm('Seguro que desea '+Mensaje+' ('+$scope.MainSearchs.results[$scope.CurrentRow].ID+' - '+$scope.MainSearchs.results[$scope.CurrentRow].C1+' - '+$scope.MainSearchs.results[$scope.CurrentRow].C2+')?');
			if (retVal == true) {
				$http.post("./api.php", { api: '05', t: $rootScope.MantenimientoActual, id: $scope.MainSearchs.results[$scope.CurrentRow].ID, tk: $rootScope.globals.currentUser.TOKEN }).
				success(function(dataFromServer, status, headers, config) {
					//alert(angular.toJson(dataFromServer));
					$scope.BuscarREQUEST();
					//console.log(angular.toJson($scope.MainSearchs));
				}).
				error(function(data, status, headers, config) {
					$scope.Resultados={};
					console.log(status+'-'+data);
					AuthenticationService.CheckCredentials(status,2);
				});
			}
		};
		
		$scope.Prev = function () {
			if ($scope.MainSearchs.page>1){
				$scope.SearchCMD.p=$scope.MainSearchs.page-1;
				$scope.CurrentRow=0;
				$scope.BuscarREQUEST();
			}
		};	

		$scope.Next = function () {
			if ($scope.MainSearchs.page<$scope.MainSearchs.totalpages){
				$scope.SearchCMD.p=$scope.MainSearchs.page+1;
				$scope.CurrentRow=0;
				$scope.BuscarREQUEST();
			}
		};
		
		$scope.RowFill = function (target,color) {
			for (var i = 0; i < $scope.MainSearchs.results.length; i++) {
				//console.log($scope.MainSearchs.results[i]);
				if (($scope.MainSearchs.results[i].C1==null) || ($scope.MainSearchs.results[i].C1=='')){
					$scope.MainSearchs.results[i].C1='-';
				}
				if (($scope.MainSearchs.results[i].C2==null) || ($scope.MainSearchs.results[i].C2=='')){
					$scope.MainSearchs.results[i].C2='-';
				}
				if ((target==0) || (target==1)){
					$scope.MainSearchs.results[i].Color=color;
				}
				if ((target==0) || (target==2)){
					$scope.MainSearchs.results[i].BackColor=color;
				}
			}
		};
		
		$scope.RowSelect = function (selected) {
			$scope.RowFill(1);
			$scope.MainSearchs.results[selected].Color='#FF4000';
			$scope.CurrentRow=selected;
		};
		
		$scope.MouseOverRow = function (selected) {
			$scope.RowFill(2);
			$scope.MainSearchs.results[selected].BackColor='#BDBDBD';
			$scope.CurrentRow=selected;
		};

		$rootScope.NotificationsREQUEST();
		if ($routeParams.mantenimiento) {
			for (var i = 0; i < $rootScope.globals.currentUser.Menus.length; i++) {
				if ($rootScope.globals.currentUser.Menus[i].MANTENIMIENTO==$routeParams.mantenimiento){
					$scope.MenuClick($rootScope.globals.currentUser.Menus[i]);
				}
			}
		}else{
			$scope.MenuClick($rootScope.globals.currentUser.Menus[0]);
		}
	}]);