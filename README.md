## SeaUP

Is code-example to make a PHP File upload using Seafile API and a simple JSON configuration 


### Steps

Download this

Make a: composer install 

Edit configuration and test is using upload-test.html

### sea-config.json

This Json configuration is read by the uploader script to understand what Repository and what Auth token you have to make your upload:


    {
      "seafile_host": "https://storage.luckycloud.de",
      "token":"your token",
      "repository_id":"Is on the last part of Url when opening a library: #my-libs/lib/REPOSITORY_ID"
    }

We've tried this successfully using [https://luckycloud.de](Luckycloud.de) but it should work on any other Seafile installation with an open Web API.

### How to obtain a Token

curl -d "username=username@example.com&password=123456" https://yourseafileserver.com/api2/auth-token/

{"token": "24fd3c026886e3121b2ca630805ed425c272cb96"}

Make sure to read [Seafile Web API quicktart](https://manual.seafile.com/develop/web_api_v2.1.html#quick-start) for more information.  Token is only regenerated when you change your password, but it may be updated in the latest versions.


### The examples

**upload-cam** is an example we build to upload an image from a camera directly to a Seafile storage. Note that first we move the file to a folder, you can easily modify this example to your needs.

**upload-sea** is a pure Seafile upload without upload receive hook and it will simply upload this composer.json to the repository ID.
