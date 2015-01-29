'use strict';

document.onkeydown = function (event) {
	
	if (!event) { /* This will happen in IE */
		event = window.event;
	}
		
	var keyCode = event.keyCode;
	
	if (keyCode == 8 &&
		((event.target || event.srcElement).tagName != "TEXTAREA") && 
		((event.target || event.srcElement).tagName != "INPUT")) { 
		
		if (navigator.userAgent.toLowerCase().indexOf("msie") == -1) {
			event.stopPropagation();
		} else {
			alert("prevented");
			event.returnValue = false;
		}
		
		return false;
	}
};

Date.prototype.yyyymmdd = function(){
	var yyyy = this.getFullYear().toString();
	var mm = (this.getMonth()+1).toString();
	var dd  = this.getDate().toString();
	return yyyy+'-'+(mm[1]?mm:"0"+mm[0])+'-'+(dd[1]?dd:"0"+dd[0]);
};

// declare modules
angular.module('Authentication', []);
angular.module('Home', []);
angular.module('Tareas', []);
angular.module('Casos', []);
angular.module('Consultas', []);
angular.module('Empleados', []);
angular.module('Departamentos', []);
angular.module('Sucursales', []);

angular.module('SeguimientoApp', [
    'Authentication',
    'Home',
    'Tareas',
    'Casos',
    'Consultas',
    'Empleados',
    'Departamentos',
    'Sucursales',
    'ngRoute',
    'ngCookies'
])

.directive('back', ['$window', function($window) {
        return {
            restrict: 'A',
            link: function (scope, elem, attrs) {
                elem.bind('click', function () {
                    $window.history.back();
                });
            }
        };
    }])

.directive('keyboardPoster', ['$timeout', function($timeout) {
  var DELAY_TIME_BEFORE_POSTING = 300;
  return function(scope, elem, attrs) {

    var element = angular.element(elem)[0];
    var currentTimeout = null;

    element.oninput = function() {
      var poster = scope[attrs.postFunction];
      if(currentTimeout) {
        $timeout.cancel(currentTimeout)
      }

      currentTimeout = $timeout(function() {
        poster(angular.element(element).val());
      }, DELAY_TIME_BEFORE_POSTING);
    }
  }
}])
	
.config(['$routeProvider', function ($routeProvider) {
    $routeProvider
		.when('/login', {
            controller: 'LoginController',
            templateUrl: 'modules/authentication/views/login.html'
        })
		.when('/', {
            controller: 'HomeController',
            templateUrl: 'modules/home/views/home.html'
        })
        .when('/:mantenimiento', {
            controller: 'HomeController',
            templateUrl: 'modules/home/views/home.html'
        })
		.when('/tareas/:id/:readonly/:params', {
            controller: 'TareasController',
            templateUrl: 'modules/tareas/views/tarea.html'
        })
		.when('/casos/:id/:readonly/:params', {
            controller: 'CasosController',
            templateUrl: 'modules/casos/views/caso.html'
        })
		.when('/consultas/:id/:readonly/:params', {
            controller: 'ConsultasController',
            templateUrl: 'modules/consultas/views/consulta.html'
        })
		.when('/empleados/:id/:readonly/:params', {
            controller: 'EmpleadosController',
            templateUrl: 'modules/empleados/views/empleado.html'
        })
		.when('/departamentos/:id/:readonly/:params', {
            controller: 'DepartamentosController',
            templateUrl: 'modules/departamentos/views/departamento.html'
        })
		.when('/sucursales/:id/:readonly/:params', {
            controller: 'SucursalesController',
            templateUrl: 'modules/sucursales/views/sucursal.html'
        })
        .otherwise({ redirectTo: '/login' });
}])

.run(['$rootScope', '$location', '$cookieStore', '$http', 'AuthenticationService',
    function ($rootScope, $location, $cookieStore, $http, AuthenticationService) {
        // keep user logged in after page refresh
		if ($location.path() !== '/login') {
			AuthenticationService.CheckCredentials();
		}
		$rootScope.Notifications={};
		$rootScope.ItemsPerPage=10;
		if ($rootScope.globals.currentUser) {
			$rootScope.MantenimientoActualMenu=$rootScope.globals.currentUser.Menus[0].NOMBRE || '';
			$rootScope.MantenimientoActual=$rootScope.globals.currentUser.Menus[0].MANTENIMIENTO || '';
		}else{
			$rootScope.MantenimientoActualMenu='';
			$rootScope.MantenimientoActual='';
		}
		$rootScope.SearchDelayTime=300;
		$rootScope.SearchOptionSelected=0;
		
		$rootScope.navigated = false;
		$rootScope.$on('$stateChangeSuccess', function (ev, to, toParams, from, fromParams) {
			if (from.name) { $rootScope.navigated = true; }
		}); 
		
        $rootScope.$on('$locationChangeStart', function (event, next, current) {
            // redirect to login page if not logged in
            if (($location.path() !== '/login') && !$rootScope.globals.currentUser) {
                $location.path('/login');
            }
        });
    }]);