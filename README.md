# Vanksen Kimple component

Allows you to implement Kimple apps very quickly.

## Installation:

```
composer require vanksen/kimple:dev-master
```

## Usage:

The easiest way to work with the component is to create a file "kimple" at the document root of your project and to insert these lines

```php
#!/usr/bin/env php
<?php
require __DIR__. '/vendor/autoload.php';

use Vanksen\Kimple;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new Kimple\KimpleCommand());
$application->run();
```

Then, just execute the command like this (where "domain.com" is the final url for your app -without www-)

```
php kimple create:app domain.com
```

With this example, your final app will be located in the folder "apps/domain.com". 
To configure the application, just follow the instructions given by the console.


