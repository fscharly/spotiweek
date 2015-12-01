var spotiweekApp = angular.module('spotiweekApp', []);

spotiweekApp.controller('spotiweekController', function($scope, $http) {

    $scope.init = function () {
        $scope.can_retry = false;
        $scope.is_copying = false;
        $scope.isAuth();
    }

    $scope.getPlaylist = function () {
        $http.get('/api/get_playlist').success(function (data) {
            $scope.currentPage = 0;
            $scope.playlistList = data.playlist.items;
        });
    }

    $scope.prevPlayListPage = function () {
        if ($scope.currentPage > 0) {
            $scope.currentPage--;
        }
        $scope.refreshPager();
    }

    $scope.refreshPager = function  () {
        if ($scope.currentPage == 0) {
            $('.prevPlaylistPage').addClass('disabled');
        } else {
            $('.prevPlaylistPage').removeClass('disabled');
        }
        if ($scope.currentPage + 1 == ($scope.playlistList.length / 5)) {
            $('.nextPlaylistPage').addClass('disabled');
        } else {
            $('.nextPlaylistPage').removeClass('disabled');
        }
    }

    $scope.nextPlayListPage = function () {
        if ($scope.currentPage + 1 < ($scope.playlistList.length / 5)) {
            $scope.currentPage++;
        }
        $scope.refreshPager();
    }

    $scope.getUserInfo = function () {
        $http.get('/api/get_user').success(function (data) {
            if (data.error == 0) {
                $scope.user = data.user;
            }
        });
    }

    $scope.retry = function () {
        $scope.copyWeeklyPlaylist();
    }

    $scope.clear = function () {
        $scope.stepList = new Array();
        $scope.is_copying = false;
        $scope.can_retry = false;
    }

    $scope.copyWeeklyPlaylist = function () {
        if ($scope.is_auth) {
            $scope.is_copying = true;
            $scope.stepList = new Array();
            $scope.stepList.push('Looking for Spotify Discover playlist...');
            $http.get('/api/find_discover_playlist').success(function (data) {
                if (data.error == 0) {
                    $scope.stepList.push('Spotify Discover found !');
                    $scope.discover_playlist_id = data.playlist_id;
                    $scope.stepList.push('Create a new playlist...');
                    $http.get('/api/create_playlist').success(function (data) {
                        if (data.error == 0) {
                            $scope.stepList.push('Done...');
                            $scope.getPlaylist(); // Reload playlist table
                            $scope.dest_playlist_id = data.playlist.id;
                            $scope.stepList.push('Copying tacks...');
                            $http.get('/api/copy_playlist', {
                                params : {
                                    src_playlist_id : $scope.discover_playlist_id,
                                    dest_playlist_id : $scope.dest_playlist_id
                                }
                            }).success(function (data) {
                                if (data.error == 0) {
                                    $scope.stepList.push('Done, your playlist is now available !');
                                    $scope.can_retry = true;
                                } else {
                                    $scope.stepList.push('Oops, something went wrong...');
                                    $scope.can_retry = true;
                                }

                            });
                        } else {
                            $scope.can_retry = true;
                            $scope.stepList.push(data.message);
                        }
                    });
                } else {
                    $scope.can_retry = true;
                    $scope.stepList.push(data.message);
                }
            })
        } else {
            $('.step-1').effect('shake', {}, 500, function () {});
        }
    }

    $scope.startRegister = function () {
        if ($scope.is_auth) {
            $('#teasing-modal').modal();
        } else {
            $('.step-1').effect('shake', {}, 500, function () {});
        }
    }

    $scope.isAuth= function () {
        $http.get('/api/is_auth').success(function(data) {
            $scope.is_auth = data.is_auth;
            if (data.is_auth) {
                $('.step-2, .step-3').removeClass('panel-disable');
                $scope.getUserInfo();
                $scope.getPlaylist();
            } else {
                $('.step-2, .step-3').addClass('panel-disable');
                $scope.user = null;
            }
        });
    }

    $scope.logout = function() {
        $http.get('/api/logout').success(function (data) {
            $scope.is_copying = false;
            $scope.isAuth();
        });
        return false;
    }

    $scope.login = function() {
        $http.get('/api/get_authentification_url').success(function (data) {
            window.location.href = data.url;
        })
    }
});
