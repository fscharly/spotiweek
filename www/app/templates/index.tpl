{extends file="template.tpl"}
{block name=content}
<div class="head">
    <h1>Welcome on <span class="primary-text">Spoti</span>Week</h1>
    <p>Spotify Discover gives weekly playlist of songs you may like. This playlist disappears every Monday. Herre you can copy your weekly recommendations in a classic playlist so you can edit it and keep it forever.</p>
</div>
<hr />
{if isset($flash.error)}
<div class="alert alert-danger" role="alert">{$flash.error}</div>
{/if}
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-primary step-1">
            <div class="panel-heading">Step 1 : Connect with Spotify</div>
            <div class="panel-body">
                <p ng-bind="step1_text"></p>
                <button ng-hide="is_login" class="btn btn-primary login-button ng-hide" ng-click="login()">Give access to your Spotify</button>
                <button ng-show="is_login" class="btn btn-primary logout-button ng-hide" ng-click="logout()">Log out</button>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-primary step-2">
            <div class="panel-heading">Step 2 : Copy week discover</div>
            <div class="panel-body">
                <p>Click on the button to create the playlist.</p>
                <a href="#" class="btn btn-primary">Copy songs into a playlist</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-primary step-3">
            <div class="panel-heading">Step 3 (optional) : Do it every Monday</div>
            <div class="panel-body">
                <p>You can register to make the playlist copy automatic.</p>
                <a href="#" class="btn btn-primary">Register</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="assets/js/index.js"></script>
{/block}
