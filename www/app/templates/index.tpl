{extends file="template.tpl"}
{block name=content}
{if isset($flash.error)}
<div class="alert alert-danger" role="alert">{$flash.error}</div>
{/if}
<div class="row" ng-controller="spotiweekController" ng-init="init()">
    <div class="col-md-4">
        <div class="panel panel-primary step-1">
            <div class="panel-heading">Step 1 : Connect with Spotify</div>
            <div class="panel-body">
                <!-- User not logged in -->
                <p ng-hide="is_auth">
                    Grant access to your spotify account.
                </p>
                <button ng-hide="is_auth" class="btn btn-primary login-button ng-hide" ng-click="login()">Give access to your Spotify</button>
                <!-- End user not logged in -->
                <!-- User logged in -->
                <div ng-show="is_auth">
                    <div ng-show="is_auth">
                        <div class="col-md-9 pull-right">
                            <div ng-show="is_auth" class="ng-hide" ng-bind="user.display_name"></div>
                            <div ng-show="is_auth" class="ng-hide"><a ng-click="logout()" href="">Log out</a></div>
                            <div ng-show="is_auth" class="ng-hide"><input type="checkbox"> Display spotiweek playlists only</div>
                        </div>
                        {literal}<img class=" img-circle" height="80" ng-show="is_auth" ng-src="{{user.images[0].url}}" />{/literal}

                    </div>
                    <table ng-show="is_auth" class="table table-striped table-condensed table-playlist">
                        <thead>
                            <tr>
                                <th>Playlists</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="playlist in playlistList | limitTo:5:currentPage*5">
                                <td>
                                    <span ng-bind="playlist.name"></span>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>
                                    <ul class="pagination pagination-sm pagination-playlist">
                                        <li class="prevPlaylistPage disabled">
                                            <a ng-click="prevPlayListPage()" href="" aria-hidden="true">&laquo;</a>
                                        </li>
                                        <li class="disabled">
                                            {literal}<span>{{currentPage + 1}} / {{playlistList.length/5}}</span>{/literal}
                                        </li>
                                        <li class="nextPlaylistPage">
                                            <a href="" ng-click="nextPlayListPage()" aria-hidden="true">&raquo;</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- End user logged in -->
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-primary step-2">
            <div class="panel-heading">Step 2 : Copy week discover</div>
            <div class="panel-body">
                <p ng-hide="is_copying" >Click on the button to create the playlist.</p>
                <button ng-hide="is_copying" class="btn btn-primary" ng-click="copyWeeklyPlaylist()">Copy songs into a playlist</button>
                <p ng-show="is_copying" class="result" ng-repeat="step in stepList">
                    <span ng-bind="step"></span>
                </p>
                <button class="btn btn-primary" ng-click="retry()" ng-show="can_retry" >Retry</button>
                <button class="btn btn-default" ng-click="clear()" ng-show="can_retry" >Clear</button>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-primary step-3">
            <div class="panel-heading">Step 3 (optional) : Do it every Monday</div>
            <div class="panel-body">
                <p>You can register to make the playlist copy automatic.</p>
                <button class="btn btn-primary" ng-click="startRegister()">Register</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="teasing-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Comming soon !</h4>
            </div>
            <div class="modal-body">
                This feature will be available soon. Check <a target="_blank" href="https://github.com/fscharly/spotiweek">project repository</a>on github to stay tuned.
            </div>
        </div>
    </div>
</div>
<!-- End Modal -->
<script type="text/javascript" src="assets/js/index.js"></script>
{/block}
