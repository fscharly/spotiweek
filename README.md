# spotify-weekly
[Spotify Discover](https://press.spotify.com/it/2015/07/20/introducing-discover-weekly-your-ultimate-personalised-playlist/) give weekly playlist of songs you may like. This playlist disappear every Monday. This is a web-app that copy your weekly recommendations in a "normal" playlist so you can edit it and keep it forever.

## Install
Create a config file named *api_token.json* in *www/app/config/*.

It must look like this :
```
{
    "client_id" : "Spotify client id",
    "client_secret" : "Spotify client secret"
}
```

You can get api_token and api_secret from [https://developer.spotify.com](https://developer.spotify.com).
