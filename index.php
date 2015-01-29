<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="es" ng-app="SeguimientoApp">
<head>
	<title>Trace - Seguimiento</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <script src="//code.jquery.com/jquery-2.0.3.min.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css" rel="stylesheet">
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <script src="//code.angularjs.org/1.3.6/angular.js"></script>
    <script src="//code.angularjs.org/1.3.6/angular-route.js"></script>
    <script src="//code.angularjs.org/1.3.6/angular-cookies.js"></script>
    <script src="scripts/app.js"></script>
    <script src="modules/authentication/services.js"></script>
    <script src="modules/authentication/controllers.js"></script>
	<script src="modules/home/controllers.js"></script>
	<script src="modules/tareas/controllers.js"></script>
	<script src="modules/casos/controllers.js"></script>
	<script src="modules/consultas/controllers.js"></script>
	<script src="modules/empleados/controllers.js"></script>
	<script src="modules/departamentos/controllers.js"></script>
	<script src="modules/sucursales/controllers.js"></script>
</head>
<body>
	<nav data-ng-hide="hideMenus" id="myNavbar" class="navbar navbar-default" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#/">Seguimiento <strong>({{MantenimientoActualMenu}})</strong></a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li data-ng-repeat="Menu in globals.currentUser.Menus"><a href="#/{{Menu.MANTENIMIENTO}}" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">{{Menu.NOMBRE}}</a></li>
                    <li class="dropdown" data-ng-show="Notifications.results">
                        <a href="" data-toggle="dropdown" class="dropdown-toggle">Notificaciones <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li data-ng-repeat="Notification in Notifications.results"><a href="" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" data-ng-click="MenuClick(Notification.A)">{{Notification.M+' ('+Notification.C+')'}}</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle">{{globals.currentUser.NOMBRE}}<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="#/empleados/{{globals.currentUser.IDEMPLEADO}}/false/user">Configuracion</a></li>
                            <li class="divider"></li>
                            <li><a href="#/login">Salir</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="jumbotron">
        <div class="container">
			<div data-ng-view></div>
        </div>
    </div>
    <div class="credits text-center">
		<a href="http://tracesolar.com">tracesolar.com</a>
    </div>
</body>
</html>