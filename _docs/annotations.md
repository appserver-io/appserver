---
layout: docs
title: Annotations
position: 60
group: Docs
permalink: /get-started/documentation/annotations.html
---

As one of our main targets is to make configuration as simple as possible we decided to use
annotations wherever possible. As annotations are not supported natively by PHP, we provide
annotation support over our [lang](https://github.com/appserver-io/lang) package.

Beside the usage in our application server components, it'll also possible to extend your 
application with annotations by using the functionality we deliver out-of-the-box.

If you, for example, think about extending the actions of the controller component in your
MVC framework with a @Route annotation, you can do that in the following way

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
PSR and used for [Dependency Injection](#dependency-injection), which will be described below,
are based on that annotation implementation.