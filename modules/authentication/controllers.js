'use strict';

angular.module('Authentication')

.controller('LoginController',
    ['$scope', '$rootScope', '$location', 'AuthenticationService',
    function ($scope, $rootScope, $location, AuthenticationService) {
        // reset login status
		$rootScope.hideMenus=true;
        AuthenticationService.ClearCredentials();

        $scope.login = function () {
            $scope.dataLoading = true;
            AuthenticationService.Login($scope.username, $scope.password, function (response) {
                if (response.TOKEN) {
					console.log(response);
                    AuthenticationService.SetCredentials(response.IDEMPLEADO, response.NOMBRE, response.TOKEN, response.Menus );
                    $location.path('/');
                } else {
                    $scope.error = response.message;
					$scope.dataLoading = false;
                }
            });
        };
    }]);