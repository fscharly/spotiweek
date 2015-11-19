var spotiweekApp = angular.module('spotiweekApp', []);

spotiweekApp.controller('spotiweekController', function($scope, $http) {
    $scope.is_login = false;

    $scope.init = function () {
        $scope.isRegister();
    }

    $scope.isRegister = function () {
        $http.get('/api/islogin').success(function(data) {
            $scope.is_login = data.is_login;
            if (data.is_login) {
                $scope.step1_text = "Thank you for trusting us.";
                $('.step-2, .step-3').removeClass('panel-disable');
            } else {
                $scope.step1_text = "Grant access to your spotify account.";
                $('.step-2, .step-3').addClass('panel-disable');
            }
        });
    }

    $scope.logout = function() {
        $http.get('/api/logout').success(function (data) {
            $scope.isRegister();
        })
    }

    $scope.login = function() {
        $http.get('/api/get_authentification_url').success(function (data) {
            window.location.href = data.url;
        })
    }
});
