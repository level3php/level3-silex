Level3 Silex Provider [![Build Status](https://travis-ci.org/yunait/level3-silex.png?branch=master)](https://travis-ci.org/yunait/level3-silex)
==============================

Provider for using Level3 with Silex framework


Requirements
------------

* PHP 5.3.x
* Unix system
* yunait/level3

Installation
------------

The recommended way to install Level3/Silex is [through composer](http://getcomposer.org).
You can see [the package information on Packagist.](https://packagist.org/packages/yunait/level3-silex)

```JSON
{
    "require": {
        "yunait/level3-silex": "dev-master"
    }
}
```

Parameters
------------

* ```level3.base_uri``` (default '/'): base URI for the API

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