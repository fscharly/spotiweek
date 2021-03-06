# SpotiWeek
[Spotify Discover](https://press.spotify.com/it/2015/07/20/introducing-discover-weekly-your-ultimate-personalised-playlist/) gives weekly playlist of songs you may like. This playlist disappears every Monday. This is a web-app that copy your weekly recommendations in a classic playlist so you can edit it and keep it forever.

## Project
The goal of this project is not to disrupt the entire music industry ;). It's just a way to explore :
* [Slim framework](http://www.slimframework.com)
* [Spotify API](https://developer.spotify.com/web-api/)
* O-Auth authentication
* [Angular JS](https://angularjs.org)
* Composer
* Github
* Markdown

And having fun by building a simple (but very well architectured ! ;) ) application.

## Install
Nothing special, just take the *www* folder and drop it on an apache server.

## Configure
Create a config file named *spotify.json* in *www/app/config/*.

It must look like this :
```
{
    "client_id" : "Spotify client id",
    "client_secret" : "Spotify client secret"
}
```

You can get *client_id* and *client_secret* from [https://developer.spotify.com](https://developer.spotify.com) by creating an app. Don't forget to add *http://yourhost.com/authentification/callback* as a trusted callback URL.

## Demo
You can take a tour at [http://spotiweek.io](http://spotiweek.io)
