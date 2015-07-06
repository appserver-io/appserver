---
layout: docs_2_0
title: Runtime Environment
meta_title: appserver.io runtime environment
meta_description: appserver.io provides a runtime which is system independent and encloses a thread-safe compiled PHP environment.
position: 160
group: Docs
permalink: /get-started/documentation/2.0/runtime-environment.html
---

The appserver.io runtime environment is delivered by the package [runtime](<https://github.com/appserver-io-php/runtime>).
This package  provides a runtime that is system independent and encloses a thread-safe
compiled PHP environment. Besides, the most recent PHP 5.5.x version the package comes with follows installed
extensions:

* [pthreads](http://github.com/appserver-io-php/pthreads)
* [appserver](https://github.com/appserver-io-php/php-ext-appserver) (contains some replacement functions that behave badly in a multithreaded environment)

Additionally, the PECL extensions [XDebug](http://pecl.php.net/package/xdebug) and [ev](http://pecl.php.net/package/ev)
are compiled as shared modules. `XDebug` is necessary to render detailed code coverage reports when
running unit and integration tests. `ev` will be used to integrate a timer service in one of the future
versions.
