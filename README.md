Level3 Silex Provider [![Build Status](https://travis-ci.org/level3php/silex.png?branch=master)](https://travis-ci.org/level3php/silex)
==============================

Provider for using Level3 with Silex framework


Requirements
------------

* PHP 5.4.x
* Unix system
* level3/level3

Installation
------------

The recommended way to install Level3/Silex is [through composer](http://getcomposer.org).
You can see [the package information on Packagist.](https://packagist.org/packages/level3php/silex)

```JSON
{
    "require": {
        "level3php/silex": "dev-master"
    }
}
```

Parameters
------------

* ```level3.base_uri``` (default '/'): base URI for the API
* ```level3.logger``` (default false):
* ```level3.redis``` (default false):

###Request limiter
* ```level3.enable.limiter``` (default false):
* ```level3.limiter.max_request``` (default false):
* ```level3.limiter.time_period``` (default false):

###Firewall
* ```level3.enable.firewall``` (default false):
* ```level3.firewall.blacklist``` (default false):
* ```level3.firewall.whitelist``` (default false):

###Cross-origin resource sharing:
* ```level3.enable.cors``` (default false):
* ```level3.cors.allowed_origins``` (default '*'):
* ```level3.cors.expose_headers``` (default false):
* ```level3.cors.max_age``` (default false):
* ```level3.cors.allow_credentials``` (default false):
* ```level3.cors.allow_methods``` (default false):
* ```level3.cors.allow_headers``` (default false):

###Other services:
* ```level3.enable.logger``` (default false):
* ```level3.enable.authenticator``` (default false):

Registrating
------------

```PHP
$app->register(new Level3\Silex\ServiceProvider(), array(
    'level3.loader.path' => '/api'
));
```

Tests
-----

Tests are in the `tests` folder.
To run them, you need PHPUnit.
Example:

    $ phpunit --configuration phpunit.xml.dist


License
-------

MIT, see [LICENSE](LICENSE)
