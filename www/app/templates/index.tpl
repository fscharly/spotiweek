{extends file="template.tpl"}
{block name=content}
<div class="head">
    <h1>Welcome on spotify-weekly</h1>
    <p>If you want to save you "Spotify Discover Weekly" playlist as a simple playlist that you can edit and keep forever, pleas connect with spotify</p>
</div>
<hr />
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-primary step-1">
            <div class="panel-heading">Step 1 : Connect with Spotify</div>
            <div class="panel-body">
                <p>Grant access to your spotify account.</p>
                <button class="btn btn-primary open-login-popup">Give access to your Spotify</button>
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
