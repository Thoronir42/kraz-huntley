Huntley's treasure hunt hint'n'question UI

# Dev environment
To set up environment, ensure you have a mysql user and an empty database
## Backend stuff
Configure environment by providing local configuration...
```shell script
cp config/config.sample.neon config/config.local.neon
```
...and enter credentials to own database. 

Install dependencies and initialize database by:
```shell script
$ composer install

# reinitialize database
$ php bin\cli db:wipe & php bin\cli db:init -d
```

Note: [as per default with Nette applications](https://doc.nette.org/cs/3.0/application#toc-adresarova-struktura), your web server should have its
document root in the folder `.../www` and should allow **mod_rewrite**.

## Frontend and UI
Install frontend sources and dependencies by:
```shell script
$ yarn
$ gulp copy-vendor & gulp js & gulp sass

# For more tasks, refer to:
$ gulp --tasks
```
