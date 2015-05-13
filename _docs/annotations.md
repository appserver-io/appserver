---
layout: docs
title: Annotations
meta_title: appserver.io annotations
meta_description: Since one of our main goals is to make configuration as simple as possible, we decided to use annotations wherever possible.
position: 60
group: Docs
permalink: /get-started/documentation/annotations.html
---

Since one of our main goals is making the configuration of appserver as simple as possible, we have decided to use
annotations wherever we can. Annotations are not supported natively by PHP. Therefore, we provide
annotation support via the [lang](https://github.com/appserver-io/lang) package.

In addition to the usage of the application server's components, it is possible to extend your
application with annotations by using the functionality we deliver out-of-the-box.

If you, for example, would like to extend the actions of the controller component in your
MVC framework with a @Route annotation, you can do this in the following way.

```php
<?php

namespace Namespace\Module;

use AppserverIo\Appserver\Lang\Reflection\ReflectionClass;
use AppserverIo\Appserver\Lang\Reflection\ReflectionAnnotation;

class Route extends ReflectionAnnotation
{

  /**
   * Returns the value of the name attribute.
   *
   * @return string The annotations name attribute
   */
  public function getPattern()
  {
    return $this->values['pattern'];
  }
}

class IndexController
{

  /**
   * Default action implementation.
   *
   * @return void
   * @Route(pattern="/index/index")
   */
  public function indexAction()
  {
    // do something here
  }
}

// create a reflection class to load the methods annotation
$reflectionClass = new ReflectionClass('IndexController');
$reflectionMethod = $reflectionClass->getMethod('indexAction');
$reflectionAnnotation = $reflectionMethod->getAnnotation('Route');
$pattern = $reflectionAnnotation->newInstance()->getPattern();
```

Most of the annotation implementations provided by our [Enterprise Beans](https://github.com/appserver-io-psr/epb)
PSR and used for [Dependency Injection](#dependency-injection), are based on the [lang](https://github.com/appserver-io/lang) package.
