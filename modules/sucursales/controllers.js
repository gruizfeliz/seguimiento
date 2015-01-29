'use strict';

angular.module('Sucursales')

.controller('SucursalesController',
    ['$scope', '$rootScope', '$http', '$location', '$routeParams', '$window', 'AuthenticationService',
    function ($scope, $rootScope, $http, $location, $routeParams, $window, AuthenticationService) {
		$rootScope.hideMenus=true;
		$scope.Sucursal={ api: '0', t: 'sucursales', id: $routeParams.id, NOMBRE: '', DIRECCION: '', tk: $rootScope.globals.currentUser.TOKEN};
		$scope.ReadOnly=!$routeParams.readonly;
		$scope.LoadData = function() {
			$scope.dataLoading = true;
			$http.post("./api.php", { api: '02', a: '0', t: 'sucursales', id: $routeParams.id , tk: $rootScope.globals.currentUser.TOKEN }).
			success(function(dataFromServer, status, headers, config) {
				$scope.dataLoading = false;
				if (dataFromServer.results.length>0){
					$scope.Sucursal.NOMBRE=dataFromServer.results[0].C1;
					$scope.Sucursal.DIRECCION=dataFromServer.results[0].C2;
				}
				console.log(dataFromServer);
			}).
			error(function(data, status, headers, config) {
				$scope.dataLoading = false;
				console.log(status+'-'+data);
				AuthenticationService.CheckCredentials(status,2);
			});
		};
		
		$scope.SaveData = function() {
			$scope.dataLoading = true;
			//console.log(angular.toJson($scope.Sucursal));
			$http.post("./api.php", $scope.Sucursal).
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
				$location.path('/sucursales');
			}
		};
		
		$scope.LoadData();
    }]);