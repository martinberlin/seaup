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