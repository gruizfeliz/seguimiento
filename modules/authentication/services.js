'use strict';

angular.module('Authentication')

.factory('AuthenticationService',
    ['$http', '$cookieStore', '$rootScope', '$timeout', '$location',
    function ($http, $cookieStore, $rootScope, $timeout, $location) {
        var service = {};

        service.Login = function (username, password, callback) {
            $http.post('./api.php', { api: '03', a: '3', t: 'users', uname: username, upass: password })
                .success(function (response) {
                    callback(response);
                })
				.error(function (response) {
                    callback(response);
                });

        };

        service.SetCredentials = function (IDEMPLEADO, NOMBRE, TOKEN, Menus) {
            $rootScope.globals = {
                currentUser: {
                    IDEMPLEADO: IDEMPLEADO,
					NOMBRE: NOMBRE,
                    TOKEN: TOKEN,
					Menus: Menus
                }
            };
			console.log($rootScope.globals.currentUser);
            $cookieStore.put('globals', $rootScope.globals);
        };

        service.ClearCredentials = function () {
            $rootScope.globals = {};
            $cookieStore.remove('globals');
        };
		
		service.CheckCredentials = function (code,type) {
			$rootScope.globals = $cookieStore.get('globals') || {};
				if ($rootScope.globals.currentUser) {
				if ('undefined' === typeof code) {
					$http.post('./api.php', { api: '0', t: 'users', tk: $rootScope.globals.currentUser.TOKEN }).
					error(function(data, status, headers, config) {
						code=status;
					});
				}
				if (code==401){
					if (type==1){
						alert('Datos de Acceso no Validos');
						$rootScope.globals = {};
						$cookieStore.remove('globals');
						$location.path('/login');
					}else{
						alert('No tiene acceso');
						$window.history.back();
					}
				}
				if (code==403){
					alert('No tiene permisos para realizar esta accion.');
				}
			}
		};

        return service;
    }]
);